<?php

/**
 * Class Tx_Pathfinder_Tests_Unit_RouterTest
 */
class Tx_Pathfinder_Tests_Unit_RouterTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{

	/**
	 * @var Tx_Pathfinder_Router
	 */
	protected $obj;

	/**
	 * @var int
	 */
	protected $testPageId = 740;

	/**
	 * @var array
	 */
	protected $fixtures = array(
		'args'    => array(
			'page'      => array(
				'uid' => 740
			),
			'addParams' => 'test=1',

		),
		'typeNum' => 1,
		'LD'      => array()
	);

	/**
	 *
	 */
	public function setUp()
	{
		$this->obj = $this->objectManager->get('Tx_Pathfinder_Router');
	}

	public function tearDown()
	{
	}

	/**
	 * @test
	 */
	public function testIsObject()
	{
		$this->assertInstanceOf('Tx_Pathfinder_Router', $this->obj);
	}

	/**
	 * @test
	 */
	public function testToUrl()
	{

	}

	/**
	 * @test
	 */
	public function testToId()
	{

	}

}