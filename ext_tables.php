<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_pathfinder_domain_model_cache'] = array(
	'ctrl' => array(
		'title'             => 'URL Path' ,
		'label'             => 'path' ,
		'searchFields'      => 'path' ,
		'hideAtCopy'        => TRUE ,
		'delete'            => 'deleted' ,
		'enablecolumns'     => array(
			'disabled'  => 'hidden' ,
			'starttime' => 'starttime' ,
			'endtime'   => 'endtime' ,
			'hidden'    => 'hidden'
		) ,
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/cache.png' ,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Cache.php'

	)
);

$TCA['tx_pathfinder_domain_model_cachehistory'] = array(
	'ctrl' => array(
		'title'             => 'URL History Paths' ,
		'label'             => 'path' ,
		'searchFields'      => 'path' ,
		'hideAtCopy'        => TRUE ,
		'delete'            => 'deleted' ,
		'enablecolumns'     => array(
			'disabled'  => 'hidden' ,
			'starttime' => 'starttime' ,
			'endtime'   => 'endtime' ,
			'hidden'    => 'hidden'
		) ,
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/cachehistory.png' ,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/CacheHistory.php'
	)
);
t3lib_extMgm::allowTableOnStandardPages('tx_pathfinder_domain_model_cachehistory');
t3lib_extMgm::allowTableOnStandardPages('tx_pathfinder_domain_model_cache');

if (TYPO3_MODE === 'BE') {
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY ,
		'tools' , // submodule
		'pathfinder' , // key
		'' , // position
		array(
			'Backend' => 'index,missing,clearMissing'
		) ,
		array(
			'access' => 'user,group' ,
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.png' ,
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml' ,
		)
	);
}

t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages' , array(
	'tx_pathfinder_exclude' => array(
		'exclude' => 0 ,
		'label'   => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:excludepath' ,
		'config'  => array(
			'type'  => 'check' ,
			'items' => array(
				'1' => array(
					'0' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:exclude'
				) ,
			) ,
		)
	)
) , 1);
t3lib_extMgm::addFieldsToPalette('pages' , 'miscellaneous' , 'tx_pathfinder_exclude' , 'after:no_search');