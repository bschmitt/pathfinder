<?php

/**
 * Class Tx_Pathfinder_Tests_Unit_Utility_ListenerTest
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Tests_Unit_Utility_ListenerTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{

	/**
	 * @var Tx_Pathfinder_Utility_Listener
	 */
	protected $obj;

	/**
	 *
	 */
	public function setUp()
	{
		$this->obj = $this->objectManager->get('Tx_Pathfinder_Utility_Listener');
	}

	public function tearDown()
	{
	}

	/**
	 * @test
	 */
	public function testIsObject()
	{
		$this->assertInstanceOf('Tx_Pathfinder_Utility_Listener', $this->obj);
	}

}