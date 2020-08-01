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
        $array1 = [
            '0' => ['field1' => '11', 'field2' => '12', 'field3' => '13'],
            '1' => ['field1' => '21', 'field2' => '22', 'field3' => '23'],
        ];

        $array2 = [
            '11' => ['field1' => '11', 'field2' => '12', 'field3' => '13'],
            '21' => ['field1' => '21', 'field2' => '22', 'field3' => '23'],
        ];

        self::assertEquals([], CArray::flipByField($array1));
        self::assertEquals($array2, CArray::flipByField($array1, 'field1'));
    }

    /**
     * Test for CArray::uniqueByField
     */
    public function testUniqueByField(): void
    {
        $arrayTest = [
            '0' => ['field1' => '11', 'field2' => '12', 'field3' => '13'],
            '1' => ['field1' => '21', 'field2' => '22', 'field3' => '23'],
            '2' => ['field1' => '31', 'field2' => '32', 'field3' => '33'],
        ];

        $arrayResult = [
            '0' => '11',
            '1' => '21',
            '2' => '31',
        ];

        $arrayTest2 = [
            '0' => ['field1' => '11', 'field2' => '12', 'field3' => '13'],
            '1' => ['field1' => '21', 'field2' => '22', 'field3' => '23'],
            '2' => ['field1' => '31', 'field2' => '32', 'field3' => '33'],
            '3' => ['field1' => '11', 'field2' => '12', 'field3' => '12'],
        ];

        $arrayResult2 = [
            '0' => '11',
            '1' => '21',
            '2' => '31',
            '3' => '11',
        ];

        self::assertEquals([], CArray::uniqueByField($arrayTest));
        self::assertEquals($arrayResult, CArray::uniqueByField($arrayTest, 'field1'));
        self::assertEquals($arrayResult2, CArray::uniqueByField($arrayTest2, 'field1', false));
        self::assertEquals($arrayResult, CArray::uniqueByField($arrayTest2, 'field1', true));
    }

    /**
     * Test for CArray::changeKeysCase
     */
    public function testChangeKeysCase(): void
    {
        $arrTest      = ['Key1' => 1, 'kEy2' => 2];
        $arrTestLower = ['key1' => 1, 'key2' => 2];
        $arrTestUpper = ['KEY1' => 1, 'KEY2' => 2];

        self::assertEquals($arrTestLower, CArray::changeKeysCase($arrTest));
        self::assertEquals($arrTestLower, CArray::changeKeysCase($arrTest, CASE_LOWER));
        self::assertEquals($arrTestUpper, CArray::changeKeysCase($arrTest, CASE_UPPER));
    }

    /**
     * Test for CArray::toArray
     */
    public function testTtoArray()
    {
        $obj         = new stdClass();
        $obj->fieldA = 'A';
        $obj->fieldB = 2;
        $obj->fieldC = [1, 2, 3];
        $obj->fieldD = ['1' => 1, '2' => 2, '3' => 3];

        $array = [
            'fieldA' => 'A',
            'fieldB' => 2,
            'fieldC' => [0 => 1, 1 => 2, 2 => 3],
            'fieldD' => [1 => 1, 2 => 2, 3 => 3],
        ];

        self::assertEquals([], CArray::toArray(new stdClass()));
        self::assertEquals($array, CArray::toArray($obj));
    }
}
