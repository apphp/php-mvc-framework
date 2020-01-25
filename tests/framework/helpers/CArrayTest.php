<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use stdClass;
use CArray;

require_once('tests/autoload.php');

class CArrayTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}
	
	/**
	 * Test for CArray::flipByField
	 */
	public function testFlipByField(): void
	{
		$array1 = array(
			'0' => array('field1' => '11', 'field2' => '12', 'field3' => '13'),
			'1' => array('field1' => '21', 'field2' => '22', 'field3' => '23'),
		);
		
		$array2 = array(
			'11' => array('field1' => '11', 'field2' => '12', 'field3' => '13'),
			'21' => array('field1' => '21', 'field2' => '22', 'field3' => '23'),
		);
		
		self::assertEquals(array(), CArray::flipByField($array1));
		self::assertEquals($array2, CArray::flipByField($array1, 'field1'));
	}
	
	/**
	 * Test for CArray::uniqueByField
	 */
	public function testUniqueByField(): void
	{
		$arrayTest = array(
			'0' => array('field1' => '11', 'field2' => '12', 'field3' => '13'),
			'1' => array('field1' => '21', 'field2' => '22', 'field3' => '23'),
			'2' => array('field1' => '31', 'field2' => '32', 'field3' => '33'),
		);
		
		$arrayResult = array(
			'0' => '11',
			'1' => '21',
			'2' => '31',
		);
		
		$arrayTest2 = array(
			'0' => array('field1' => '11', 'field2' => '12', 'field3' => '13'),
			'1' => array('field1' => '21', 'field2' => '22', 'field3' => '23'),
			'2' => array('field1' => '31', 'field2' => '32', 'field3' => '33'),
			'3' => array('field1' => '11', 'field2' => '12', 'field3' => '12'),
		);
		
		$arrayResult2 = array(
			'0' => '11',
			'1' => '21',
			'2' => '31',
			'3' => '11',
		);
		
		self::assertEquals(array(), CArray::uniqueByField($arrayTest));
		self::assertEquals($arrayResult, CArray::uniqueByField($arrayTest, 'field1'));
		self::assertEquals($arrayResult2, CArray::uniqueByField($arrayTest2, 'field1', false));
		self::assertEquals($arrayResult, CArray::uniqueByField($arrayTest2, 'field1', true));
	}
	
	/**
	 * Test for CArray::changeKeysCase
	 */
	public function testChangeKeysCase(): void
	{
		$arrTest = array('Key1'=>1, 'kEy2'=>2);
		$arrTestLower = array('key1'=>1, 'key2'=>2);
		$arrTestUpper = array('KEY1'=>1, 'KEY2'=>2);
		
		self::assertEquals($arrTestLower, CArray::changeKeysCase($arrTest));
		self::assertEquals($arrTestLower, CArray::changeKeysCase($arrTest, CASE_LOWER));
		self::assertEquals($arrTestUpper, CArray::changeKeysCase($arrTest, CASE_UPPER));
	}
	
	/**
	 * Test for CArray::toArray
	 */
	public function testTtoArray()
	{
		$obj = new stdClass();
		$obj->fieldA = 'A';
		$obj->fieldB = 2;
		$obj->fieldC = array(1, 2, 3);
		$obj->fieldD = array('1'=>1, '2'=>2, '3'=>3);
		
		$array = array(
			'fieldA' => 'A',
			'fieldB' => 2,
			'fieldC' => array(0 => 1, 1 => 2, 2 => 3),
			'fieldD' => array(1 => 1, 2 => 2, 3 => 3),
		);
		
		self::assertEquals(array(), CArray::toArray(new stdClass()));
		self::assertEquals($array, CArray::toArray($obj));
	}
}
