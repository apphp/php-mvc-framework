<?php
/**
 * Apphp bootstrap file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2015 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					    PRIVATE:		
 * ----------               ----------                      ----------
 * __construct              _onBeginRequest                 _autoload
 * run                      _registerCoreComponents
 * init (static)			_setComponent
 * app (static)				_registerAppComponents 
 * powered (static)			_registerAppHelpers
 * getVersion (static)		_registerAppModules
 * t (static)				_hasEvent
 * getComponent             _hasEventHandler
 * getClientScript          _raiseEvent
 * clientScript
 * getRequest
 * request
 * getLocalTime
 * localTime
 * getSession
 * session
 * getCookie
 * cookie
 * getUri
 * uri
 * attachEventHandler       
 * detachEventHandler       
 * mapCoreComponent         
 * mapAppModule
 * setResponseCode
 * getResponseCode
 * setLanguage
 * getLanguage
 * setCurrency
 * getCurrency
 * 
 */

class A
{
    /**	@var object View */
    public $view;
    /**	@var object Router */
    public $router;
    /** @var string */
    public $charset = 'UTF-8';
    /** @var string */
    public $sourceLanguage = 'en';    
    
	/** @var string */
    private static $_phpVersion;	
	/** @var object */
    private static $_instance;
    /** @var array */
    private static $_classMap = array(
        'Controller'    => 'controllers',
        'Model'         => 'models',
        ''              => 'models',
    );
    /** @var array */
    private static $_coreClasses = array(
        'CConfig'        => 'collections/CConfig.php',
        
        'CController'   => 'core/CController.php',
        'CDebug'        => 'core/CDebug.php',
        'CModel'        => 'core/CModel.php',
        'CRouter'       => 'core/CRouter.php',
        'CView'         => 'core/CView.php',
        
        'CActiveRecord' => array('5.2.0'=>'db/CActiveRecord.520.php', '5.3.0'=>'db/CActiveRecord.530.php'),
        'CDatabase'     => 'db/CDatabase.php',
        'CDataGrid'     => 'db/CDataGrid.php',
    );
    /** @var array */
    private static $_coreComponents = array(
		'component'    	=> array('class' => 'CComponent', 		'path' => array('5.2.0'=>'components/CComponent.520.php', '5.3.0'=>'components/CComponent.530.php')),
        'clientScript'	=> array('class' => 'CClientScript', 	'path' => 'components/CClientScript.php'),
        'dbSession' 	=> array('class' => 'CDbHttpSession', 	'path' => 'components/CDbHttpSession.php'),
        'request'   	=> array('class' => 'CHttpRequest', 	'path' => 'components/CHttpRequest.php'),
        'session'   	=> array('class' => 'CHttpSession', 	'path' => 'components/CHttpSession.php'),
        'cookie'    	=> array('class' => 'CHttpCookie', 		'path' => 'components/CHttpCookie.php'),
        'localTime'     => array('class' => 'CLocalTime', 		'path' => 'components/CLocalTime.php'),
        'coreMessages' 	=> array('class' => 'CMessageSource', 	'path' => 'components/CMessageSource.php',	'language' => 'en'),
		'messages' 		=> array('class' => 'CMessageSource', 	'path' => 'components/CMessageSource.php'),
		'uri'   		=> array('class' => 'CUri',				'path' => 'components/CUri.php'),
    );
    /** @var array */
    private static $_coreHelpers = array(
        'CArray'        => 'helpers/CArray.php',        
        'CAuth'         => 'helpers/CAuth.php',
        'CCache'        => 'helpers/CCache.php',
        'CCurrency'     => 'helpers/CCurrency.php',
        'CFile'         => 'helpers/CFile.php',
        'CFilter'       => 'helpers/CFilter.php',
        'CHash'         => 'helpers/CHash.php',        
        'CHtml'         => 'helpers/CHtml.php',
        'CImage'        => 'helpers/CImage.php',
        'CMailer'       => 'helpers/CMailer.php',
        'CNumber'       => 'helpers/CNumber.php',
		'CPdf'          => 'helpers/CPdf.php',
		'CRss'          => 'helpers/CRss.php',
        'CString'       => 'helpers/CString.php',
        'CTime'         => 'helpers/CTime.php',
        'CValidator'    => 'helpers/CValidator.php',
        'CWidget'       => 'helpers/CWidget.php',
	);
    /** @var array */
    private static $_coreModules = array(        
        // 'General'   => '/core/modules/General.php'
    );    
    /** @var array */
    private static $_appClasses = array(
        // empty
    );
    /** @var array */
    private static $_appComponents = array(
        // empty
    );
    /** @var array */
    private static $_appHelpers = array(
		// empty
	);
    /** @var array */
    private static $_appModules = array(
        'setup' => array('classes' => array('Setup'))
    );
    /** @var array */	
    private $_components = array(); 
    /** @var array */	
    private $_events;
    /** @var boolean */	
    private $_setup = false;
    /** @var string */
    private $_responseCode = '';
    /** @var string */    
    private $_language = '';
    /** @var string */    
    private $_currency = '';
    

