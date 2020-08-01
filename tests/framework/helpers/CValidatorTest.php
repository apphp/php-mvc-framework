<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use CValidator;

require_once('tests/autoload.php');

class CValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test for CValidator::isEmpty
     */
    public function testIsEmpty(): void
    {
        self::assertEquals(true, CValidator::isEmpty(null));
        self::assertEquals(true, CValidator::isEmpty([]));
        self::assertEquals(true, CValidator::isEmpty(''));
        self::assertEquals(true, CValidator::isEmpty(' ', true));

        self::assertNotEquals(true, CValidator::isEmpty(0));
        self::assertNotEquals(true, CValidator::isEmpty('text'));
    }

    /**
     * Test for CValidator::isAlpha
     */
    public function testIsAlpha(): void
    {
        self::assertEquals(true, CValidator::isAlpha('abcde'));
        self::assertEquals(true, CValidator::isAlpha('ABCDE'));
        self::assertEquals(true, CValidator::isAlpha('ABcde'));
        self::assertEquals(true, CValidator::isAlpha('ABcde#', ['#']));
        self::assertEquals(true, CValidator::isAlpha('ABc de#', [' ', '#']));

        self::assertNotEquals(true, CValidator::isAlpha('ABcde3'));
        self::assertNotEquals(true, CValidator::isAlpha('ABcde#'));
        self::assertNotEquals(true, CValidator::isAlpha('ABc de'));
        self::assertNotEquals(true, CValidator::isAlpha('ABc de#', ['#']));
    }


}