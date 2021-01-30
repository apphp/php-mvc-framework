<?php
/**
 * CHttpRequest is a default application component loaded by Apphp.
 * Manages HTTP values sent by a client during a Web request.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                  PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * __construct              _cleanRequest               _getParam
 * __call                   _getProtocolAndHost         _getAll
 * init (static)            _getControllerAndAction
 * stripSlashes
 * getBasePath
 * getBaseUrl
 * getRequestUri
 * getServerName
 * getUserHostAddress
 * getHostInfo
 * getHostName
 * getUserAgent
 * setBaseUrl
 * getQuery
 * setQuery
 * get (alias to getQuery/setQuery)
 * getAll (alias to getQuery())
 * getPost
 * getPostWith
 * postWith (alias to getPostWith)
 * setPost
 * post (alias to getPost/setPost)
 * postAll (alias to getPost())
 * getRequest
 * request
 * isAjaxRequest
 * isPutRequest
 * isDeleteRequest
 * isPostRequest
 * isPostExists
 * isSecureConnection
 * getCsrfValidation
 * getCsrfTokenKey
 * getCsrfTokenValue
 * validateCsrfToken
 * setGzipHandler
 * downloadContent
 * downloadFile
 * getBrowser
 * setHttpReferrer
 * getUrlReferrer
 * getPort
 * getSecurePort
 * getUrlContent
 *
 */

class CHttpRequest extends CComponent
{

    /** @var string */
    private $_baseUrl;
    /** @var string */
    private $_hostInfo;
    /** @var string */
    private $_hostName;
    /** @var string */
    private $_basePath = '/';
    /** @var boolean to enable cookies validation to be sure they are not tampered (defaults - false) */
    public $cookieValidation = false;
    /** @var boolean whether to enable CSRF (Cross-Site Request Forgery) validation (defaults - false) */
    private $_csrfValidation = false;
    /** @var string excluding controllers */
    private $_csrfExclude = [];
    /** @var boolean to enable gzip output compression */
    private $_gzipCompression = false;
    /** @var string */
    private $_csrfTokenKey = 'APPHP_CSRF_TOKEN';
    /** @var string */
    private $_csrfTokenValue = null;
    /** @var string session, cookie, multipages or multiforms */
    private $_csrfTokenType = 'session';
    /** @var int */
    private $_csrfMaxTokenedPages = 20;
    /** @var int port number */
    private $_port = null;
    /** @var int secure port number */
    private $_securePort = null;
    /** @var boolean
     * whether to enable referrer storage in session - has potential risk
     * requires special handling to prevent endless loops on redirection on the same page
     */
    private $_referrerInSession = false;


    /**
     * Class default constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->_csrfValidation  = (CConfig::get('validation.csrf.enable') === true) ? true : false;
        $this->_csrfExclude     = CConfig::exists('validation.csrf.exclude') ? CConfig::get('validation.csrf.exclude')
            : [];
        $this->_gzipCompression = (CConfig::get('compression.gzip.enable') === true) ? true : false;
        $csrfTokenType          = CConfig::get('validation.csrf.tokenType');
        $this->_csrfTokenType   = ($csrfTokenType !== ''
            && in_array(
                $csrfTokenType,
                ['session', 'cookie', 'multipages', 'multiforms']
            )) ? $csrfTokenType : 'session';

        $this->_cleanRequest();
        $this->_baseUrl = $this->setBaseUrl();
    }

    /**
     * Triggered when invoking inaccessible methods in an object context
     *
     * @param  string  $method
     * @param  array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (strtolower($method)) {
            case 'get':
            case 'post':
            case 'request':

                if ($method == 'get') {
                    $innerGet = 'getQuery';
                    $innerSet = 'setQuery';
                } elseif ($method == 'post') {
                    $innerGet = 'getPost';
                    $innerSet = 'setPost';
                } else {
                    $innerGet = 'getRequest';
                    $innerSet = 'getRequest';
                }

                if (count($args) == 0) {
                    return $this->_getAll($method);
                } elseif (count($args) == 1) {
                    return $this->$innerGet($args[0]);
                } elseif (count($args) == 2) {
                    return $this->$innerSet($args[0], $args[1]);
                } elseif (count($args) == 3) {
                    return $this->$innerGet($args[0], $args[1], $args[2]);
                } elseif (count($args) == 4) {
                    return $this->$innerGet($args[0], $args[1], $args[2], $args[3]);
                }

                break;

            case 'getAll':
                return $this->_getAll('get');

            case 'postAll':
                return $this->_getAll('post');

            case 'postWith':
                if (count($args) == 1) {
                    return $this->getPostWith($args[0]);
                }
                break;
        }
    }

    /**
     *    Returns the instance of object
     *
     * @return current class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Strips slashes from data
     *
     * @param  mixed  $data  input data to be processed
     *
     * @return mixed processed data
     */
    public function stripSlashes(&$data)
    {
        return is_array($data) ? array_map([$this, 'stripSlashes'], $data) : stripslashes($data);
    }

