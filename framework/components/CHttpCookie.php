<?php
/**
 * CHttpCookie provides cookie-level data management
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct                                          
 * set
 * get
 * remove
 * clear
 * setDomain
 * setPath
 * getAll
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */	  

class CHttpCookie extends CComponent
{
	/** @var integer - timestamp at which the cookie expires. Default 0 means "until the browser is closed" */
	public $expire = 0;	
	/** @var boolean */
	public $secure = false;
	/** @var boolean whether the cookie should be accessible only through the HTTP protocol or not */
	public $httpOnly = true;

	/** @var string - the domain that the cookie is available to */
	private $_domain = '';
	/** @var string - path on the server where the cookie will be available on. The default is '/' */
	private $_path = '/';	

	/**
	 * Class default constructor
	 */
	function __construct()
	{
		if(CConfig::get('cookies.domain') != '') $this->setDomain(CConfig::get('cookies.domain'));
		if(CConfig::get('cookies.path') != '') $this->setPath(CConfig::get('cookies.path'));
	}
    
    /**
     * Returns the instance of object
     * @return CHttpCookie class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}

	/**
	 * Sets cookie domain
	 * @param string $domain
	 */
	public function setDomain($domain = '')
	{
		$this->_domain = $domain;
	}

	/**
	 * Sets cookie path
	 * @param string $path
	 */
	public function setPath($path = '')
	{
		$this->_path = $path;
	}

	/**
	 * Sets cookie
	 * @param string $name
	 * @param mixed $value
	 * @param mixed $expire
	 * @param mixed $path
	 * @param mixed $domain
	 */
	public function set($name, $value = '', $expire = '', $path = '', $domain = '')
	{
		$expire = (!empty($expire)) ? $expire : $this->expire;
		$path = (!empty($path)) ? $path : $this->_path;
		$domain = (!empty($domain)) ? $domain : $this->_domain;
		
		if(version_compare(PHP_VERSION,'5.2.0','>=')){
			setcookie($name, $value, $expire, $path, $domain, $this->secure, $this->httpOnly);
		}else{
			setcookie($name, $value, $expire, $path, $domain, $this->secure);
		}
	}

	/**
	 * Returns cookie value
	 * @param string $name
	 */
	public function get($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
	}
	
	/**
	 * Deletes cookie
	 * @param string $name
	 */
	public function remove($name)
	{
		if(version_compare(PHP_VERSION,'5.2.0','>=')){
			setcookie($name, null, 0, $this->_path, $this->_domain, $this->secure, $this->httpOnly);
		}else{
			setcookie($name, null, 0, $this->_path, $this->_domain, $this->secure);
		}
	}

	/**
	 * Deletes all cookie
	 */
	public function clear()
	{
		if(!isset($_COOKIE)) return '';
		
		foreach($_COOKIE as $key => $value){
			self::remove($name);
		}		
	}
	
	/**
	 * Get all cookies
	 */
	public function getAll()
	{
		return isset($_COOKIE) ? $_COOKIE : array();
	}
	
    
}