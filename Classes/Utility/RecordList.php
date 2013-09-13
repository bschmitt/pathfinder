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

require_once(PATH_typo3 . 'interfaces/interface.localrecordlist_actionsHook.php');

/**
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Utility_RecordList implements localRecordList_actionsHook
{
	/**
	 * @return string
	 */
	protected function getLanguageCode() {
		if (file_exists(PATH_typo3conf . 'ext/pathfinder/settings.php')) {
			$settings = include(PATH_typo3conf . 'ext/pathfinder/settings.php');
			if (isset($settings['defaultLanguageCode'])) {
				return $settings['defaultLanguageCode'] . '/';
			}
		}
		return '';
	}

	/**
	 * modifies Web>List control icons of a displayed row
	 *
	 * @param    string        the current database table
	 * @param    array         the current record row
	 * @param    array         the default control-icons to get modified
	 * @param    object        Instance of calling object
	 * @return    array        the modified control-icons
	 */
	public function makeControl($table , $row , $cells , &$parentObject) {
		if ($table == 'tx_pathfinder_domain_model_cache' || $table == 'tx_pathfinder_domain_model_cachehistory') {
			$cells['view'] .= '<a href="#" onclick="window.open(\'/' . $this->getLanguageCode() . $row['path'] . '\')" title="' .
				$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showPage' , TRUE) . '">' .
				t3lib_iconWorks::getSpriteIcon('actions-document-view') . '</a>';
		}
		return $cells;
	}

	/**
	 * modifies Web>List clip icons (copy, cut, paste, etc.) of a displayed row
	 *
	 * @param    string        the current database table
	 * @param    array         the current record row
	 * @param    array         the default clip-icons to get modified
	 * @param    object        Instance of calling object
	 * @return    array        the modified clip-icons
	 */
	public function makeClip($table , $row , $cells , &$parentObject) {
		return $cells;
	}

	/**
	 * modifies Web>List header row columns/cells
	 *
	 * @param    string        the current database table
	 * @param    array         Array of the currently displayed uids of the table
	 * @param    array         An array of rendered cells/columns
	 * @param    object        Instance of calling (parent) object
	 * @return    array        Array of modified cells/columns
	 */
	public function renderListHeader($table , $currentIdList , $headerColumns , &$parentObject) {
		return $headerColumns;
	}

	/**
	 * modifies Web>List header row clipboard/action icons
	 *
	 * @param    string        the current database table
	 * @param    array         Array of the currently displayed uids of the table
	 * @param    array         An array of the current clipboard/action icons
	 * @param    object        Instance of calling (parent) object
	 * @return    array        Array of modified clipboard/action icons
	 */
	public function renderListHeaderActions($table , $currentIdList , $cells , &$parentObject) {
		return $cells;
	}
}
