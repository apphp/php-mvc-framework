<?php
/**
 * CAuthentication (CAuth) is a helper class that provides basic authentication methods
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * isLoggedIn
 * isLoggedInAs
 * isLoggedInAsAdmin
 * isGuest
 * handleLogin
 * handleLoggedIn
 * getLoggedId
 * getLoggedName
 * getLoggedEmail
 * getLoggedLastVisit
 * getLoggedAvatar
 * getLoggedLang
 * getLoggedRole
 * getLoggedParam
 * 
 */	  

class CAuth
{
    
    /**
     * Checks if user is logged in and returns a result
     * @return bool
     */
    public static function isLoggedIn()
    {
        return (A::app()->getSession()->get('loggedId') == true) ? true : false;
    }

    /**
     * Checks if user is logged in as a specific account role
     * @param string ('role1', 'role2', [,...])
     * @return bool
     */
    public static function isLoggedInAs()
    {
		if(!self::isLoggedIn()) return false;	
		$loggedRole = self::getLoggedRole();
		$roles = func_get_args();
		if(in_array($loggedRole, $roles)){
			return true;
		}
		return false;        
    }
    
    /**
     * Checks if user is logged in as an admin
     * @return bool
     */
    public static function isLoggedInAsAdmin()
    {
		if(!self::isLoggedIn()) return false;	
		$loggedRole = self::getLoggedRole();
		$adminRoles = array('owner', 'mainadmin', 'admin');
		if(in_array($loggedRole, $adminRoles)){
			return true;
		}
		return false;        
    }

    /**
     * Checks if user is a guest (not logged in)
     * @return bool
     */
    public static function isGuest()
    {
        return (!self::isLoggedIn()) ? true : false;
    }    

    /**
     * Handles access for non-logged users (block access)
     * @param string $location
     * @param string $role
     */
    public static function handleLogin($location = 'index/index', $role = '')
    {
        if(APPHP_MODE == 'test') return '';
        $isLoggedIn = ($role === '') ? self::isLoggedInAsAdmin() : self::isLoggedInAs($role);
        if(!$isLoggedIn){
            //session_destroy();
            header('location: '.A::app()->getRequest()->getBaseUrl().$location);
            exit;
        }
    }

    /**
     * Handles access for logged in users (redirect logged in users)
     * @param string $location
     * @param string $role
     */
    public static function handleLoggedIn($location = '', $role = '')
    {
        if(APPHP_MODE == 'test') return '';
        $isLoggedIn = ($role === '') ? self::isLoggedInAsAdmin() : self::isLoggedInAs($role);
        if($isLoggedIn){
            header('location: '.A::app()->getRequest()->getBaseUrl().$location);
            exit;
        }
    }
    
    /**
     * Returns ID of logged in user
     * @return string
     */
    public static function getLoggedId()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedId') : null;
    }
    
    /**
     * Returns display name of logged in user
     * @return string
     */
    public static function getLoggedName()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedName') : null;
    }

    /**
     * Returns email of logged in user
     * @return string
     */
    public static function getLoggedEmail()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedEmail') : null;
    }
	
    /**
     * Returns last visit date of logged in user
     * @return string
     */
    public static function getLoggedLastVisit()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedLastVisit') : null;
    }
	
    /**
     * Returns avatar of logged in user
     * @return string
     */
    public static function getLoggedAvatar()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedAvatar') : null;
    }
    
    /**
     * Returns preferred language of logged in user
     * @return string
     */
    public static function getLoggedLang()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedLanguage') : null;
    }    
	
    /**
     * Returns role of logged in user
     * @return string
     */
    public static function getLoggedRole()
    {
        return (self::isLoggedIn()) ? A::app()->getSession()->get('loggedRole') : null;
    }

    /**
     * Returns parameter value of logged in user
     * @param string $param
     * @return string
     */
    public static function getLoggedParam($param)
    {
		$result = null;
		if(self::isLoggedIn() && A::app()->getSession()->isExists($param)){
			$result = A::app()->getSession()->get($param);			
		}
		return $result;
    }
    
}