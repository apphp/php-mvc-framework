<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use A;
use CAuth;

require_once('tests/autoload.php');

class CAuthTestTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}
	
	/**
	 * Test for CAuth::isLoggedIn
	 */
	public function testIsLoggedIn(): void
	{
		A::app()->getSession()->set('loggedId', 0);
		self::assertEquals(false, CAuth::isLoggedIn());

		A::app()->getSession()->set('loggedId', 1);
		self::assertEquals(true, CAuth::isLoggedIn());
	}
	
//	/**
//	 * Test for CAuth::isLoggedInAs
//	 */
//	public function testIsLoggedInAs(): void
//	{
//		A::app()->getSession()->set('loggedId', 0);
//		self::assertEquals(false, CAuth::isLoggedInAs());
//
//		A::app()->getSession()->set('loggedId', 1);
//		self::assertEquals(false, CAuth::isLoggedInAs('user'));
//
//		A::app()->getSession()->set('loggedRole', 'user');
//		self::assertEquals(true, CAuth::isLoggedInAs('user'));
//		self::assertEquals(false, CAuth::isLoggedInAs('customer'));
//	}

	//isLoggedInAsAdmin
	//isGuest()
	// handleLogin
	// handleLoggedIn
	// getLoggedId()
	// getLoggedName()
	//	getLoggedEmail()
	//	getAccountCreated()
	//	getLoggedLastVisit()
	//	getLoggedAvatar()
	//	getLoggedLang()
	//	getLoggedRole()
	//	getLoggedRoleId()
	//	getLoggedParam($param)
	
	
}
