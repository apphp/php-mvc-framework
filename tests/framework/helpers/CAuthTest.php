<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use A;
use CAuth;

require_once('tests/autoload.php');

class CAuthTest extends TestCase
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
	
	/**
	 * Test for CAuth::isLoggedInAs
	 */
	public function testIsLoggedInAs(): void
	{
		A::app()->getSession()->set('loggedId', 0);
		self::assertEquals(false, CAuth::isLoggedInAs());

		A::app()->getSession()->set('loggedId', 1);
		self::assertEquals(false, CAuth::isLoggedInAs('user'));
		
		A::app()->getSession()->set('loggedRole', 'user');
		self::assertEquals(true, CAuth::isLoggedInAs('user'));
		self::assertEquals(false, CAuth::isLoggedInAs('customer'));
	}
	
	/**
	 * Test for CAuth::isLoggedInAsAdmin
	 */
	public function testIsLoggedInAsAdmin(): void
	{
		A::app()->getSession()->set('loggedId', 0);
		self::assertEquals(false, CAuth::isLoggedInAsAdmin());
		
		A::app()->getSession()->set('loggedId', 1);
		self::assertEquals(false, CAuth::isLoggedInAsAdmin());
		
		A::app()->getSession()->set('loggedRole', 'owner');
		self::assertEquals(true, CAuth::isLoggedInAsAdmin());
		
		A::app()->getSession()->set('loggedRole', 'mainadmin');
		self::assertEquals(true, CAuth::isLoggedInAsAdmin());
		
		A::app()->getSession()->set('loggedRole', 'admin');
		self::assertEquals(true, CAuth::isLoggedInAsAdmin());
		
		A::app()->getSession()->set('loggedRole', 'super-admin');
		self::assertEquals(false, CAuth::isLoggedInAsAdmin());
        self::assertEquals(true, CAuth::isLoggedInAsAdmin(['super-admin']));
    }

    //
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
