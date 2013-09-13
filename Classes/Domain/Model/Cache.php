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
 * Class Tx_Pathfinder_Domain_Model_Cache
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Domain_Model_Cache
{

	/**
	 * @var int
	 */
	protected $uid;

	/**
	 * @var int
	 */
	protected $rootpage;

	/**
	 * @var string
	 */
	protected $mpvar;

	/**
	 * @var int
	 */
	protected $pid;

	/**
	 * @var int
	 */
	protected $sysLanguageUid;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $dependencies;

	/**
	 * @param string $dependencies
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setDependencies($dependencies)
	{
		$this->dependencies = $dependencies;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @param string $mpvar
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setMpvar($mpvar)
	{
		$this->mpvar = $mpvar;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMpvar()
	{
		return $this->mpvar;
	}

	/**
	 * @param string $path
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param int $pid
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setPid($pid)
	{
		$this->pid = $pid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPid()
	{
		return $this->pid;
	}

	/**
	 * @param int $rootpage
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setRootpageId($rootpage)
	{
		$this->rootpage = $rootpage;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getRootpage()
	{
		return $this->rootpage;
	}

	/**
	 * @param int $sysLanguageUid
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setSysLanguageUid($sysLanguageUid)
	{
		$this->sysLanguageUid = $sysLanguageUid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSysLanguageUid()
	{
		return $this->sysLanguageUid;
	}

	/**
	 * @param int $uid
	 * @return Tx_Pathfinder_Domain_Model_Cache
	 */
	public function setUid($uid)
	{
		$this->uid = $uid;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUid()
	{
		return $this->uid;
	}

}
