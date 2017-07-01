<?php
/**
 * Apphp bootstrap file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					    PRIVATE:		
 * ----------               ----------                      ----------
 * __construct              _onBeginRequest                 _autoload
 * run                      _onEndRequest					_runApp
 * init (static)			_registerCoreComponents
 * app (static)				_setComponent
 * powered (static)			_registerAppComponents
 * version (static)			_registerAppHelpers
 * getVersion (static)		_registerAppModules
 * t (static)				_hasEvent
 * te (static)				_hasEventHandler	
 * getComponent             _raiseEvent
 * getClientScript          
 * clientScript
 * getRequest
 * request
 * getLogger
 * logger
 * getLocalTime
 * localTime
 * getSession
 * session
 * getCookie
 * cookie
 * getShoppingCart
 * shoppingCart
 * getMobileDetect
 * mobileDetect
 * getUri
 * uri
 * attachEventHandler
 * detachEventHandler
 * mapCoreComponent
 * mapAppModule
 * mapAppModuleClass
 * setResponseCode
 * getResponseCode
 * setLanguage
 * getLanguage
 * setCurrency
 * getCurrency
 * isSetup
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
	private static $_frameworkVersion = '1.0.3';	
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
        'CConfig'		=> 'collections/CConfig.php',
        
        'CController'   => 'core/CController.php',
        'CDebug'        => 'core/CDebug.php',
        'CModel'        => 'core/CModel.php',
        'CRouter'       => 'core/CRouter.php',
        'CView'         => 'core/CView.php',
        
        'CActiveRecord' => array('5.2.0'=>'db/CActiveRecord.520.php', '5.3.0'=>'db/CActiveRecord.php'),
        'CDatabase'     => 'db/CDatabase.php',
        'CDataGrid'     => 'db/CDataGrid.php',
    );
    /** @var array */
    private static $_coreComponents = array(
		'component'    	=> array('class' => 'CComponent', 		'path' => array('5.2.0'=>'components/CComponent.520.php', '5.3.0'=>'components/CComponent.php')),
        'clientScript'	=> array('class' => 'CClientScript', 	'path' => 'components/CClientScript.php'),
        'dbSession' 	=> array('class' => 'CDbHttpSession', 	'path' => 'components/CDbHttpSession.php'),
        'request'   	=> array('class' => 'CHttpRequest', 	'path' => 'components/CHttpRequest.php'),
        'session'   	=> array('class' => 'CHttpSession', 	'path' => 'components/CHttpSession.php'),
        'cookie'		=> array('class' => 'CHttpCookie', 		'path' => 'components/CHttpCookie.php'),
        'localTime'		=> array('class' => 'CLocalTime', 		'path' => 'components/CLocalTime.php'),
		'logger'     	=> array('class' => 'CLogger', 			'path' => 'components/CLogger.php'),
        'coreMessages' 	=> array('class' => 'CMessageSource', 	'path' => 'components/CMessageSource.php',	'language' => 'en'),
		'messages' 		=> array('class' => 'CMessageSource', 	'path' => 'components/CMessageSource.php'),
		'mobileDetect' 	=> array('class' => 'CMobileDetect', 	'path' => 'components/CMobileDetect.php'),
		'shoppingCart' 	=> array('class' => 'CShoppingCart', 	'path' => 'components/CShoppingCart.php'),
		'uri'   		=> array('class' => 'CUri',				'path' => 'components/CUri.php'),
    );
    /** @var array */
    private static $_coreHelpers = array(
        'CArray'        => 'helpers/CArray.php',        
        'CAuth'         => 'helpers/CAuth.php',
        'CCache'        => 'helpers/CCache.php',
		'CConvert'      => 'helpers/CConvert.php',
        'CCurrency'     => 'helpers/CCurrency.php',
        'CFile'         => 'helpers/CFile.php',
        'CFilter'       => 'helpers/CFilter.php',
		'CGeoLocation'	=> 'helpers/CGeoLocation.php',
        'CHash'         => 'helpers/CHash.php',        
        'CHtml'         => 'helpers/CHtml.php',
        'CImage'        => 'helpers/CImage.php',
		'CLoader'       => 'helpers/CLoader.php',
		'CLocale'       => 'helpers/CLocale.php',
		'CLog'       	=> 'helpers/CLog.php',
        'CMailer'       => 'helpers/CMailer.php',
		'CMinify'       => 'helpers/CMinify.php',
        'CNumber'       => 'helpers/CNumber.php',
		'COauth'        => 'helpers/COauth.php',
		'CPdf'          => 'helpers/CPdf.php',
		'CRss'          => 'helpers/CRss.php',
		'CSoap'         => 'helpers/CSoap.php',
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
	private $_events = array();
	/** @var boolean */	
	private $_setup = false;
	/** @var string */
	private $_responseCode = '';
	/** @var string */
	private $_language = null;
	/** @var string */
	private $_currency = null;


    /**
     * Class constructor
     * @param array $configDir
     */
    public function __construct($configDir)
    {
    	spl_autoload_register(array($this, '_autoload'));        
        // Include interfaces
        require(dirname(__FILE__).DS.'core'.DS.'interfaces.php');    
        
		self::$_phpVersion = phpversion();
		 
        $configMain = $configDir.'main.php';
        $configDb = $configDir.'db.php';
        
        if(is_string($configMain) && is_string($configDb)){
            // Check if main configuration file exists
            if(!file_exists($configMain)){
                $arrConfig = array(
                    'template' => array('default' => 'setup'),
                    'defaultController' => 'Setup',
                    'defaultAction' => 'index',                                   
                );
                // Block access to regular files when application is not properly installed
                $url = isset($_GET['url']) ? $_GET['url'] : '';
                if(!preg_match('/setup\//i', $url)){
                    $_GET['url'] = 'setup/index';
                }
                $this->_setup = true;

                // Set default timezone
                date_default_timezone_set('UTC');
            }else{
                $arrConfig = require($configMain);
                // Check if db configuration file exists and marge it with a main config file
                if(file_exists($configDb)){
                    $arrDbConfig = require($configDb);
                    $arrConfig = array_merge($arrConfig, $arrDbConfig);
                    
                    // Check if modules configuration files exist and marge its with a main config file
                    foreach($arrConfig['modules'] as $module => $moduleInfo){
                        $configFile = APPHP_PATH.'/protected/config/'.$module.'.php';
                        if(file_exists($configFile)){
                            $configFileContent = include($configFile);
                            // Merge rules settings
                            if(isset($configFileContent['urlManager']['rules'])){
                                $tempConfig = $configFileContent['urlManager']['rules'];
                                $arrConfig['urlManager']['rules'] = array_merge($arrConfig['urlManager']['rules'], $tempConfig);
                            }
                            // Merge components settings
                            if(isset($configFileContent['components'])){
                                $tempConfig = $configFileContent['components'];
                                // Add module identificator to component
                                foreach($tempConfig as $key => $val){                                    
                                    $tempConfig[$key]['module'] = $module;
                                }
                                $arrConfig['components'] = array_merge($arrConfig['components'], $tempConfig);
                            }
                            // Override default Controller/Action settings
                            if(isset($configFileContent['defaultController']) && isset($configFileContent['defaultAction'])){
								$arrConfig['defaultController'] = ucfirst($configFileContent['defaultController']);
								$arrConfig['defaultAction'] = $configFileContent['defaultAction'];
							}
                            // Override default ErrorController settings
                            if(isset($configFileContent['defaultErrorController'])){
								$arrConfig['defaultErrorController'] = ucfirst($configFileContent['defaultErrorController']);
							}
                            // Override default payment complete page settings
                            if(isset($configFileContent['paymentCompletePage'])){
								$arrConfig['paymentCompletePage'] = $configFileContent['paymentCompletePage'];
							}
                            // Override backend default URL setings if such settings doesn't exist in config/main.php
							if(isset($configFileContent['backendDefaultUrl']) && empty($arrConfig['modules'][$module]['backendDefaultUrl'])){
								$arrConfig['modules'][$module]['backendDefaultUrl'] = $configFileContent['backendDefaultUrl'];
							}                            
                        }
                    }
                }
            }
            
            // Save configuration array in config class
            CConfig::load($arrConfig);
        }
    }	
 
    /**
     * Runs application
     */
    public function run()
    {
        if(APPHP_MODE != 'hidden'){
            // Specify error settings
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
                // To prevent 'Web Page exired' message on using submission method 'POST'
                session_cache_limiter('private, must-revalidate');    
            }
    
            // Initialize Debug class
            CDebug::init(); 
            
            // Load view (must do it before app components registration)
            $this->view = new CView(); 
			$this->view->setTemplate(CConfig::get('template.default'));
        }
        
		// Run application and global debug backtrace
		if(CConfig::get('exceptionHandling.enable') && CConfig::get('exceptionHandling.level') === 'global'){
			try{
				$this->_runApp();
			}catch(Exception $e){
				echo CDebug::backtrace($e->getMessage(), $e->getTrace());
				exit;
			}
		}else{
			$this->_runApp();
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
     * Alias to getVersion
     * @SEE getVersion()
     * Returns the version of ApPHP framework
     * @return string 
     */
    public static function version()
    {
    	return self::getVersion();
    }

    /**
     * Returns the version of ApPHP framework
     * @return string 
     */
    public static function getVersion()
    {
    	return self::$_frameworkVersion;
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
    	if(self::$_instance !== null && $message !== ''){
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
     * Translates a message to the specified language with encoded output (used htmlspecialchars() function)
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $source
     * @param string $language
     * @return string 
     */
    public static function te($category = 'app', $message = '', $params = array(), $source = null, $language = null)
    {
		return CHtml::encode(self::t($category, $message, $params, $source, $language));
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
			// Check if we need PHP version compatible class
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
		elseif(isset(self::$_coreHelpers[$className])){            
			$coreHelper = dirname(__FILE__).DS.self::$_coreHelpers[$className];
			$extCoreHelper = APPHP_PATH.DS.'protected'.DS.self::$_coreHelpers[$className];
			// Check if there extension exists in application
			if(is_file($extCoreHelper)){
				include($extCoreHelper);
			}else{
				include($coreHelper);
			}		
        }
		// Framework: COMPONENT CLASSES
		elseif($coreComponent = $this->mapCoreComponent($className)){			
			include(dirname(__FILE__).DS.$coreComponent);
        }

		// Application: COMPONENT CLASSES
		elseif(isset(self::$_appClasses[$className])){            
            include(APPHP_PATH.DS.'protected'.DS.self::$_appClasses[$className]);
        }
		// Application: HELPER CLASSES
		elseif(isset(self::$_coreHelpers[$className])){
			include(APPHP_PATH.DS.'protected'.DS.self::$_appHelpers[$className]);
		}
		 
		// Check if required class is Controller or Model (in application or modules)
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
            
            // Use model mapping pattern for classes AaaBbbCcc
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
					// Look for class if namespacing is used (from v0.8.0)
					$namespace = explode('\\', $className);
					if(count($namespace) > 1){
                        $fileName = array_pop($namespace);
						$classFile = APPHP_PATH.DS.'protected'.DS.implode('/', array_map('strtolower', $namespace)).'/'.$fileName.'.php';
					}else{
						$classModuleDir = APPHP_PATH.DS.'protected'.DS.$this->mapAppModule($pureClassName).self::$_classMap[$pureClassType];
						$classFile = $classModuleDir.DS.$className.'.php';
					}
					
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
				
				if(!empty($className)){
					CDebug::addMessage('general', 'classes', $className);	
				}
            }
        }        
    }    

    /**
     * Run application 
     * @return void
     */
    private function _runApp()
    {
		// Register framework core components
		$this->_registerCoreComponents();

		// Global test for database
		if(CConfig::get('db.driver') != ''){
			$db = CDatabase::init();
			if(!CAuth::isGuest()) $db->cacheOff();
		}
   
		// Register application components
		$this->_registerAppComponents();
		// Register application helpers
		$this->_registerAppHelpers();
		// Register application modules
		$this->_registerAppModules();	
		
		// Run begin events
		if($this->_hasEventHandler('_onBeginRequest')) $this->_onBeginRequest();
		
		if(APPHP_MODE != 'hidden'){
			$this->router = new CRouter();
			$this->router->route();
			// Run finish events
			if($this->_hasEventHandler('_onEndRequest')) $this->_onEndRequest();
			// Show debug bar
			CDebug::displayInfo();
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
            // For PHP_VERSION | phpversion() >= 5.3.0 you may use
            // $this->_components[$id] = $component::init();
            if($callback = call_user_func_array($component.'::init', array())){
                $this->_components[$id] = $callback;    
            }elseif(!in_array($component, array('CComponent'))){
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
     * Returns the logger component
     * @return CLogger component
     */
    public function getLogger()
    {
    	return $this->getComponent('logger');
    }

    /**
     * Alias to getLogger
     * @SEE getLogger()
     * @return CLogger component
     */
    public function logger()
    {
    	return $this->getLogger();
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
     * Returns the shopping cart component
     * @return CShoppingCart component
     */
    public function getShoppingCart()
    {
    	return $this->getComponent('shoppingCart');
    }

    /**
     * Alias to getShoppingCart
     * @SEE getShoppingCart()
     * @return CShoppingCart component
     */
    public function shoppingCart()
    {
    	return $this->getShoppingCart();
    }

    /**
     * Alias to getMobileDetect
     * @SEE getMobileDetect()
     * @USAGE
	 * 	mobileDetect()->isMobile()
	 * 	mobileDetect()->isTablet()
     * 
     * @return CMobileDetect component
     */
    public function mobileDetect()
    {
    	return $this->getMobileDetect();
    }

    /**
     * Returns the mobile detect component
     * @return CMobileDetect component
     */
    public function getMobileDetect()
    {
    	return $this->getComponent('mobileDetect');
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
    protected function _hasEventHandler($name)
    {
    	$name = strtolower($name);
    	return isset($this->_events[$name]) && count($this->_events[$name]) > 0;
    }

    /**
     * Raises an event
     * @param string $name 
     */
    protected function _raiseEvent($name)
    {
        $name = strtolower($name);
        if(isset($this->_events[$name])){
            foreach($this->_events[$name] as $handler){
                if(is_string($handler[1])){
					$object = $handler[0];
					$method = $handler[1];
					if(is_string($object)){
						@call_user_func_array(array($object, $method), array());
					}elseif(method_exists($object, $method)){
						$object->$method();
					}
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
					// Check if we need PHP version compatible class
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
			// No classes found - continue
            if(!isset($moduleInfo['classes']) || !is_array($moduleInfo['classes'])) continue;
			// Find class whether it has namesape or not 
			foreach($moduleInfo['classes'] as $key => $moduleClass){
				$namespace = explode('\\', $moduleClass);
				$compareClass = (count($namespace) > 1) ? array_pop($namespace) : $moduleClass;
				if(strtolower($class) == strtolower($compareClass)){
					$path = 'modules/'.$module.'/';
					break 2;
				}
			}
        }
        
		return $path;
    }

    /**
     * Maps application modules classes
     * @param string $class
     * @return string
     */
    public function mapAppModuleClass($class)
    {
		$classFullPath = '';
        foreach(self::$_appModules as $module => $moduleInfo){
			// No classes found - continue
            if(!isset($moduleInfo['classes']) || !is_array($moduleInfo['classes'])) continue;
			// Find class whether it has namesape or not 
			foreach($moduleInfo['classes'] as $key => $moduleClass){
				$namespace = explode('\\', $moduleClass);
				$compareClass = (count($namespace) > 1) ? array_pop($namespace) : $moduleClass;
				if(strtolower($class) == strtolower($compareClass)){
					$classFullPath = $moduleClass;
					break 2;
				}
			}
        }
        
		return $classFullPath;
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
		
		if(isset($params['name'])) $this->getSession()->set('language_name', $params['name']);
		if(isset($params['name_native'])) $this->getSession()->set('language_name_native', $params['name_native']);
        if(isset($params['locale'])){
            $this->getSession()->set('language_locale', $params['locale']);
            if(!setlocale(LC_ALL, $params['locale'])) CDebug::addMessage('warnings', 'missing-locale', A::t('core', 'Unable to find locale "{locale}" on your server.', array('{locale}'=>$params['locale'])), 'session');
        }
        if(isset($params['direction'])) $this->getSession()->set('language_direction', $params['direction']);
		if(isset($params['icon'])) $this->getSession()->set('language_icon', $params['icon']);
    }

    /**
     * Returns the language that is used for application or language parameter
     * @param string $param
     * @param bool $useDefault
     * @return string 
     */
    public function getLanguage($param = '', $useDefault = true)
    {
        $language = $this->getSession()->get('language');
        if(!empty($param)){
			return $this->getSession()->get('language_'.$param);
		}elseif(!empty($language)){
            return $language;
        }elseif($this->_language === null && $useDefault){ 
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
		
		if(isset($params['name'])) $this->getSession()->set('currency_name', $params['name']);
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
	 * Returns current status (if setup or not)
	 * @return bool
	 */
	public function isSetup()
	{
		return $this->_setup;
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
            }elseif(!CConfig::get('session.customStorage') && $id == 'dbSession'){
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
     * Raised after the application processes the request
     */
    protected function _onEndRequest()
    {
    	$this->_raiseEvent('_onEndRequest');
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
				if(!preg_match('/Component/i', $class)){
					$this->_setComponent($id, $class);	
				}
            }
        }
    }
	
    /**
     * Registers application helpers
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
                $moduleConfig = APPHP_PATH.DS.'protected'.DS.'modules'.DS.$moduleName.DS.'config'.DS.'main.php';
                if(file_exists($moduleConfig)){
                    $arrConfig = include_once($moduleConfig);
                    self::$_appModules[$moduleName] = $arrConfig;
                }
            }
        }
    }
    
}