    /**
     * Gets base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Gets Request URI
     *
     * @return string
     */
    public function getRequestUri()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    /**
     * Gets Server Name
     *
     * @return string
     */
    public function getServerName()
    {
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    }

    /**
     * Gets IP address of visitor
     *
     * @return string
     */
    public function getUserHostAddress()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     * Returns the schema and host part of the current request URL
     * The returned URL does not have an ending slash
     * By default this is determined based on the user request information.
     * You may explicitly specify it by setting the setHostInfo()[hostInfo]] property.
     *
     * @return string|null
     */
    public function getHostInfo()
    {
        if ($this->_hostInfo === null) {
            $secure = $this->isSecureConnection();
            $http   = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_hostInfo = $http.'://'.$_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->_hostInfo = $http.'://'.$_SERVER['SERVER_NAME'];
                $port            = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && ! $secure) || ($port !== 443 && $secure)) {
                    $this->_hostInfo .= ':'.$port;
                }
            }
        }

        return $this->_hostInfo;
    }

    /**
     * Returns the host part of the current request URL (ex.: apphp.com)
     * Value is calculated from current getHostInfo()[hostInfo] property
     *
     * @return string|null
     * @see getHostInfo()
     * @since 0.9.0
     */
    public function getHostName()
    {
        if ($this->_hostName === null) {
            $this->_hostName = parse_url($this->getHostInfo(), PHP_URL_HOST);
        }

        return $this->_hostName;
    }

    /**
     * Gets a string denoting the user agent being which is accessing the page.
     * A typical example is: Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586).
     *
     * @return string
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    /**
     * Gets base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Sets base URL
     *
     * @param  boolean  $useAbsolutePath
     *
     * @return string
     */
    public function setBaseUrl($useAbsolutePath = true)
    {
        $absolutePart = ($useAbsolutePath) ? $this->_getProtocolAndHost() : '';

        $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
        if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
            $scriptUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (basename($_SERVER['PHP_SELF']) === $scriptName) {
            $scriptUrl = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
            $scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
        } elseif (($pos = strpos($_SERVER['PHP_SELF'], '/'.$scriptName)) !== false) {
            $scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos).'/'.$scriptName;
        } elseif (isset($_SERVER['DOCUMENT_ROOT'])
            && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0
        ) {
            $scriptUrl = str_replace(
                '\\',
                '/',
                str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME'])
            );
        } else {
            CDebug::addMessage(
                'error',
                'entry_script',
                A::t('core', 'Framework is unable to determine the entry script URL')
            );
        }

        $this->_basePath = rtrim(dirname($scriptUrl), '\\/').'/';

        return $absolutePart.$this->_basePath;
    }

    /**
     *    Returns parameter from global array $_GET
     *
     * @param  string  $name
     * @param  string|array  $filters
     * @param  string  $default
     * @param  array  $allowedValues
     *
     * @return mixed
     * @see CFilter
     */
    public function getQuery($name = '', $filters = '', $default = '', $allowedValues = [])
    {
        if (empty($name)) {
            return $this->_getAll('get');
        } else {
            return $this->_getParam('get', $name, $default, $filters, $allowedValues);
        }
    }

    /**
     *    Sets value to global array $_GET
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return bool
     */
    public function setQuery($name, $value = '')
    {
        if (isset($_GET)) {
            $_GET[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     *    Returns parameter from global array $_POST
     *
     * @param  string  $name
     * @param  string|array  $filters
     * @param  string  $default
     * @param  array  $allowedValues
     *
     * @return mixed
     * @see CFilter
     */
    public function getPost($name = '', $filters = '', $default = '', $allowedValues = [])
    {
        if (empty($name)) {
            return $this->_getAll('post');
        } else {
            return $this->_getParam('post', $name, $default, $filters, $allowedValues);
        }
    }

    /**
     *    Returns variables from global array $_POST with certain parameter
     *
     * @param  string  $name
     * @param  string|array  $filters
     * @param  string  $default
     * @param  array  $allowedValues
     *
     * @return array
     */
    public function getPostWith($name, $filters = '', $default = '', $allowedValues = [])
    {
        $result = [];
        if ( ! isset($_POST) || ! is_array($_POST)) {
            return $result;
        }

        foreach ($_POST as $key => $val) {
            if (preg_match('/'.$name.'/i', $key)) {
                $result[$key] = $this->_getParam('post', $key, $default, $filters, $allowedValues);
            }
        }

        return $result;
    }

    /**
     *    Sets value to global array $_POST
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return bool
     */
    public function setPost($name, $value = '')
    {
        if (isset($_POST)) {
            $_POST[$name] = $value;

            return true;
        }

        return false;
    }

    /**
     *    Returns parameter from global array $_GET or $_POST
     *
     * @param  string  $name
     * @param  string|array  $filters
     * @param  string  $default
     * @param  array  $allowedValues
     *
     * @return mixed
     */
    public function getRequest($name = '', $filters = '', $default = '', $allowedValues = [])
    {
        if (empty($name)) {
            return $this->_getAll('request');
        } else {
            return $this->_getParam('request', $name, $default, $filters, $allowedValues);
        }
    }

    /**
     * Returns whether there is an AJAX (XMLHttpRequest) request
     *
     * @return boolean
     * @since 0.6.0
     */
    public function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Returns whether there is a PUT request
     *
     * @return boolean
     * @since 0.6.0
     */
    public function isPutRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && ! strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT');
    }

    /**
     * Returns whether there is a DELETE request
     *
     * @return boolean
     * @since 0.6.0
     */
    public function isDeleteRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && ! strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE');
    }

    /**
     * Returns whether there is a POST request
     *
     * @return boolean
     */
    public function isPostRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && ! strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * Returns whether there is a POST variable exists
     *
     * @param  string  $name
     *
     * @return boolean
     */
    public function isPostExists($name)
    {
        return isset($_POST[$name]);
    }

    /**
     * Return if the request is sent via secure channel (https)
     *
     * @return boolean
     */
    public function isSecureConnection()
    {
        return (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1))
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0);
    }

    /**
     * Returns if csrf validation is used
     *
     * @return string
     */
    public function getCsrfValidation()
    {
        if ( ! empty($this->_csrfExclude) && is_array($this->_csrfExclude)) {
            // Retrirve current controller
            // TODO: this is a simplest code, we need to improve it and use URL rules
            $request    = isset($_GET['url']) ? $_GET['url'] : '';
            $split      = explode('/', trim($request, '/'));
            $controller = ! empty($split[0]) ? $split[0] : CConfig::get('defaultController');

            if (in_array(strtolower($controller), array_map('strtolower', $this->_csrfExclude))) {
                return false;
            }
        }

        return $this->_csrfValidation;
    }

    /**
     * Returns CSRF token key name
     *
     * @return string
     */
    public function getCsrfTokenKey()
    {
        return $this->_csrfTokenKey;
    }

    /**
     * Returns the random token value used to perform CSRF validation
     *
     * @param  string  $formId
     *
     * @return string
     * @see $this->_csrfValidation()
     */
    public function getCsrfTokenValue($formId = '')
    {
        // Check and set token
        $csrfTokenValue = md5(uniqid(rand(), true));

        if ($this->_csrfTokenType == 'session') {
            if ($this->_csrfTokenValue === null) {
                $this->_csrfTokenValue = $csrfTokenValue;
                A::app()->getSession()->set('token', $this->_csrfTokenValue);
            }
        } elseif ($this->_csrfTokenType == 'cookie') {
            if ($this->_csrfTokenValue === null) {
                $this->_csrfTokenValue = $csrfTokenValue;
                A::app()->getCookie()->set('token', $this->_csrfTokenValue);
            }
        } elseif ($this->_csrfTokenType == 'multipages' || $this->_csrfTokenType == 'multiforms') {
            if ($this->_csrfTokenType == 'multipages') {
                // Get page ID
                $tokenId = $this->_getControllerAndAction();
            } else {
                $tokenId = $formId;
            }
            if (CString::length($tokenId) < 100 && CValidator::isVariable($tokenId)) {
                if ($this->_csrfTokenValue === null) {
                    $this->_csrfTokenValue = $csrfTokenValue;
                }
                // Get array and validate
                $csrfTokenValues = A::app()->getSession()->get('token');
                if (empty($csrfTokenValues) || ! is_array($csrfTokenValues)) {
                    $csrfTokenValues = [];
                }
                // Save data
                $csrfTokenValues[$tokenId] = ($this->_csrfTokenValue !== null) ? $this->_csrfTokenValue
                    : $csrfTokenValue;
                if (count($csrfTokenValues) > $this->_csrfMaxTokenedPages) {
                    array_shift($csrfTokenValues);
                }
                A::app()->getSession()->set('token', $csrfTokenValues);
            }
        }

        return $this->_csrfTokenValue;
    }

    /**
     * Performs the CSRF validation
     *
     * @param  string  $formId
     *
     * @return void
     */
    public function validateCsrfToken($formId = '')
    {
        // Validate only POST requests
        if ($this->isPostRequest()) {
            $valid         = false;
            $tokenFromPost = isset($_POST[$this->_csrfTokenKey]) ? $_POST[$this->_csrfTokenKey] : null;

            if ($this->_csrfTokenType == 'session') {
                if (A::app()->getSession()->isExists('token') && isset($_POST[$this->_csrfTokenKey])) {
                    $tokenFromSession = A::app()->getSession()->get('token');
                    $valid            = ($tokenFromSession === $tokenFromPost);
                }
            } elseif ($this->_csrfTokenType == 'cookie') {
                if (A::app()->getCookie()->isExists('token') && isset($_POST[$this->_csrfTokenKey])) {
                    $tokenFromCookie = A::app()->getCookie()->get('token');
                    $valid           = ($tokenFromCookie === $tokenFromPost);
                }
            } elseif ($this->_csrfTokenType == 'multipages' || $this->_csrfTokenType == 'multiforms') {
                if ($this->_csrfTokenType == 'multipages') {
                    // Get page ID
                    $tokenId = $this->_getControllerAndAction();
                } else {
                    $tokenId = $formId;
                }
                if (A::app()->getSession()->isExists('token') && isset($_POST[$this->_csrfTokenKey])) {
                    $tokenFromSession = A::app()->getSession()->get('token');
                    $tokenFromSession = isset($tokenFromSession[$tokenId]) ? $tokenFromSession[$tokenId] : '';
                    $valid            = ($tokenFromSession === $tokenFromPost);
                }
            }

            if ( ! $valid) {
                unset($_POST);
                A::app()->getSession()->setFlash(
                    'csrfError',
                    CWidget::create(
                        'CMessage',
                        [
                            'warning',
                            A::t(
                                'core',
                                'The CSRF token has expired or invalid. Please try to <a href="javascript:csrf_refresh_page()" class="csrf-refresh-page">refresh page</a> and resubmit the form.'
                            )
                        ]
                    )
                );
                CDebug::addMessage(
                    'warnings',
                    'csrf_token',
                    A::t('core', 'The CSRF token could not be verified.'),
                    'session'
                );
                A::app()->getClientScript()->registerScript(
                    'csrfError',
                    'function csrf_refresh_page(){location.href = location.href + \'?\' + Math.random();}',
                    2
                );
            }
        }
    }

    /**
     * Set GZIP compression handler
     */
    public function setGzipHandler()
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            // Fix for warning: ob_start() [ref.outcontrol]: output handler 'ob_gzhandler' conflicts with zlib output compression'
            if (extension_loaded('zlib')) {
                if (ob_get_length()) {
                    ob_end_clean();
                }
            }
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
    }

    /**
     * Cleans the request data
     * This method removes slashes from request data if get_magic_quotes_gpc() is turned on
     * Also performs CSRF validation if {@link _csrfValidation} is true
     */
    protected function _cleanRequest()
    {
        // 01.12.2018 - DEPRECATED
        // Clean request only for PHP < 5.3, in greater versions of PHP 'magic' functions are deprecated
        //if(version_compare(phpversion(), '5.3.0', '<')){
        //	if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
        //		$_GET = $this->stripSlashes($_GET);
        //		$_POST = $this->stripSlashes($_POST);
        //		$_REQUEST = $this->stripSlashes($_REQUEST);
        //		$_COOKIE = $this->stripSlashes($_COOKIE);
        //	}
        //}

        if ($this->getCsrfValidation()) {
            A::app()->attachEventHandler('_onBeginRequest', [$this, 'validateCsrfToken']);
        }
        if ($this->_gzipCompression) {
            A::app()->attachEventHandler('_onBeginRequest', [$this, 'setGzipHandler']);
        }
        if ($this->_referrerInSession) {
            A::app()->attachEventHandler('_onBeginRequest', [$this, 'setHttpReferrer']);
        }
    }

    /**
     * Returns protocol and host
     *
     * @param  bool  $usePort
     *
     * @return string
     */
    protected function _getProtocolAndHost($usePort = true)
    {
        $protocol       = 'http://';
        $port           = '';
        $httpHost       = isset($_SERVER['HTTP_HOST']) ? htmlentities($_SERVER['HTTP_HOST']) : '';
        $serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';

        if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
            || strtolower(
                substr($serverProtocol, 0, 5)
            ) == 'https'
        ) {
            $protocol = 'https://';
        }

        if ($usePort) {
            $portNumber = $this->getPort();
            if ($portNumber != '80' && ! strpos($httpHost, ':')) {
                $port = ':'.$portNumber;
            }
        }

        return $protocol.$httpHost.$port;
    }

    /**
     * Returns controller and action from URL
     *
     * @return string
     */
    protected function _getControllerAndAction()
    {
        $request = isset($_GET['url']) ? $_GET['url'] : '';
        $split   = explode('/', trim($request, '/'));
        $pageId  = isset($split[0]) ? $split[0] : '';
        $pageId  .= isset($split[1]) ? '_'.$split[1] : '';

        return $pageId;
    }

    /**
     *    Returns parameter from global arrays $_GET or $_POST according to the type of request
     *
     * @param  string  $type
     * @param  string  $name
     * @param  string|array  $filters
     * @param  array  $allowedValues
     * @param  string  $default
     *
     * @return mixed
     */
    private function _getParam($type = 'get', $name = '', $default = '', $filters = '', $allowedValues = [])
    {
        $value = null;

        if ($type == 'get') {
            if (isset($_GET[$name])) {
                $value = $_GET[$name];
            } else {
                // Check for variant
                // URL: http://localhost/site/page/contact/param1/aaa/param2/bbb/param3/ccc
                $request = isset($_GET['url']) ? $_GET['url'] : '';
                $split   = explode('/', trim($request, '/'));

                $temp = [];
                foreach ($split as $index => $part) {
                    if ( ! $temp || end($temp) !== null) {
                        $temp[$part] = null;
                    } else {
                        $arrayArg       = array_keys($temp);
                        $tempEnd        = end($arrayArg);
                        $temp[$tempEnd] = $part;
                    }
                }
                $temp = array_slice($temp, 1);
                if (isset($temp[$name])) {
                    $value = $temp[$name];
                }
            }
        } elseif ($type == 'post' && isset($_POST[$name])) {
            $value = $_POST[$name];
        } elseif ($type == 'request' && (isset($_GET[$name]) || isset($_POST[$name]))) {
            $value = isset($_GET[$name]) ? $_GET[$name] : $_POST[$name];
        }

        if ($value !== null) {
            // Validate allowed values
            if ( ! empty($allowedValues)) {
                if ( ! is_array($allowedValues)) {
                    $allowedValues = [$allowedValues];
                }
                if ( ! in_array($value, $allowedValues)) {
                    $value = $default;
                }
            }

            // Filter values
            if ( ! empty($filters)) {
                if ( ! is_array($filters)) {
                    $filters = [$filters];
                }
                foreach ($filters as $filter) {
                    $value = CFilter::sanitize($filter, $value);
                }
            }
        } else {
            $value = $default;
        }

        return $value;
    }

    /**
     *    Returns global arrays: $_GET, $_POST or $_REQUEST according to given type
     *
     * @param  string  $type
     *
     * @return array
     */
    private function _getAll($type = 'get')
    {
        if ($type == 'get') {
            return isset($_GET) ? $_GET : [];
        } elseif ($type == 'post') {
            return isset($_POST) ? $_POST : [];
        } elseif ($type == 'request' && (isset($_GET) || isset($_POST))) {
            return isset($_GET) ? $_GET : $_POST;
        }

        return [];
    }

    /**
     * Downloads a content as file from browser to user
     *
     * @param  string  $fileName
     * @param  string  $content
     * @param  string  $mimeType
     * @param  boolean  $terminate
     */
    public function downloadContent($fileName, $content, $mimeType = null, $terminate = true)
    {
        if ($mimeType === null) {
            $mimeType = 'text/plain';
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-type: '.$mimeType);
        if (ob_get_length() === false) {
            header('Content-Length: '.(function_exists('mb_strlen') ? mb_strlen($content, '8bit') : strlen($content)));
        }
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Content-Transfer-Encoding: binary');
        echo $content;

        if ($terminate) {
            exit(0);
        }
    }

    /**
     * Downloads a file from browser to user
     *
     * @param  string  $file
     * @param  string  $mimeType
     * @param  boolean  $terminate
     */
    public function downloadFile($file, $mimeType = null, $terminate = true)
    {
        if ($mimeType === null) {
            $mimeType = CFile::getMimeType($file);
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-type: '.$mimeType);
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Content-Transfer-Encoding: binary');
        readfile($file);

        if ($terminate) {
            exit(0);
        }
    }

    /**
     * Returns information about the browser of user
     *
     * @param  string  $key
     * @param  string  $userAgent
     *
     * @return array
     * @see http://www.php.net/manual/en/function.get-browser.php
     */
    public function getBrowser($key = '', $userAgent = null)
    {
        $browser = get_browser($userAgent, true);

        if ( ! empty($key)) {
            return isset($browser[$key]) ? $browser[$key] : '';
        }

        return $browser;
    }

    /**
     * Sets HTTP Referrer
     * Has potential risk because can insert current URL as a referrer and lead to endless loops on redirection
     * Ex.: language or currency changes
     */
    public function setHttpReferrer()
    {
        // Save current data as previous referrer
        A::app()->getSession()->set('http_referrer_previous', A::app()->getSession()->get('http_referrer_current'));
        // Save current link as referrer
        $httpReferrerCurrent = $this->_getProtocolAndHost().$this->getRequestUri();
        A::app()->getSession()->set('http_referrer_current', $httpReferrerCurrent);
    }

    /**
     * Returns the URL referrer, null if not present
     */
    public function getUrlReferrer()
    {
        $serverReferrer  = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $sessionReferrer = A::app()->getSession()->get('http_referrer_previous');

        if ( ! empty($serverReferrer)) {
            return $serverReferrer;
        } elseif ( ! empty($sessionReferrer)) {
            return $sessionReferrer;
        } else {
            return null;
        }
    }

    /**
     * Returns the port to use for insecure requests
     * Defaults to 80 or the port specified by the server (if the current request is insecure)
     *
     * @return int
     * @since 0.7.0
     */
    public function getPort()
    {
        if ($this->_port === null) {
            $this->_port = ! $this->isSecureConnection() && isset($_SERVER['SERVER_PORT'])
                ? (int)$_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * Returns the port to use for secure requests
     * Defaults to 443, or the port specified by the server (if the current request is secure)
     *
     * @return int
     * @since 0.7.0
     */
    public function getSecurePort()
    {
        if ($this->_securePort === null) {
            $this->_securePort = $this->isSecureConnection() && isset($_SERVER['SERVER_PORT'])
                ? (int)$_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * Returns content of the given URL
     *
     * @param  string  $url
     * @param  string  $method
     * @param  string  $data
     * @param  string  $params
     * @param  string  $function  'file_get_contents' or 'curl'
     *
     * @return mixed
     */
    function getUrlContent($url = '', $method = 'get', $data = [], $params = [], $function = 'file_get_contents')
    {
        # Validate function argumanets
        $method = strtolower($method);
        $data   = (array)$data;

        if (empty($url) && ! in_array($method, ['get', 'post'])) {
            return true;
        }

        # Get parameters
        $ajaxCall      = isset($params['ajax']) ? (bool)$params['ajax'] : false;
        $showErrors    = isset($params['errors']) ? (bool)$params['errors'] : false;
        $json          = isset($params['json']) ? (bool)$params['json'] : false;
        $sslVerifyHost = isset($params['ssl_verify_host']) ? (bool)$params['ssl_verify_host'] : false;
        $sslVerifyPeer = isset($params['ssl_verify_peer']) ? (bool)$params['ssl_verify_peer'] : false;
        $result        = null;

        if ($function == 'curl') {
            # Init curl
            $ch = curl_init();

            # Set options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt(
                $ch,
                CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)'
            );

            # Fake AJAX call
            if ($ajaxCall) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Requested-With: XMLHttpRequest"]);
            }

            # SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, ($sslVerifyHost ? 2 : 0));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, ($sslVerifyPeer ? 1 : 0));

            if ($method == 'post') {
                # Set the HEADER, number of POST vars, POST data
                if ($json) {
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
                    curl_setopt($ch, CURLOPT_POST, count($data));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                } else {
                    curl_setopt($ch, CURLOPT_POST, count($data));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                }
            }

            if ($showErrors) {
                # Check for errors and include in the error message
                $error = '';
                if ($errno = curl_errno($ch)) {
                    $errorMessage = function_exists('curl_strerror') ? curl_strerror($errno) : '';
                    $error        = "cURL error ({$errno}):\n {$errorMessage}";
                }

                $result['result'] = curl_exec($ch);
                $result['error']  = $error;
            } else {
                $result = curl_exec($ch);
            }

            # Close connection
            curl_close($ch);
        } else {
            $context = null;

            # Use key 'http' even if you send the request to https://
            if ($method == 'post') {
                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                            ($ajaxCall ? "X-Requested-With: XMLHttpRequest\r\n" : ''),
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ],
                ];

                # Disable SSL verification
                if ( ! $sslVerifyPeer) {
                    $options['ssl'] = [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ];
                }

                $context = stream_context_create($options);
            }

            $result = file_get_contents($url, false, $context);
        }

        return $result;
    }

}
