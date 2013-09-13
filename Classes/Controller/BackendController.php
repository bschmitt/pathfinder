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
 * Class Tx_Pathfinder_Controller_BackendController
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Controller_BackendController extends Tx_Extbase_MVC_Controller_ActionController
{

	/**
	 * @param Tx_Pathfinder_Domain_Model_Search $search
	 * @dontvalidate $search
	 */
	public function indexAction($search = NULL) {
		if (is_null($search)) {
			$search = t3lib_div::makeInstance('Tx_Pathfinder_Domain_Model_Search');
		}
		$this->view->assign('search' , $search);
		$query = $search->getQuery();
		if (!empty($query)) {
			// path cache
			$this->view->assign('cache' , $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*' ,
					'tx_pathfinder_domain_model_cache' ,
					"path like '%$query%' and deleted=0" ,
					'' ,
					'path asc'
				)
			);
			// history cache
			$this->view->assign('history' , $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*' ,
					'tx_pathfinder_domain_model_cachehistory' ,
					"path like '%$query%' and deleted=0" ,
					'' ,
					'path asc'
				)
			);
		}
	}

	/**
	 * Missing paths
	 */
	public function missingAction() {
		// 404s
		$this->view->assign('paths' , $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*' ,
				'tx_pathfinder_domain_model_404' ,
				'' ,
				'' ,
				'counter desc'
			)
		);
		$this->view->assign('baseurl' , 'http://' . $_SERVER['SERVER_NAME'] . '/');
	}

	/**
	 * Missing paths
	 */
	public function clearMissingAction() {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pathfinder_domain_model_404' , "");
		$this->redirect('missing');
	}
}
