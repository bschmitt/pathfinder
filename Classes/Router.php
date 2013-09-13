<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Björn Schmitt <bjoern@bjoerns.biz>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * TYPO3 Pathfinder
 * Converts TYPO3 query strings to human readable paths and the other way around
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Router
{
	/**
	 * @var int
	 */
	protected $rootPage = 1;

	/**
	 * Default language code
	 *
	 * @var string
	 */
	protected $defaultLanguageCode = 'en';

	/**
	 * Language code -> id mapping
	 *
	 * @var array
	 */
	protected $languages = array();

	/**
	 * Config for fixed paths which will be used instead of get vars
	 *
	 * @var array
	 */
	protected $replaceGetVars = array();

	/**
	 * Init and settings
	 */
	public function __construct() {
		// settings
		if (file_exists(PATH_typo3conf . 'ext/pathfinder/settings.php')) {
			$settings = include(PATH_typo3conf . 'ext/pathfinder/settings.php');
			if (isset($settings['rootPage'])) {
				$this->rootPage = $settings['rootPage'];
			}
			if (isset($settings['defaultLanguageCode'])) {
				$this->defaultLanguageCode = $settings['defaultLanguageCode'];
			}
			if (isset($settings['languageMapping'])) {
				$this->languages = $settings['languageMapping'];
			} else {
				$this->languages = $this->getLanguageMapping();
			}
		}
		// fixed get vars
		if (file_exists(PATH_typo3conf . 'ext/pathfinder/fixed.php')) {
			$this->replaceGetVars = include(PATH_typo3conf . 'ext/pathfinder/fixed.php');
		}
	}

	/**
	 * @return mixed
	 */
	protected function getSettings() {
		return $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pathfinder'];
	}

	/**
	 * Main method for converting typo query strings to a human readable paths
	 *
	 * @param $params
	 * @param $ref
	 */
	public function toUrl(&$params , $ref) {
		// extract page id from params
		$pageId = $params['args']['page']['uid'];

		// extract query from params and get array
		$queryParams = t3lib_div::explodeUrl2Array($params['args']['addParams'] , TRUE);
		// add typeNum to array
		if ($params['typeNum'] != '') {
			$queryParams['type'] = $params['typeNum'];
		}

		// set language
		$lang = $this->getLangCode();
		if (array_key_exists('L' , $queryParams)) {
			$lang = $this->getLangCodeById($queryParams['L']);
			unset($queryParams['L']);
		}

		// convert alias to page id
		$pageId = $this->resolveAlias($pageId);
		// convert shortcut to page id
		$pageId = $this->resolveShortcuts($pageId , $mpvar);
		//$mpvar = $paramKeyValues['MP'];
		//unset($paramKeyValues['MP']);

		// build query string
		$queryString = '';
		if (!empty($queryParams)) {
			$queryString = '?' . substr(t3lib_div::implodeArrayForUrl('' , $queryParams) , 1);
		}
		// get adjusted query string in case of fixed page paths
		$queryString = $this->replaceQueryParamsWithFixedPaths($pageId , $queryString);

		// build url with absolute prefix
		$url = $this->getPathFromPageId($pageId) . $queryString;
		$params['LD']['totalURL'] = '/' . $lang . (($url != '/') ? '/' . $url : $url);
	}

	/**
	 * Main method for converting paths to typo3 query strings
	 *
	 * @param $params
	 * @param $ref
	 */
	public function toId(&$params , $ref) {
		$url = $params['pObj']->siteScript;
		// get fragments out of url
		$fragments = $this->getUrlFragments($url);
		// merge query string into params
		$this->mergeFragmentsWithGetVars($params , $fragments);
		if (!empty($fragments['path'])) {
			// lookup in cache
			if ($cache = $this->getCacheByPath($fragments['path'])) {
				$params['pObj']->id = $cache['pid'];
			} // lookup in history cache
			elseif ($cacheHistory = $this->getCacheHistoryByPath($fragments['path'])) {
				// redirect to current page
				$page = $this->getCacheById($cacheHistory['pid']);
				$url = $fragments['lang'] . '/' . $page['path'];
				if (!empty($fragments['query'])) {
					$url .= '?' . $fragments['query'];
				}
				t3lib_utility_http::redirect($url , t3lib_utility_http::HTTP_STATUS_301);
			} // check for static paths and lookup in cache
			elseif ($newPath = $this->checkForFixedPathsAndRemoveThem($fragments['path'])) {
				$tmp = explode('?' , $newPath);
				if ($cache = $this->getCacheByPath($tmp[0])) {
					$params['pObj']->id = $cache['pid'];
					$getVars = t3lib_div::explodeUrl2Array($tmp[1] , TRUE);
					$params['pObj']->mergingWithGetVars($getVars);
				} else {
					// 404
					$this->setMissingPath($url);
					$params['pObj']->pageNotFoundAndExit("Path $newPath was not found");
				}
			} else {
				// 404
				$this->setMissingPath($url);
				$params['pObj']->pageNotFoundAndExit("Path " . $fragments['path'] . " was not found");
			}
		}
	}

	/**
	 * Encode custom string to a valid url
	 *
	 * @param string $str
	 * @param string $delimiter
	 * @return mixed
	 */
	public function encodeStringToUrlSegment($str , $delimiter = '-') {
		$clean = iconv('UTF-8' , 'ASCII//TRANSLIT' , trim($str));
		$clean = preg_replace("/[^a-zA-Z0-9_\.|+ -]/" , '' , $clean);
		$clean = strtolower(trim($clean , '-'));
		$clean = preg_replace("/[|+ -]+/" , $delimiter , $clean);
		$clean = trim($clean , $delimiter);
		return $clean;
	}

	/**
	 * Converts url to fragments array
	 *
	 * @param string $url
	 * @return array
	 */
	protected function getUrlFragments($url) {
		$tmp = explode('?' , $url);
		$lang = $path = '';
		if (preg_match('~^(\w{2})/(.*)~' , $tmp[0] , $match)) {
			$lang = $match[1];
			$path = $match[2];
		}
		return array(
			'lang'  => $lang ,
			'path'  => $path ,
			'query' => array_key_exists(1 , $tmp) ? $tmp[1] : ''
		);
	}

	/**
	 * Merges fragments get vars with the ones into the $params object
	 *
	 * @param mixed $params
	 * @param array $fragments
	 */
	protected function mergeFragmentsWithGetVars(&$params , $fragments) {
		$getVars = array();
		if (!empty($fragments['query'])) {
			parse_str($fragments['query'] , $getVars);
		}
		if (!empty($fragments['lang'])) {
			$getVars['L'] = $this->getLangIdByCode($fragments['lang']);
		}
		$params['pObj']->mergingWithGetVars($getVars);
	}

	/**
	 * Replaces the query string with fixed paths
	 * Used by *toUrl*
	 *
	 * @param $pageId
	 * @param $queryString
	 * @return mixed
	 */
	protected function replaceQueryParamsWithFixedPaths($pageId , $queryString) {
		if (is_numeric($pageId)) {
			// proceed only if replace config is existing
			if (array_key_exists($pageId , $this->replaceGetVars)) {
				$paths = array();
				foreach ($this->replaceGetVars[$pageId] as $part) {
					$param = $part;
					if (is_array($part)) {
						$param = $part['param'];
					}
					$rule = '~' . $this->addRegexSlashes($param) . '=([^&]*)~';
					preg_match($rule , $queryString , $matches);
					// existing in query string
					if ($matches) {
						$path = $matches[1];
						// value map
						if (is_array($part) && isset($part['map'])) {
							if (array_key_exists($path , $part['map'])) {
								$path = $part['map'][$path];
							}
						}
						// query map
						if (is_array($part) && isset($part['sql']) && is_numeric($path)) {
							$lookupId = $path;
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
								$part['sql']['field'] ,
								$part['sql']['from'] ,
								sprintf($part['sql']['where'] , $lookupId)
							);
							$path = $this->encodeStringToUrlSegment($res[$part['sql']['field']]);
							// cache in meta cache for decoding
							$this->setMeta('fixed-sql-decode' , $path , array($lookupId));
						}
						// add to path in case it should be shown
						if (!isset($part['hide'])) {
							$paths[] = $path;
						}
						// remove from query string
						$rule2 = '~[\&|\?]?' . $this->addRegexSlashes($matches[0]) . '~';
						$queryString = preg_replace($rule2 , '' , $queryString);
					}
				}
				// build query string
				if (substr($queryString , 0 , 1) == '&') {
					$queryString = '?' . substr($queryString , 1 , strlen($queryString) - 1);
				}
				// merge path and query string
				if (!empty($paths)) {
					$queryString = implode('/' , $paths) . '/' . $queryString;
				}
			}
		}
		return $queryString;
	}

	/**
	 * In case of used fixed paths parts they will be converted into the original query string
	 * Used by *toId*
	 *
	 * @param $url
	 * @return bool|string
	 */
	protected function checkForFixedPathsAndRemoveThem($url) {
		$parts = explode('/' , $url);
		$tempPath = $urlPath = '';
		$potentialFixedParmsPageId = NULL;
		for ($i = 0; $i < count($parts); $i++) {
			$tempPath .= $parts[$i] . '/';
			if ($cache = $this->getCacheByPath($tempPath)) {
				$potentialFixedParmsPageId = array_key_exists($cache['pid'] , $this->replaceGetVars) ? $cache['pid'] : $potentialFixedParmsPageId;
			} // if last page id has config
			elseif (is_numeric($potentialFixedParmsPageId)) {
				$conf = $this->replaceGetVars[$potentialFixedParmsPageId];
				$getVars = array();
				for ($j = 0; $j < count($conf); $j++) {
					if (!empty($parts[$i])) {
						$key = $conf[$j];
						$value = $parts[$i];
						// advanced fixed options
						if (is_array($conf[$j])) {
							$key = $conf[$j]['param'];
							// value map
							if (isset($conf[$j]['map'])) {
								$mapValue = array_search($value , $conf[$j]['map']);
								if ($mapValue !== FALSE) {
									$value = $mapValue;
								}
							}
							// sql map
							if (isset($conf[$j]['sql'])) {
								if ($data = $this->getMetaByHash('fixed-sql-decode' , $value)) {
									$value = $data[0];
								}
							}
						}
						$getVars[$key] = $value;
					}
					$i++;
				}
				return $urlPath . '?' . substr(t3lib_div::implodeArrayForUrl('' , $getVars) , 1);
			}
			$urlPath .= $parts[$i] . '/';
		}
		return FALSE;
	}

	/**
	 * @param int $id
	 * @return string
	 */
	protected function getPathFromPageId($id) {
		if ($cache = $this->getCacheById($id)) {
			return $cache['path'];
		}
		return $this->updateAndReturnPath($id);
	}

	/**
	 * @param int    $pageId
	 * @param string $mpvar
	 * @return string
	 */
	public function updateAndReturnPath($pageId , $mpvar = '') {
		/* @var t3lib_pageSelect $sys_page */
		$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = $sys_page->getRootLine($pageId , $mpvar);
		$parts = $dependencies = array();
		$count = count($rootLine) - 1;
		for ($i = 1; $i <= $count; $i++) {
			// get parts in case path is not excluded and not on 1st level
			if ($rootLine[$i]['tx_pathfinder_exclude'] != 1 || $count == 1) {
				$value = (!empty($rootLine[$i]['nav_title'])) ? $rootLine[$i]['nav_title'] : $rootLine[$i]['title'];
				$parts[] = $this->encodeStringToUrlSegment($value);
			}
			// set dependency only for parent nodes
			if ($i != $count) {
				$dependencies[] = $rootLine[$i]['uid'];
			}
		}
		$path = implode('/' , $parts) . '/';
		$this->setCache($pageId , $path , $dependencies);
		return $path;
	}

	/**
	 * @param int $pageId
	 */
	public function updateDependencies($pageId) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*' ,
			'tx_pathfinder_domain_model_cache' ,
			"dependencies LIKE '%," . $pageId . ",%' AND deleted = 0"
		);
		while ($row = mysql_fetch_array($res)) {
			$this->updateAndReturnPath($row['pid']);
		}
	}

	/**
	 * @param int    $pageId
	 * @param string $path
	 * @param array  $dependencies
	 */
	protected function setCache($pageId , $path , $dependencies) {
		if ($old = $this->getCacheById($pageId , TRUE)) {
			// copy to history
			if (!$his = $this->getCacheHistoryByPath($path , TRUE)) {
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pathfinder_domain_model_cachehistory' , array(
					'pid'              => $old['pid'] ,
					'path'             => $old['path'] ,
					'sys_language_uid' => $old['sys_language_uid'] ,
					'rootpage'         => $old['rootpage'] ,
					'mpvar'            => $old['mpvar']
				));
			}
			// update
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_pathfinder_domain_model_cache' , 'pid = ' . $pageId , array(
				'pid'          => $pageId ,
				'path'         => $path ,
				'rootpage'     => $this->rootPage ,
				'dependencies' => ',' . implode(',' , $dependencies) . ','
			));
			// clean history cache with current path
			$this->deleteCacheHistoryRecord($pageId , $path);
		} else {
			// remove deleted=1 records cause of unique index
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pathfinder_domain_model_cache' , "pid = '$pageId'");
			// insert
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pathfinder_domain_model_cache' , array(
				'pid'          => $pageId ,
				'path'         => $path ,
				'rootpage'     => $this->rootPage ,
				'deleted'      => '0' ,
				'hidden'       => '0' ,
				'dependencies' => ',' . implode(',' , $dependencies) . ','
			));
		}
	}

	/**
	 * @param int    $path
	 * @param string $mpvar
	 * @return bool
	 */
	protected function getCacheByPath($path , $mpvar = '') {
		if ($this->isBackend()) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cache c' ,
				"c.path = '$path' AND c.deleted = 0 AND c.hidden = 0");
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cache c, pages p' ,
				"p.uid = c.pid AND p.deleted = 0 AND p.hidden = 0 AND c.path = '$path' AND c.deleted = 0 AND c.hidden = 0");
		}
		if ($res) {
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		return FALSE;
	}

	/**
	 * @param int    $pageId
	 * @param string $mpvar
	 * @return bool
	 */
	protected function getCacheHistoryById($pageId , $mpvar = '') {
		if ($this->isBackend()) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cachehistory c' ,
				"c.pid = '$pageId' AND c.deleted = 0 AND c.hidden = 0");
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cachehistory c, pages p' ,
				"p.uid = c.pid AND p.deleted = 0 AND p.hidden = 0 AND c.pid = '$pageId' AND c.deleted = 0 AND c.hidden = 0");
		}
		if ($res) {
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		return FALSE;
	}

	/**
	 * @param string $path
	 * @param bool   $backend
	 * @param string $mpvar
	 * @return bool
	 */
	public function getCacheHistoryByPath($path , $backend = FALSE , $mpvar = '') {
		if ($this->isBackend() || $backend) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cachehistory c' ,
				"c.path = '$path' AND c.deleted = 0 AND c.hidden = 0");
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cachehistory c, pages p' ,
				"p.uid = c.pid AND p.deleted = 0 AND p.hidden = 0 AND c.path = '$path' AND c.deleted = 0 AND c.hidden = 0");
		}
		if ($res) {
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		return FALSE;
	}

	/**
	 * @param  int   $pageId
	 * @param string $path
	 * @param string $mpvar
	 * @return mixed
	 */
	public function deleteCacheHistoryRecord($pageId , $path , $mpvar = '') {
		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_pathfinder_domain_model_cachehistory' , "pid = '$pageId' and path = '$path'" , array(
			'deleted' => 1
		));
	}

	/**
	 * @param int  $pageId
	 * @param bool $backend
	 * @return bool
	 */
	protected function getCacheById($pageId , $backend = FALSE) {
		if ($this->isBackend() || $backend) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cache c' ,
				"c.pid = '$pageId' AND c.deleted = 0 AND c.hidden = 0");
		} else {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('c.*' , 'tx_pathfinder_domain_model_cache c, pages p' ,
				"p.uid = c.pid AND p.deleted = 0 AND p.hidden = 0 AND c.pid = '$pageId' AND c.deleted = 0 AND c.hidden = 0");
		}
		if ($res) {
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
		return FALSE;
	}

	/**
	 * @param string $ns namespace
	 * @param string $hash
	 * @return bool
	 */
	protected function getMetaByHash($ns , $hash) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('value' , 'tx_pathfinder_domain_model_meta' , "ns = '$ns' and hash = '$hash'");
		if ($res) {
			return json_decode($res['value']);
		}
		return FALSE;
	}

	/**
	 * @param string $ns namespace
	 * @param string $hash
	 * @param mixed  $value
	 * @return mixed
	 */
	protected function setMeta($ns , $hash , array $value) {
		$exists = $this->getMetaByHash($ns , $hash);
		if ($exists === $value) {
			return TRUE;
		}
		if ($exists !== FALSE) {
			return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_pathfinder_domain_model_meta' , "ns = '$ns' and hash = '$hash'" , array(
				'value' => json_encode($value)
			));
		} else {
			return $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pathfinder_domain_model_meta' , array(
				'ns'    => $ns ,
				'hash'  => $hash ,
				'value' => json_encode($value)
			));
		}
	}

	/**
	 * @param $path
	 * @return bool
	 */
	protected function setMissingPath($path) {
		$item = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid,counter' , 'tx_pathfinder_domain_model_404' , "path = '$path'");
		if ($item !== FALSE) {
			return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_pathfinder_domain_model_404' , "uid = '" . $item['uid'] . "'" , array(
				'counter' => $item['counter'] + 1
			));
		} else {
			return $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_pathfinder_domain_model_404' , array(
				'path' => $path ,
			));
		}
	}

	/**
	 * @param string $ns namespace
	 * @param string $hash
	 * @return mixed
	 */
	public function removeMetaByHash($ns , $hash) {
		return $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pathfinder_domain_model_meta' , "ns = '$ns' and hash = '$hash'");
	}

	/**
	 * @return array
	 */
	protected function getLanguageMapping() {
		$mapping = array($this->defaultLanguageCode => 0);
		$languages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*' ,
			'sys_language' ,
			'hidden = 0'
		);
		foreach ($languages as $language) {
			$mapping[$language['flag']] = $language['uid'];
		}
		return $mapping;
	}

	/**
	 * @return mixed
	 */
	protected function getLangCode() {
		return $GLOBALS['TSFE']->config['config']['language'];
	}

	/**
	 * @param string $code
	 * @return int
	 */
	protected function getLangIdByCode($code) {
		if (array_key_exists($code , $this->languages)) {
			return $this->languages[$code];
		}
		return 0;
	}

	/**
	 * @param $id
	 * @return mixed|string
	 */
	protected function getLangCodeById($id) {
		if (in_array($id , $this->languages)) {
			return array_search($id , $this->languages);
		}
		return 'en';
	}

	/**
	 * 404 action
	 */
	protected function pageNotFound() {
		$GLOBALS['TSFE']->pageNotFoundAndExit();
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function addTrailingSlash($url) {
		if (substr($url , -1) != '/') {
			$url .= '/';
		}
		return $url;
	}

	/**
	 * If page id is not numeric, try to resolve it from alias.
	 *
	 * @param int $pageId
	 * @return int
	 */
	protected function resolveAlias($pageId) {
		if (!is_numeric($pageId)) {
			$pageId = $GLOBALS['TSFE']->sys_page->getPageIdFromAlias($pageId);
		}
		return $pageId;
	}

	/**
	 * Checks if the page should be excluded from processing.
	 *
	 * @param int $pageId
	 * @return boolean
	 */
	protected function isExcludedPage($pageId) {
		return $this->conf['excludePageIds'] && t3lib_div::inList($this->conf['excludePageIds'] , $pageId);
	}

	/**
	 * Resolves shortcuts if necessary and returns the final destination page id.
	 *
	 * @param int    $pageId
	 * @param string $mpvar
	 * @return bool
	 */
	protected function resolveShortcuts($pageId , &$mpvar) {
		$disableGroupAccessCheck = ($GLOBALS['TSFE']->config['config']['typolinkLinkAccessRestrictedPages'] ? TRUE : FALSE);
		$loopCount = 20; // Max 20 shortcuts, to prevent an endless loop
		while ($pageId > 0 && $loopCount > 0) {
			$loopCount--;

			$page = $GLOBALS['TSFE']->sys_page->getPage($pageId , $disableGroupAccessCheck);
			if (!$page) {
				$pageId = FALSE;
				break;
			}

			if (!$this->conf['dontResolveShortcuts'] && $page['doktype'] == 4) {
				// Shortcut
				$pageId = $this->resolveShortcut($page , $disableGroupAccessCheck , array() , $mpvar);
			} else {
				$pageId = $page['uid'];
				break;
			}
		}
		return $pageId;
	}

	/**
	 * Resolves shortcut to the page
	 *
	 * @param       $page
	 * @param       $disableGroupAccessCheck
	 * @param array $log
	 * @param null  $mpvar
	 * @return int
	 */
	protected function resolveShortcut($page , $disableGroupAccessCheck , $log = array() , &$mpvar = NULL) {
		if (isset($log[$page['uid']])) {
			// loop detected!
			return $page['uid'];
		}
		$log[$page['uid']] = '';
		$pageid = $page['uid'];
		if ($page['shortcut_mode'] == 0) {
			// Jumps to a certain page
			if ($page['shortcut']) {
				$pageid = intval($page['shortcut']);
				$page = $GLOBALS['TSFE']->sys_page->getPage($pageid , $disableGroupAccessCheck);
				if ($page && $page['doktype'] == 4) {
					$mpvar = '';
					$pageid = $this->resolveShortcut($page , $disableGroupAccessCheck , $log , $mpvar);
				}
			}
		} elseif ($page['shortcut_mode'] == 1) {
			// Jumps to the first subpage
			$rows = $GLOBALS['TSFE']->sys_page->getMenu($page['uid']);
			if (count($rows) > 0) {
				reset($rows);
				$row = current($rows);
				$pageid = ($row['doktype'] == 4 ? $this->resolveShortcut($row , $disableGroupAccessCheck , $log , $mpvar) : $row['uid']);
			}

			if (isset($row['_MP_PARAM'])) {
				if ($mpvar) {
					$mpvar .= ',';
				}

				$mpvar .= $row['_MP_PARAM'];
			}
		} elseif ($page['shortcut_mode'] == 3) {
			// Jumps to the parent page
			$page = $GLOBALS['TSFE']->sys_page->getPage($page['pid'] , $disableGroupAccessCheck);
			$pageid = $page['uid'];
			if ($page && $page['doktype'] == 4) {
				$pageid = $this->resolveShortcut($page , $disableGroupAccessCheck , $log , $mpvar);
			}
		}
		return $pageid;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function addRegexSlashes($string) {
		return strtr($string , array('.' => '\.' , '/' => '\/' , '\\' => '\\\\' , '+' => '\+' , '[' => '\[' , ']' => '\]'));
	}

	/**
	 * @return bool
	 */
	protected function isBackend() {
		return $GLOBALS['TSFE']->beUserLogin == 1;
	}
}