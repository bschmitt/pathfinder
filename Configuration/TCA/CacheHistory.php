<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$TCA['tx_pathfinder_domain_model_cachehistory'] = array(
	'ctrl'      => $TCA['tx_pathfinder_domain_model_cachehistory']['ctrl'] ,
	'interface' => array(
		'showRecordFieldList' => 'path,hidden'
	) ,
	'types'     => array(
		'0' => array('showitem' => 'path,hidden') ,
	) ,
	'palettes'  => array(
		'1' => array('showitem' => '')
	) ,
	'columns'   => array(
		'uid'       => array(
			'exclude' => 0 ,
			'label'   => 'LLL:EXT:pathfinder/Resources/Private/Language/locallang_db.xml:uid' ,
			'config'  => array(
				'type' => 'input'
			)
		) ,
		'rootpage'  => array(
			'exclude' => 0 ,
			'label'   => 'LLL:EXT:pathfinder/Resources/Private/Language/locallang_db.xml:rootpage' ,
			'config'  => array(
				'type' => 'input'
			)
		) ,
		'pid'       => array(
			'exclude' => 0 ,
			'label'   => 'LLL:EXT:pathfinder/Resources/Private/Language/locallang_db.xml:pid' ,
			'config'  => array(
				'type' => 'input'
			)
		) ,
		'path'      => array(
			'exclude' => 0 ,
			'label'   => 'LLL:EXT:pathfinder/Resources/Private/Language/locallang_db.xml:path' ,
			'config'  => array(
				'type' => 'input' ,
				'size' => '50' ,
				'eval' => 'Tx_Pathfinder_Utility_EvaluatePath,required'
			) ,
		) ,
		'mpvar'     => array(
			'exclude' => 0 ,
			'label'   => 'LLL:EXT:pathfinder/Resources/Private/Language/locallang_db.xml:mpvar' ,
			'config'  => array(
				'type' => 'input'
			) ,
		) ,
		'hidden'    => array(
			'exclude' => 1 ,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden' ,
			'config'  => array(
				'type' => 'check'
			)
		) ,
		'crdate'    => array(
			'exclude' => 0 ,
			'config'  => array(
				'type'   => 'input' ,
				'format' => 'date' ,
				'size'   => 8 ,
				'eval'   => 'date' ,
			)
		) ,
		'starttime' => array(
			'exclude' => 0 ,
			'config'  => array(
				'type'   => 'input' ,
				'format' => 'datetime' ,
				'eval'   => 'datetime' ,
				'size'   => 10
			)
		) ,
		'endtime'   => array(
			'exclude' => 0 ,
			'config'  => array(
				'type'   => 'input' ,
				'format' => 'datetime' ,
				'eval'   => 'datetime' ,
				'size'   => 10
			)
		)
	) ,
);