    /**
     * Class constructor
     * @param array $configDir
     */
    public function __construct($configDir)
    {
    	spl_autoload_register(array($this, '_autoload'));        
        // include interfaces
        require(dirname(__FILE__).DS.'core'.DS.'interfaces.php');    
        
		self::$_phpVersion = phpversion();
		 
        $configMain = $configDir.'main.php';
        $configDb = $configDir.'db.php';
        
        if(is_string($configMain) && is_string($configDb)){
            // check if main configuration file exists
            if(!file_exists($configMain)){
                $arrConfig = array(
                    'defaultTemplate' => 'setup',
                    'defaultController' => 'Setup',
                    'defaultAction' => 'index',                                   
                );
                // block access to regular files when application is not properly installed
                $url = isset($_GET['url']) ? $_GET['url'] : '';
                if(!preg_match('/setup\//i', $url)){
                    $_GET['url'] = 'setup/index';
                }
                $this->_setup = true;

                // set default timezone
                date_default_timezone_set('UTC');
            }else{
                $arrConfig = require($configMain);
                // check if db configuration file exists and marge it with a main config file
                if(file_exists($configDb)){
                    $arrDbConfig = require($configDb);
                    $arrConfig = array_merge($arrConfig, $arrDbConfig);
                    
                    // check if modules configuration files exist and marge its with a main config file
                    foreach($arrConfig['modules'] as $module => $moduleInfo){
                        $configFile = APPHP_PATH.'/protected/config/'.$module.'.php';
                        if(file_exists($configFile)){
                            $configFileContent = include($configFile);
                            // merge rules settings
                            if(isset($configFileContent['urlManager']['rules'])){
                                $tempConfig = $configFileContent['urlManager']['rules'];
                                $arrConfig['urlManager']['rules'] = array_merge($arrConfig['urlManager']['rules'], $tempConfig);
                            }
                            // merge components settings
                            if(isset($configFileContent['components'])){
                                $tempConfig = $configFileContent['components'];
                                // add module identificator to component
                                foreach($tempConfig as $key => $val){                                    
                                    $tempConfig[$key]['module'] = $module;
                                }
                                $arrConfig['components'] = array_merge($arrConfig['components'], $tempConfig);
                            }
                            // override default Controller/Action settings
                            if(isset($configFileContent['defaultController']) && isset($configFileContent['defaultAction'])){
								$arrConfig['defaultController'] = $configFileContent['defaultController'];
								$arrConfig['defaultAction'] = $configFileContent['defaultAction'];
							}
                        }
                    }
                }
            }
            
            // save configuration array in config class
            CConfig::load($arrConfig);
        }
    }	
 
