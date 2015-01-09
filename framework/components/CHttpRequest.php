<?php
/**
 * CHttpRequest is a default application component loaded by Apphp.
 * Manages HTTP values sent by a client during a Web request.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct              _cleanRequest               _getParam
 * stripSlashes
 * getBasePath
 * getBaseUrl
 * getRequestUri
 * getUserHostAddress
 * setBaseUrl
 * getQuery
 * getPost
 * getPostWith
 * setPost
 * getRequest
 * isPostRequest
 * isPostExists
 * getCsrfValidation
 * getCsrfTokenKey
 * getCsrfTokenValue
 * validateCsrfToken
 * downloadFile
 * 
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * 
 */	  

class CHttpRequest extends CComponent
{

	/** @var string */
	private $_baseUrl;
	/** @var string */
	private $_basePath = '/';
	/** @var boolean to enable cookies validation to be sure they are not tampered (defaults - false) */
	public $cookieValidation = false;
	/** @var boolean whether to enable CSRF (Cross-Site Request Forgery) validation (defaults - false) */
	private $_csrfValidation = false;
	/** @var string */
	private $_csrfTokenKey = 'APPHP_CSRF_TOKEN';
	/** @var string */
	private $_csrfTokenValue = null;
	/** @var string session or cookie */
	private $_csrfTokenType = 'session';
	
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
		$this->_csrfValidation = (CConfig::get('validation.csrf') === true) ? true : false;
		
