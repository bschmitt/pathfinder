<?php

// page id to path
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['pathfinder'] = 'EXT:pathfinder/Classes/Router.php:&Tx_Pathfinder_Router->toUrl';

// path to page id
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['pathfinder'] = 'EXT:pathfinder/Classes/Router.php:&Tx_Pathfinder_Router->toId';

// update path on page update
// save page
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:pathfinder/Classes/Utility/Listener.php:Tx_Pathfinder_Utility_Listener';
// drag drop
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:pathfinder/Classes/Utility/Listener.php:Tx_Pathfinder_Utility_Listener';

// adjust path to a valid url
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['Tx_Pathfinder_Utility_EvaluatePath'] = 'EXT:pathfinder/Classes/Utility/EvaluatePath.php';

// modifies the record list
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] = 'EXT:pathfinder/Classes/Utility/RecordList.php:Tx_Pathfinder_Utility_RecordList';

// add pathfinder fields to rootline
$TYPO3_CONF_VARS['FE']['addRootLineFields'] .= ',tx_pathfinder_exclude';
