<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use CString;

require_once('tests/autoload.php');

class CStringTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}
	
	/**
	 * Test for \CString::substr
	 */
	public function testSubstr(): void
	{
		self::assertEquals('hello', CString::substr('hello world', 5, '', false));
		self::assertEquals('hello w', CString::substr('hello world', 7, '', false));
	}
	

}