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
 * Class Tx_Pathfinder_Utility_Listener
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Utility_Listener
{
	/**
	 * @var $pathfinder Tx_Pathfinder_Router
	 */
	protected $pathfinder;

	public function __construct() {
		$this->pathfinder = t3lib_div::makeInstance('Tx_Pathfinder_Router');
	}

	/**
	 * @param $status
	 * @param $table
	 * @param $id
	 * @param $fieldArray
	 * @param $obj
	 */
	public function processDatamap_afterDatabaseOperations($status , $table , $id , &$fieldArray , &$obj) {
		// update path on page update
		if ($table == 'pages' && (
				array_key_exists('title' , $fieldArray) ||
				array_key_exists('nav_title' , $fieldArray) ||
				array_key_exists('tx_pathfinder_exclude' , $fieldArray)
			)
		) {
			if ($status == 'new') {
				$id = $obj->substNEWwithIDs[$id];
			}
			$this->updatePathCache($id);
		}
		// clean history path on path update
		if ($table == 'tx_pathfinder_domain_model_cache' && array_key_exists('path' , $fieldArray)) {
			$pageId = $obj->checkValue_currentRecord['pid'];
			$this->pathfinder->deleteCacheHistoryRecord($pageId , $fieldArray['path']);
		}
	}

	/**
	 * @param $status
	 * @param $table
	 * @param $id
	 * @param $fieldArray
	 * @param $obj
	 */
	public function processDatamap_postProcessFieldArray($status , $table , $id , &$fieldArray , &$obj) {
		// update path on page update
		if ($table == 'tx_pathfinder_domain_model_cachehistory' && array_key_exists('path' , $fieldArray)) {
			if ($status == 'new') {
				$id = $obj->substNEWwithIDs[$id];
			}
			$fieldArray['path'] = $this->updateHistoryCache($id , $fieldArray['path']);
		}
	}

	/**
	 * @param $obj
	 */
	public function processCmdmap_afterFinish(t3lib_TCEmain &$obj) {
		if (array_key_exists('pages' , $obj->cmdmap)) {
			foreach ($obj->cmdmap['pages'] as $pageId => $action) {
				if (in_array(key($action) , array('move'))) {
					$this->updatePathCache($pageId);
				}
			}
		}
	}

	/**
	 * @param $pageId
	 */
	public function updatePathCache($pageId) {
		$this->pathfinder->updateAndReturnPath($pageId);
		$this->pathfinder->updateDependencies($pageId);
	}

	/**
	 * @param $recordId
	 * @param $path
	 * @return string
	 */
	protected function updateHistoryCache($recordId , $path) {
		$cache = $this->pathfinder->getCacheHistoryByPath($path , TRUE);
		if ($cache !== FALSE && $cache['uid'] != $recordId) {
			$path .= 'exists-in-page-' . $cache['pid'];
		}
		return $path;
	}

	/**
	 * @param $recordId
	 * @param $path
	 * @return string
	 */
	protected function cleanHistoryCache($recordId , $path) {
		$cache = $this->pathfinder->getCacheHistoryByPath($path , TRUE);
		if ($cache !== FALSE && $cache['uid'] != $recordId) {
			$path .= 'exists-in-page-' . $cache['pid'];
		}
		return $path;
	}
}