		$this->_cleanRequest();
		$this->_baseUrl = $this->setBaseUrl();
	}
    
    /**
     *	Returns the instance of object
     *	@return CHttpRequest class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}

	/**
	 * Strips slashes from data
	 * @param mixed $data input data to be processed
	 * @return mixed processed data
	 */
	public function stripSlashes(&$data)
	{
		return is_array($data) ? array_map(array($this,'stripSlashes'), $data) : stripslashes($data);
	}
	
	/**
	 * Gets base URL
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->_baseUrl;
	}
    
	/**
	 * Gets Request URI
	 * @return string
	 */
	public function getRequestUri()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}    

	/**
	 * Gets IP address of visitor
	 * @return string 
	 */
	public function getUserHostAddress()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	}

	/**
	 * Gets base path
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}	
	
	/**
	 * Sets base URL
	 * @param boolean $absolute
	 */
	public function setBaseUrl($absolute = true)
	{
		$absolutePart = '';
		
		if($absolute){
			$protocol = 'http://';
			$port = '';
			$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
			if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ||
				strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https'){
				$protocol = 'https://';
			}	
			if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
				if(!strpos($httpHost, ':')){
					$port = ':'.$_SERVER['SERVER_PORT'];
				}
			}
			$absolutePart = $protocol.$httpHost.$port;
		}

		$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
		if(basename($_SERVER['SCRIPT_NAME']) === $scriptName){
			$scriptUrl = $_SERVER['SCRIPT_NAME'];
		}else if(basename($_SERVER['PHP_SELF']) === $scriptName){
			$scriptUrl = $_SERVER['PHP_SELF'];
		}else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName){
			$scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
		}else if(($pos=strpos($_SERVER['PHP_SELF'], '/'.$scriptName)) !== false){
			$scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos).'/'.$scriptName;
		}else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0){
			$scriptUrl = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
		}else{
			CDebug::addMessage('error', 'entry_script', A::t('core', 'Framework is unable to determine the entry script URL'));
		}

		$this->_basePath = rtrim(dirname($scriptUrl),'\\/').'/';
		
		return $absolutePart.$this->_basePath;
	}

    /**
     *	Returns parameter from global array $_GET
     *	@param string $name
     *	@param string|array $filters
     *	@param string $default
     *	@return mixed
     */
	public function getQuery($name, $filters = '', $default = '')
	{
		return $this->_getParam('get', $name, $filters, $default);		
	}
    
    /**
     *	Returns parameter from global array $_POST
     *	@param string $name
     *	@param string|array $filters
     *	@param string $default
     *	@return mixed
     */
	public function getPost($name, $filters = '', $default = '')
	{
		return $this->_getParam('post', $name, $filters, $default);		
	}

    /**
     *	Returns parameter from global array $_POST
     *	@param string $name
     *	@param string|array $filters
     *	@param string $default
     *	@return array
     */
	public function getPostWith($name, $filters = '', $default = '')
	{
        $result = array();
        if(!isset($_POST) || !is_array($_POST)) return $result;

        foreach($_POST as $key => $val){
            if(preg_match('/'.$name.'/i', $key)){
                $result[$key] = $this->_getParam('post', $key, $filters, $default);
            }
        }            
        return $result;
	}

    /**
     *	Sets value to global array $_POST
     *	@param string $name
     *	@param string $value
     *	@return bool
     */
	public function setPost($name, $value = '')
	{
		if(isset($_POST[$name])){
			$_POST[$name] = $value;
			return true;
		}
		return false;
	}

    /**
     *	Returns parameter from global array $_GET or $_POST
     *	@param string $name
     *	@param string|array $filters
     *	@param string $default
     *	@return mixed
     */
	public function getRequest($name, $filters = '', $default = '')
	{
		return $this->_getParam('request', $name, $filters, $default);		
	}

	/**
	 * Returns whether there is a POST request
	 * @return boolean 
	 */
	public function isPostRequest()
	{
		return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
	}
	
	/**
	 * Returns whether there is a POST variable exists
	 * @param string $name
	 * @return boolean 
	 */
	public function isPostExists($name)
	{
		return isset($_POST[$name]);
	}

	/**
	 * Returns is csrf validation is used
	 * @return string 
	 */
	public function getCsrfValidation()
	{
		return $this->_csrfValidation;
	}
	
	/**
	 * Returns csrf token key name
	 * @return string 
	 */
	public function getCsrfTokenKey()
	{
		return $this->_csrfTokenKey;
	}
	
	/**
	 * Returns the random token value used to perform CSRF validation
	 * @return string 
	 * @see _csrfValidation
	 */
	public function getCsrfTokenValue()
	{
		// check and set token
		if($this->_csrfTokenValue === null){
			if($this->_csrfTokenType == 'session'){
				$this->_csrfTokenValue = md5(uniqid(rand(), true));	
				A::app()->getSession()->set('token', $this->_csrfTokenValue);
			}else if($this->_csrfTokenType == 'cookie'){
				// TODO: release cookies code here
				// ...
			}
		}
		return $this->_csrfTokenValue;
	}
	
	/**
	 * Performs the CSRF validation
	 */
	public function validateCsrfToken()
	{
		// validate only POST requests
		if($this->isPostRequest()){			
			if(A::app()->getSession()->isExists('token') && isset($_POST[$this->_csrfTokenKey])){
				$tokenFromSession = A::app()->getSession()->get('token');
				$tokenFromPost = $_POST[$this->_csrfTokenKey];
				$valid = ($tokenFromSession === $tokenFromPost);
			}else{
				$valid = false;
			}
			if(!$valid){
				unset($_POST);
				CDebug::addMessage('warnings', 'csrf_token', A::t('core', 'The CSRF token could not be verified.'));
			}
		}
	}

	/**
	 * Cleans the request data.
	 * This method removes slashes from request data if get_magic_quotes_gpc() is turned on
	 * Also performs CSRF validation if {@link _csrfValidation} is true
	 */
	protected function _cleanRequest()
	{
		// clean request
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
			$_GET = $this->stripSlashes($_GET);
			$_POST = $this->stripSlashes($_POST);
			$_REQUEST = $this->stripSlashes($_REQUEST);
			$_COOKIE = $this->stripSlashes($_COOKIE);            
		}
        
		if($this->getCsrfValidation()) A::app()->attachEventHandler('_onBeginRequest', array($this, 'validateCsrfToken'));
	}

    /**
     *	Returns parameter from global arrays $_GET or $_POST according to type of request
     *	@param string $type
     *	@param string $name
     *	@param string|array $filters
     *	@param string $default
     *	@return mixed
     */
	private function _getParam($type = 'get', $name = '', $filters = '', $default = '')
	{
		$value = '';
		if($type == 'get'){
			if(isset($_GET[$name])){
				$value = $_GET[$name];
			}else{
				// check for variant
				// URL: http://localhost/site/page/contact/param1/aaa/param2/bbb/param3/ccc
				$request = isset($_GET['url']) ? $_GET['url'] : '';
				$split = explode('/', trim($request,'/'));
				
				$temp = array();				
				foreach($split as $index => $part){
					if(!$temp || end($temp) !== null){
						$temp[$part] = null;																
					}else{						
						$arrayArg = array_keys($temp);
						$tempEnd = end($arrayArg);
						$temp[$tempEnd] = $part;
					}
				}
				$temp = array_slice($temp, 1);
				if(isset($temp[$name])) $value = $temp[$name];
			}			
		}else if($type == 'post' && isset($_POST[$name])){
			$value = $_POST[$name];
		}else if($type == 'request' && (isset($_GET[$name]) || isset($_POST[$name]))){
			$value = isset($_GET[$name]) ? $_GET[$name] : $_POST[$name];
		}
		if($value !== ''){
			if(!is_array($filters)) $filters = array($filters);
			foreach($filters as $filter){
				$value = CFilter::sanitize($filter, $value);
			}
			return $value;
		}else{
			return $default;
		}		
	}  
   
	/**
	 * Downloads a file from browser to user 
	 * @param string $fileName 
	 * @param string $content 
	 * @param string $mimeType 
	 * @param boolean $terminate 
	 */
	public function downloadFile($fileName, $content, $mimeType = null, $terminate = true)
	{
		if($mimeType === null) $mimeType='text/plain';

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-type: $mimeType");
		if(ob_get_length() === false){
			header('Content-Length: '.(function_exists('mb_strlen') ? mb_strlen($content,'8bit') : strlen($content)));
		}
		header("Content-Disposition: attachment; filename=\"$fileName\"");
		header('Content-Transfer-Encoding: binary');
		echo $content;
		
        if($terminate) exit(0);
	}
 
}