    /**
     * Runs application
     */
    public function run()
    {
        if(APPHP_MODE != 'hidden'){
            // specify error settings
            if(APPHP_MODE == 'debug' || APPHP_MODE == 'test'){
                error_reporting(E_ALL);
                ini_set('display_errors', 'On');
            }else{
                error_reporting(E_ALL);
                ini_set('display_errors', 'Off');
                ini_set('log_errors', 'On');
                ini_set('error_log', APPHP_PATH.DS.'protected'.DS.'tmp'.DS.'logs'.DS.'error.log');
            }
        
            if(CConfig::get('session.cacheLimiter') == 'private,must-revalidate'){
                // to prevent 'Web Page exired' message on using submission method 'POST'
                session_cache_limiter('private, must-revalidate');    
            }
    
            // initialize Debug class
            CDebug::init(); 
            
            // load view (must do it before app components registration)
            $this->view = new CView(); 
            $this->view->setTemplate(CConfig::get('defaultTemplate'));
        }
        
        // register framework core components
        $this->_registerCoreComponents();

        // global test for database
        if(CConfig::get('db.driver') != ''){
            $db = CDatabase::init();
            if(!CAuth::isGuest()) $db->cacheOff();
        }
   
        // register application components
        $this->_registerAppComponents();
		// register application helpers
		$this->_registerAppHelpers();
        // register application modules
        $this->_registerAppModules();
        
        // run events
        if($this->_hasEventHandler('_onBeginRequest')) $this->_onBeginRequest();
	
        if(APPHP_MODE != 'hidden'){
            $this->router = new CRouter(); 
            $this->router->route();        
            CDebug::displayInfo();
        }
    }

    /**
     * Class init constructor
     * @param array $config
     * @return Apphp
     */
    public static function init($config = array())
    {
        if(self::$_instance == null) self::$_instance = new self($config);
        return self::$_instance;
    }

    /**
     * Returns A object
     * @param array $config
     * @return Apphp
     */
    public static function app()
    {
    	return self::$_instance;
    }
 
    /**
     * Returns the version of ApPHP framework
     * @return string 
     */
    public static function getVersion()
    {
    	return '0.6.9';
    }

    /**
     * Returns a string that can be displayed on your Web page showing Powered-by-ApPHP
     * @return string 
     */
    public static function powered()
    {
        return self::t('core', 'Powered by').' <a href="http://www.apphp.com/" rel="external">ApPHP</a>';
    }

    /**
     * Translates a message to the specified language
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $source
     * @param string $language
     * @return string 
     */
    public static function t($category = 'app', $message = '', $params = array(), $source = null, $language = null)
    {
    	if(self::$_instance !== null){
            if($source === null) $source = ($category === 'core') ? 'coreMessages' : 'messages';
            if(($source = self::$_instance->getComponent($source)) !== null){
                $message = $source->translate($category, $message, $language);
            }
        }
        
        if($params === array()){
            return $message;
        }else{
            if(!is_array($params)) $params = array($params);
            return $params !== array() ? strtr($message, $params) : $message;
        }
    }


    /**
     * Autoloader
     * @param str $className
     * @return void
     */
    private function _autoload($className)
    {
		// Framework: CORE CLASSES
        if(isset(self::$_coreClasses[$className])){			
			$classPath = '';
			// check if we need PHP version compatible class
			if(is_array(self::$_coreClasses[$className])){
				foreach(self::$_coreClasses[$className] as $key => $val){
					if(self::$_phpVersion >= $key){
						$classPath = $val;
					}
				}
			}else{
				$classPath = self::$_coreClasses[$className];
			}
			
			include(dirname(__FILE__).DS.$classPath);
		}
		// Framework: HELPER CLASSES or HELPER EXTENSIONS
		else if(isset(self::$_coreHelpers[$className])){            
			$coreHelper = dirname(__FILE__).DS.self::$_coreHelpers[$className];
			$extCoreHelper = APPHP_PATH.DS.'protected'.DS.self::$_coreHelpers[$className];
			// check if there extension exists in application
			if(is_file($extCoreHelper)){
				include($extCoreHelper);
			}else{
				include($coreHelper);
			}		
        }
		// Framework: COMPONENT CLASSES
		else if($coreComponent = $this->mapCoreComponent($className)){			
			include(dirname(__FILE__).DS.$coreComponent);
        }

		// Application: COMPONENT CLASSES
		else if(isset(self::$_appClasses[$className])){            
            include(APPHP_PATH.DS.'protected'.DS.self::$_appClasses[$className]);
        }
		// Application: HELPER CLASSES
		else if(isset(self::$_coreHelpers[$className])){
			include(APPHP_PATH.DS.'protected'.DS.self::$_appHelpers[$className]);
		}
		 
		// check if required class is Controller or Model (in application or modules)
		else{            
            $classNameItems = preg_split('/(?=[A-Z])/', $className);
            $itemsCount = count($classNameItems);
            // $classNameItems[0] - 
            // $classNameItems[1..n-1] - ClassName
            // $classNameItems[n] - Type (Controller, Model, etc..)            
            $pureClassName = $pureClassType = '';
            for($i=0; $i<$itemsCount; $i++){
                if($i < $itemsCount-1){
                    $pureClassName .= isset($classNameItems[$i]) ? $classNameItems[$i] : '';    
                }else{
                    $pureClassType = isset($classNameItems[$i]) ? $classNameItems[$i] : '';    
                }
            }            
            
            // use model mapping pattern for classes AaaBbbCcc
            if(!isset(self::$_classMap[$pureClassType])){
                $pureClassName = $className;
                $pureClassType = 'Model';
            }            
            
            if(isset(self::$_classMap[$pureClassType])){                
                $classCoreDir = APPHP_PATH.DS.'protected'.DS.self::$_classMap[$pureClassType];    
                $classFile = $classCoreDir.DS.$className.'.php';
                if(is_file($classFile)){
                    include($classFile);
                }else{
                    $classModuleDir = APPHP_PATH.DS.'protected'.DS.$this->mapAppModule($pureClassName).self::$_classMap[$pureClassType];
                    $classFile = $classModuleDir.DS.$className.'.php';
                    if(is_file($classFile)){
                        include($classFile);
                    }else{
                        CDebug::addMessage('errors', 'missing-model', A::t('core', 'Unable to find class "{class}".', array('{class}'=>$className)), 'session');
						// [04.04.2015] This is not a core class - don't redirect to Error controller, just show error in debug panel
						//A::app()->getSession()->setFlash('error500', A::t('core', 'Unable to find class "{class}".', array('{class}'=>$className)));
                        //header('location: '.$this->getRequest()->getBaseUrl().'error/index/code/500');
                        //exit;
                    }
                }     
                CDebug::addMessage('general', 'classes', $className);
            }
        }        
    }    

