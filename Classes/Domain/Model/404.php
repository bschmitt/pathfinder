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
 * Class Tx_Pathfinder_Domain_Model_404
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Domain_Model_404 extends Tx_Extbase_DomainObject_AbstractEntity
{

	/**
	 * @var int
	 */
	protected $uid;

	/**
	 * @var int
	 */
	protected $counter;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @param int $counter
	 * @return Tx_Pathfinder_Domain_Model_404
	 */
	public function setCounter($counter) {
		$this->counter = $counter;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getCounter() {
		return $this->counter;
	}

	/**
	 * @param string $path
	 * @return Tx_Pathfinder_Domain_Model_404
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param int $uid
	 * @return Tx_Pathfinder_Domain_Model_404
	 */
	public function setUid($uid) {
		$this->uid = $uid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}
}
