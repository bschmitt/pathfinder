<?php

$EM_CONF[$_EXTKEY] = array(
	'title'            => 'Pathfinder',
	'description'      => 'Generates URL-Paths for Pages',
	'category'         => '',
	'author'           => 'Björn Schmitt',
	'author_email'     => 'bjoern@bjoerns.biz',
	'author_company'   => '',
	'shy'              => '',
	'priority'         => '',
	'module'           => '',
	'state'            => 'beta',
	'internal'         => '',
	'uploadfolder'     => '0',
	'createDirs'       => '',
	'modify_tables'    => '',
	'clearCacheOnLoad' => 0,
	'lockType'         => '',
	'version'          => '0.4',
	'constraints'      => array(
		'depends'   => array(
			'extbase' => '1.3',
			'fluid'   => '1.3',
			'typo3'   => '4.5',
		),
		'conflicts' => array(
			'realurl'        => '',
			'cooluri'        => '',
			'simulatestatic' => '',
		),
		'suggests'  => array(),
	)
);