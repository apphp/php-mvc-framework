<?php
/**
 * CHttpSession provides session-level data management
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct                                          _openSession
 * set                                                  _setCookieMode 
 * get
 * remove
 * isExists
 * setFlash
 * getFlash
 * hasFlash
 * setSessionName
 * getSessionName
 * getTimeout
 * setSessionPrefix
 * endSession
 * closeSession
 * getCookieMode
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */	  

class CHttpSession extends CComponent
{

	/** @var boolean */
	protected $_autoStart = true;
	/** @var string */
	protected $_defaultSessionName = 'apphp_framework';
	/** @var string */
	protected $_defaultSessionPrefix = 'apphp_';	
	/**
	 * @var int
	 * @deprecated since v0.1.0
	 * 0 - use name prefix, 1 - use session name (default)
	 */
	protected $_multiSiteSupportType = 1;
	/**
	 * @var mixed
	 */
	protected $_prefix = '';
	/**
	 * @var string
	 * only | allow | none
	 */
	protected $_cookieMode = 'allow';	
    
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
		if($this->_cookieMode !== 'only'){
			$this->_setCookieMode($this->_cookieMode);
		}
		
		if($this->_multiSiteSupportType){
			$this->setSessionName('apphp_'.CConfig::get('installationKey'));		
		}else{
			$this->setSessionPrefix('apphp_'.CConfig::get('installationKey'));		
		}
		if($this->_autoStart) $this->_openSession();
	}

    /**
     *	Returns the instance of object
     *	@return CHttpSession class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}

	/**
	 * Sets session variable 
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$_SESSION[$this->_prefix.$name] = $value;
	}
	
	/**
	 * Returns session variable 
	 * @param string $name
	 * @param mixed $default
	 */
	public function get($name, $default = '')
	{
		return isset($_SESSION[$this->_prefix.$name]) ? $_SESSION[$this->_prefix.$name] : $default;
	}
    
	/**
	 * Removes session variable 
	 * @param string $name
	 */
	public function remove($name)
	{
		if(isset($_SESSION[$this->_prefix.$name])){
            unset($_SESSION[$this->_prefix.$name]);
            return true;
        }
        return false;
	}
    
	/**
	 * Checks if session variable exists
	 * @param string $name
	 */
	public function isExists($name)
	{
		return isset($_SESSION[$this->_prefix.$name]) ? true : false;
	}

	/**
	 * Sets session flash data
	 * @param string $name
	 * @param mixed $value
	 */
	public function setFlash($name, $value)
	{
		$_SESSION[$this->_prefix.'_flash'][$name] = $value;
	}

	/**
	 * Returns session flash data
	 * @param string $name
	 * @param mixed $default
	 */
	public function getFlash($name, $default = '')
	{
		if(isset($_SESSION[$this->_prefix.'_flash'][$name])){
            $result = $_SESSION[$this->_prefix.'_flash'][$name];
            unset($_SESSION[$this->_prefix.'_flash'][$name]);            
        }else{
            $result = $default;
        }
        return $result;
	}

	/**
	 * Checks if has flash data
	 * @param string $name
	 */
	public function hasFlash($name)
	{
		return isset($_SESSION[$this->_prefix.'_flash'][$name]) ? true : false;
	}

	/**
	 * Sets session name
	 * @param string $value
	 */
	public function setSessionName($value)
	{
		if(empty($value)) $value = $this->_defaultSessionName;
		session_name($value);
	}

	/**
	 * Sets session name
	 * @param string $value
	 */
	public function setSessionPrefix($value)
	{
		if(empty($value)) $value = $this->_defaultSessionPrefix;
		$this->_prefix = $value;
	}

	/**
	 * Gets session name
	 * @return string 
	 */
	public function getSessionName()
	{
		return session_name();
	}

	/**
     * Returns the number of seconds after which data will be seen as 'garbage' and cleaned up
	 * @return integer 
	 */
	public function getTimeout()
	{
		return (int)ini_get('session.gc_maxlifetime');
	}

	/**
	 * Destroys the session
	 */
	public function endSession()
	{
		if(session_id() !== ''){
			@session_unset();
			@session_destroy();
		}
	}

	/**
	 * Session close handler
	 * Do not call this method directly
	 * @return boolean 
	 */
	public function closeSession()
	{
		return true;
	}

	/**
	 * Gets cookie mode
	 * @return string
	 */
	public function getCookieMode()
	{
		if(ini_get('session.use_cookies') === '0'){
			return 'none';
		}else if(ini_get('session.use_only_cookies') === '0'){
			return 'allow';
		}else{
			return 'only';
		}
	}

	/**
	 * Starts the session if it has not started yet
	 */
	private function _openSession()
	{
		@session_start();
		if(APPHP_MODE == 'debug' && session_id() == ''){
            Debug::addMessage('errors', 'session', A::t('core', 'Failed to start session'));
		}
	}

	/**
	 * Sets cookie mode
	 * @value string
	 */
	private function _setCookieMode($value = '')
	{
		if($value === 'none'){
			ini_set('session.use_cookies', '0');
			ini_set('session.use_only_cookies', '0');
		}else if($value === 'allow'){
			ini_set('session.use_cookies', '1');
			ini_set('session.use_only_cookies', '0');
		}else if($value === 'only'){
			ini_set('session.use_cookies', '1');
			ini_set('session.use_only_cookies', '1');
		}else{
			Debug::addMessage('warnings', 'session_cookie_mode', A::t('core', 'HttpSession.cookieMode can only be "none", "allow" or "only".'));
		}
	}
	
}