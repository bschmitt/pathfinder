<?php

/**
 * Class Tx_Pathfinder_Tests_Unit_Utility_EvaludatePathTest
 *
 * @author Björn Schmitt
 */
class Tx_Pathfinder_Tests_Unit_Utility_EvaluatePathTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{

	/**
	 * @var Tx_Pathfinder_Utility_EvaluatePath
	 */
	protected $obj;

	/**
	 *
	 */
	public function setUp()
	{
		$this->obj = $this->objectManager->get('Tx_Pathfinder_Utility_EvaluatePath');
	}

	public function tearDown()
	{
	}

	/**
	 * @test
	 */
	public function testIsObject()
	{
		$this->assertInstanceOf('Tx_Pathfinder_Utility_EvaluatePath', $this->obj);
	}

	/**
	 * @test
	 */
	public function testUmlautStringToUrl()
	{
		$test = 'björn';
		$expectedResult = 'bjrn/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testUppercaseStringToUrl()
	{
		$test = 'PROdUcts/TEST/';
		$expectedResult = 'products/test/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testSpecialSignsStringToUrl()
	{
		$test = 'products/ & % $ § " \' ! ° ? ß öäü "" [] {} sdf +* ä#';
		$expectedResult = 'products/ss-sdf/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testWhitespacesStringToUrl()
	{
		$test = '  products  dsfdsfdsfd  ';
		$expectedResult = 'products-dsfdsfdsfd/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testWithoutTrailingSlashStringToUrl()
	{
		$test = 'products/test';
		$expectedResult = 'products/test/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testWithBeginningSlashStringToUrl()
	{
		$test = '/products/test/';
		$expectedResult = 'products/test/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testDoubleSlashStringToUrl()
	{
		$test = '//products/test//';
		$expectedResult = 'products/test/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testInfoLinkToUrl()
	{
		$test = 'sales.info';
		$expectedResult = 'sales.info/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function testUnderscoreLinksToUrl()
	{
		$test = 'sales_info';
		$expectedResult = 'sales_info/';

		$result = $this->obj->evaluateFieldValue($test, new stdClass(), new stdClass());
		$this->assertEquals($expectedResult, $result);
	}

}