<?php
/**
 * CDbHttpSession provides session-level data management by using database as session data storage.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2015 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct                                          _startSession
 * init (static)										_deleteExpiredSessions
 * set
 * get
 * remove
 * isExists
 * setFlash
 * getFlash
 * hasFlash
 * setSessionName
 * getSessionName
 * setTimeout
 * getTimeout
 * setSessionPrefix
 * endSession
 * getCookieMode
 * 
 * openSession
 * closeSession
 * readSession
 * writeSession
 * destroySession
 * gcSession
 *
 */	  

class CDbHttpSession extends CComponent
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

    /** @var Database */
    private $_db;	
    
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
        $this->_db = CDatabase::init();
        
        @session_set_save_handler(
            array($this, 'openSession'),
            array($this, 'closeSession'),
            array($this, 'readSession'),
            array($this, 'writeSession'),
            array($this, 'destroySession'),
            array($this, 'gcSession')
        );
        
		// The following prevents unexpected effects when using objects as save handlers
		register_shutdown_function('session_write_close');

		if($this->_multiSiteSupportType){
			$this->setSessionName('apphp_'.CConfig::get('installationKey'));
		}else{
			$this->setSessionPrefix('apphp_'.CConfig::get('installationKey'));		
		}        

		if($this->_autoStart) $this->_startSession();
	}

    /**
     *	Returns the instance of object
     *	@return current class
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
		$_SESSION[$name] = $value;
	}
	
	/**
	 * Returns session variable 
	 * @param string $name
	 * @param mixed $default
	 */
	public function get($name, $default = '')
	{
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
	}
    
	/**
	 * Removes session variable 
	 * @param string $name
	 */
	public function remove($name)
	{
		if(isset($_SESSION[$name])){
            unset($_SESSION[$name]);
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
		return isset($_SESSION[$name]) ? true : false;
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
	 * @return bool
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
	 * Session open handler
	 * Do not call this method directly
	 * @param string $savePath 
	 * @param string $sessionName 
	 * @return boolean 
	 */
	public function openSession($savePath, $sessionName)
	{
        $this->_deleteExpiredSessions();
		return true;
	}
    
	/**
	 * Session close handler
	 * Do not call this method directly
	 * @return boolean 
	 */
	public function closeSession()
	{
		if(session_id() !== '') @session_write_close();
		return true;
	}

	/**
	 * Session read handler
	 * Do not call this method directly
	 * @param string $id 
	 * @return bool
	 */
	public function readSession($id)
	{
        $result = $this->_db->select('SELECT session_data FROM `'.CConfig::get('db.prefix').'sessions` WHERE session_id = :session_id', array(':session_id'=>$id));

		// Read session data and store it into $_SESSION array
		if(isset($result[0]['session_data'])){
			
			$dataPairs = explode('|', $result[0]['session_data']);
			
			// Prepare array of session variables in the following format:
			// [var_name_1] => serialized data
			// [var_name_2] => serialized data
			// etc.
			$previousData = '';
			$previousName = '';
			$dataPairsNew = array();
			foreach($dataPairs as $key => $val){
				if(!empty($previousData)){
					
					$previousDataRev = strrev($previousData);
					$po1 = strpos($previousDataRev,  ';');
					$po2 = strpos($previousDataRev, '}');
					
					if((!empty($po1) && empty($po2)) || (!empty($po1) && !empty($po2) && $po1 < $po2)){						
						$divider = ';';
					}else{
						$divider = '}';
					}
					
					$previousDataParts = explode($divider, $previousData);					
					$previousDataCount = count($previousDataParts);
					$paramName = isset($previousDataParts[$previousDataCount - 1]) ? $previousDataParts[$previousDataCount - 1] : '';
					unset($previousDataParts[$previousDataCount - 1]);
					$paramValue = implode($divider, $previousDataParts);
					
					$dataPairsNew[$previousName] = $paramValue;
					if($paramValue[0] == 'a'){
						$dataPairsNew[$previousName] .= '}';
					}
				}else{
					$paramName = $val;
				}
				$previousName = $paramName;
				$previousData = $val;
			}
			
			$dataPairsNew[$previousName] = $dataPairs[count($dataPairs) - 1];
			
			// Store session variables in global array $_SESSION
			foreach($dataPairsNew as $key => $val){				
				if(!empty($key)){
					$_SESSION[$key] = unserialize($val);
				}				
			}
		}
		
        return true;
	}

	/**
	 * Session write handler
	 * Do not call this method directly
	 * @param string $id 
	 * @param string $data 
	 * @return boolean 
	 */
	public function writeSession($id, $data)
	{        
        $result = $this->_db->select('SELECT * from `'.CConfig::get('db.prefix').'sessions` WHERE session_id = :session_id', array(':session_id'=>$id));
        if(isset($result[0])){
            $result = $this->_db->update(
                'sessions',
                array(
                    'expires_at' => time() + $this->getTimeout(),
                    'session_data' => $data
                ),
                'session_id = :session_id',
                array(':session_id' => $id)
            );
        }else{
            $result = $this->_db->insert(
                'sessions',
                array(
                    'session_id' => $id,
                    'expires_at' => time() + $this->getTimeout(),
                    'session_data' => $data
                )
            );
        }
        
		return true;
	}

	/**
	 * Session destroy handler
	 * Do not call this method directly
	 * @param string $id 
	 * @return boolean 
	 */
	public function destroySession($id)
	{
		return $this->_db->delete('sessions', "session_id = :session_id", array(':session_id'=>$id));        
	}

	/**
	 * Session garbage collection handler
	 * Do not call this method directly
	 * @param int $maxLifetime 
	 * @return boolean 
	 */
	public function gcSession($maxLifetime)
	{
        return $this->_deleteExpiredSessions();
	}
    
	/**
	 * Sets the number of seconds after which data will be seen as 'garbage' and cleaned up
	 * @param int $value 
	 */
	public function setTimeout($value)
	{
		ini_set('session.gc_maxlifetime', (int)$value);
	}

	/**
     * Returns the number of seconds after which data will be seen as 'garbage' and cleaned up
	 * @return integer 
	 */
	public function getTimeout()
	{
		// Get lifetime value from configuration file (in minutes)
		$maxlifetime = CConfig::get('session.lifetime');
		return (!empty($maxlifetime)) ? (int)($maxlifetime * 60) : (int)ini_get('session.gc_maxlifetime');
	}

	/**
	 * Starts the session if it has not started yet
	 */
	private function _startSession()
	{
		// Set lifetime value from configuration file (in minutes)
		$maxLifetime = CConfig::get('session.lifetime');		
		if(!empty($maxLifetime) && $maxLifetime != ini_get('session.gc_maxlifetime')){
			$this->setTimeout($maxLifetime);
		} 

		@session_start();
		if(APPHP_MODE == 'debug' && session_id() == ''){
            Debug::addMessage('errors', 'session', A::t('core', 'Failed to start session'));
		}
	}

	/**
	 * Deletes expired sessions
	 * @return bool
	 */
	private function _deleteExpiredSessions()
	{
		return $this->_db->delete('sessions', 'expires_at < :expires_at', array(':expires_at'=>time()));
	}
	
}