    /**
     * Puts a component under the management of the application
     * @param string $id
     * @param class $component 
     */
    protected function _setComponent($id, $component)
    {
    	if($component === null){
            unset($this->_components[$id]);		
    	}else{
            // for PHP_VERSION >= 5.3.0 you may use
            // $this->_components[$id] = $component::init();
            if($callback = call_user_func_array($component.'::init', array())){
                $this->_components[$id] = $callback;    
            }else if(!in_array($component, array('CComponent'))){
                CDebug::addMessage('warnings', 'missing-components', $component);    
            }            
        }
    }
 
    /**
     * Returns the application component
     * @param string $id
     */
    public function getComponent($id)
    {
    	return (isset($this->_components[$id])) ? $this->_components[$id] : null;
    }

    /**
     * Returns the client script component
     * @return CClientScript component
     */
    public function getClientScript()
    {
    	return $this->getComponent('clientScript');
    }
    
    /**
     * Alias to getClientScript
     * @SEE getClientScript()
     * @return CClientScript component
     */
    public function clientScript()
    {
    	return $this->getClientScript();
    }

    /**
     * Returns the request component
     * @return CHttpRequest component
     */
    public function getRequest()
    {
    	return $this->getComponent('request');
    }

    /**
     * Alias to getRequest
     * @SEE getRequest()
     * @return CHttpRequest component
     */
    public function request()
    {
    	return $this->getRequest();
    }

    /**
     * Returns the localTime component
     * @return CLocalTime component
     */
    public function getLocalTime()
    {
    	return $this->getComponent('localTime');
    }
    
    /**
	 * Alias to getLocalTime
     * @SEE getLocalTime()
     * @return CLocalTime component
     */
    public function localTime()
    {
    	return getLocalTime();
    }

    /**
     * Returns the session component
     * @return CHttpSession or CDbHttpSession component
     */
    public function getSession()
    {
        if(CConfig::get('session.customStorage')){
            return $this->getComponent('dbSession');    
        }else{
            return $this->getComponent('session');    
        }    	
    }

    /**
     * Alias to getSession
     * @SEE getSession()
     * @return CHttpSession or CDbHttpSession component
     */
    public function session()
    {
        return $this->getSession();    
    }

    /**
     * Returns the cookie component
     * @return CHttpCookie component
     */
    public function getCookie()
    {
    	return $this->getComponent('cookie');
    }
	
    /**
     * Alias to getCookie
     * @SEE getCookie()
     * @return CHttpCookie component
     */
    public function cookie()
    {
    	return $this->getCookie();
    }

    /**
     * Returns the uri component
     * @return CUri component
     */
    public function getUri()
    {
    	return $this->getComponent('uri');
    }

    /**
     * Alias to getUri
     * @SEE getUri()
     * @return CUri component
     */
    public function url()
    {
    	return $this->getUri();
    }

    /**
     * Attaches event handler
     * @param string $name
     * @param string $handler
     */
    public function attachEventHandler($name, $handler)
    {
    	if($this->_hasEvent($name)){
            $name = strtolower($name);
            if(!isset($this->_events[$name])){
                $this->_events[$name] = array();
            }
            if(!in_array($handler, $this->_events[$name])){
                $this->_events[$name][] = $handler;
            }
        }else{
            CDebug::addMessage('errors', 'events-attach', A::t('core', 'Event "{class}.{name}" is not defined.', array('{class}'=>get_class($this), '{name}'=>$name)));
        }
    }

    /**
     * Detaches event handler
     * @param string $name
     */
    public function detachEventHandler($name)
    {
    	if($this->_hasEvent($name)){
            $name = strtolower($name);
            if(isset($this->_events[$name])){
                unset($this->_events[$name]);
            }
        }else{
            CDebug::addMessage('errors', 'events-detach', A::t('core', 'Event "{class}.{name}" is not defined.', array('{class}'=>get_class($this), '{name}'=>$name)));
        }
    }

    /**
     * Checks whether an event is defined
     * An event is defined if the class has a method named like 'onSomeMethod'
     * @param string $name 
     * @return boolean 
     */
    protected function _hasEvent($name)
    {
    	return !strncasecmp($name, '_on', 3) && method_exists($this, $name);
    }
    
    /**
     * Checks whether the named event has attached handlers
     * @param string $name 
     * @return boolean 
     */
    public function _hasEventHandler($name)
    {
    	$name = strtolower($name);
    	return isset($this->_events[$name]) && count($this->_events[$name]) > 0;
    }

    /**
     * Raises an event
     * @param string $name 
     */
    public function _raiseEvent($name)
    {
        $name = strtolower($name);
        if(isset($this->_events[$name])){
            foreach($this->_events[$name] as $handler){
                if(is_string($handler[1])){
                    call_user_func_array(array($handler[0], $handler[1]), array());
                }else{
                    CDebug::addMessage('errors', 'events-raising', A::t('core', 'Event "{{class}}.{{name}}" is attached with an invalid handler "{'.$handler[1].'}".', array('{class}'=>$handler[0], '{name}'=>$handler[1])));
                }
            }
        }
    }
    
    /**
     * Maps core components
     * @param string $class
     */
    public function mapCoreComponent($class)
    {
		$path = '';
		foreach(self::$_coreComponents as $id => $component){
			if(isset($component['class']) && $component['class'] === $class){				
				if(isset($component['path'])){
					// check if we need PHP version compatible class
					if(is_array($component['path'])){						
						foreach($component['path'] as $key => $val){
							if(self::$_phpVersion >= $key){
								$path = $val;
							}
						}						
					}else{
						$path = $component['path'];
					}
				}				
				break;
			}
		}
		return $path;
	}

    /**
     * Maps application modules
     * @param string $class
     * @return string
     */
    public function mapAppModule($class)
    {
		$path  = '';
        foreach(self::$_appModules as $module => $moduleInfo){
            if(!isset($moduleInfo['classes']) || !is_array($moduleInfo['classes'])) continue;
            if(in_array(strtolower($class), array_map('strtolower', $moduleInfo['classes']))){
                $path = 'modules/'.$module.'/';
				break;
            }
        }
        return $path ;
    }

    /**
     * Sets response code 
     * @param string $code
     */
    public function setResponseCode($code = '')
    {
        $this->_responseCode = $code;
    }

    /**
     * Sets response code 
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }
    
    /**
     * Specifies which language the application is targeted to
     * @param string $language (code)
     * @param array $params
     */
    public function setLanguage($language = '', $params = array())
    {
    	$this->_language = $language;
        $this->getSession()->set('language', $this->_language);
        if(isset($params['locale'])){
            $this->getSession()->set('language_locale', $params['locale']);
            if(!setlocale(LC_ALL, $params['locale'])) CDebug::addMessage('warnings', 'missing-locale', A::t('core', 'Unable to find locale "{locale}" on your server.', array('{locale}'=>$params['locale'])), 'session');
        }
        if(isset($params['direction'])) $this->getSession()->set('language_direction', $params['direction']);
    }

    /**
     * Returns the language that is used for application or language parameter
     * @param string $param
     * @param bool $useDefault
     * @return string 
     */
    public function getLanguage($param = '', $useDefault = true)
    {
        $language = $this->getSession()->get(($param != '') ? 'language_'.$param : 'language');
        if(!empty($language)){
            return $language;
        }else if($this->_language === null && $useDefault){ 
            return $this->sourceLanguage;
        }else{
            return $this->_language;      
        }
    }
    
    /**
     * Specifies which currency the application is targeted to
     * @param string $currency (code)
     * @param array $params
     */
    public function setCurrency($currency = '', $params = array())
    {
    	$this->_currency = $currency;
        $this->getSession()->set('currency_code', $this->_currency);
        if(isset($params['symbol'])) $this->getSession()->set('currency_symbol', $params['symbol']);
        if(isset($params['symbol_place'])) $this->getSession()->set('currency_symbol_place', $params['symbol_place']);
        if(isset($params['decimals'])) $this->getSession()->set('currency_decimals', $params['decimals']);
        if(isset($params['rate'])) $this->getSession()->set('currency_rate', $params['rate']);
    }

    /**
     * Returns the currency that is used for application or currency parameter
     * @param string $param
     * @return string 
     */
    public function getCurrency($param = '')
    {
        $currency = $this->getSession()->get(($param != '') ? 'currency_'.$param : 'currency_code');            
        if(!empty($currency)){
            return $currency;
        }else{
            return (!$param) ? $this->_currency : '';    
        }
    }

    /**
     * Registers the framework core components
     * @see _setComponent
     */
    protected function _registerCoreComponents()
    {
    	foreach(self::$_coreComponents as $id => $component){
            if(CConfig::get('session.customStorage') && $id == 'session'){
                continue; 
            }else if(!CConfig::get('session.customStorage') && $id == 'dbSession'){
                continue; 
            }
            $this->_setComponent($id, $component['class']);
        }
    }
 
    /**
     * Raised before the application processes the request
     */
    protected function _onBeginRequest()
    {
    	$this->_raiseEvent('_onBeginRequest');
    }
    
    /**
     * Registers application components
     * @see _setComponent
     */
    protected function _registerAppComponents()
    {
    	if(!is_array(CConfig::get('components'))) return false;
        foreach(CConfig::get('components') as $id => $component){
            $enable = isset($component['enable']) ? (bool)$component['enable'] : false;
            $class = isset($component['class']) ? $component['class'] : '';
            $module = isset($component['module']) ? $component['module'] : '';
            self::$_appComponents[$id] = array('enable' => $enable, 'class' => $class);
            if($enable && $class){
                self::$_appClasses[$class] = (!empty($module) ? 'modules/'.$module.'/' : '').'components/'.$class.'.php';
                $this->_setComponent($id, $class);
            }
        }
    }
	
    /**
     * Registers application helpers
     * @see _setComponent
     */
	protected function _registerAppHelpers()
	{
		if(!is_array(CConfig::get('helpers'))) return false;
		foreach(CConfig::get('helpers') as $id => $module){
            $enable = isset($module['enable']) ? (bool)$module['enable'] : false;
            $class = isset($module['class']) ? $module['class'] : '';
            if($enable && $class){
                self::$_appHelpers[$class] = (!empty($module) ? 'modules/'.$module.'/' : '').'helpers/'.$class.'.php';
            }
		}
	}

    /**
     * Registers application modules
     */
    protected function _registerAppModules()
    {
     	if(!is_array(CConfig::get('modules'))) return false;
        foreach(CConfig::get('modules') as $id => $module){
            $enable = isset($module['enable']) ? (bool)$module['enable'] : false;
            if($enable){
                $moduleName = strtolower($id);
                $moduleConfig = 'protected/modules/'.$moduleName.'/config/main.php';
                if(file_exists($moduleConfig)){
                    $arrConfig = include_once($moduleConfig);
                    self::$_appModules[$moduleName] = $arrConfig;
                }
            }
        }
    }
    
}