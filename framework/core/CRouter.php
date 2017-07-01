<?php
/**
 * CRouter core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 *
 * USAGE:
 * ----------
 * 1st way - URL	: http://localhost/site/index.php?url=page/contact&param1=aaa&param2=bbb&param3=ccc
 *           CONFIG : 'urlFormat'=>'get' (default)
 *			 CALL	: $controller->$action();
 *			 GET	: A::app()->getRequest()->getQuery('param1');
 *			 FILTER	: manually in code
 * 2st way - URL	: http://localhost/site/page/contact?param1=aaa&param2=bbb&param3=ccc
 *           CONFIG	: 'urlFormat'=>'get' (default)
 *			 CALL	: $controller->$action();
 *           GET	: A::app()->getRequest()->getQuery('param1');
 *           FILTER	: manually in code
 * 3st way - URL	: http://localhost/site/page/contact/param1/aaa/param2/bbb/param3/ccc
 *           CONFIG	: 'urlFormat'=>'path' (default)
 *           CALL	: $controller->$action($param1, $param2, $param3);
 *           GET	: actionName($param1 = '', $param2 = '', $param3 = '')
 *           FILTER	: manually in code
 * 4st way - URL	: according to redirection rule
 * 				  		- simple redirection rule:  				    
 *                  	  'controller/action/value1/value2' => 'controller/action/param1/value1/param2/value2',
 *                  	- advanced redirection rule:
 *                  	  'index\/page\/id\/(.*[0-9])+' => 'index/page/id/{$0}',
 *                  	  'index\/page\/(.*[0-9])+' => 'index/page/id/{$0}',
 *                  	  'index\/page\/(.*[0-9])+\/(.*?)' => 'index/page/id/{$0}',
 *                  	  'index\/page\/(.*[0-9])+\/(.*?)/(.*)' => 'index/page/id/{$0}/p1/{$1}/p2/{$2}',
 *           CONFIG	: 'urlFormat'=>'shortPath' (default)
 *           CALL	: $controller->$action($param1, $param2, $param3);
 *           GET	: actionName($param1 = '', $param2 = '', $param3 = '')
 *           FILTER	: automatically according to define type (not implemented yet)
 * 
 *           
 * 
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ---------------         	---------------            	---------------
 * __construct
 * route
 * getCurrentUrl
 * getParams (static)
 * 
 */	  

///namespace Framework\Core; 
 
class CRouter
{
	/**	@var string */
	private $_path; 
	/**	@var string */
	private $_controller; 
	/**	@var string */
	private $_action; 
	/**	@var string */
	private $_defaultController = 'Index'; 
	/**	@var string */
	private $_defaultAction = 'index'; 
	/**	@var string */
	private $_module; 
	/** @var array */
	private static $_params = array();
 

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$urlFormat = CConfig::get('urlManager.urlFormat');
		$rules = (array)CConfig::get('urlManager.rules');
		
		$request = isset($_GET['url']) ? $_GET['url'] : '';
		$standardCheck = true;
		
		// Check if there are special URL rules 
		if($urlFormat == 'shortPath' && is_array($rules)){
			foreach($rules as $rule => $val){
				$matches = '';
				//if($rule === $request){
				//    $request = $val;
				//	break;
				//}else
				if(preg_match_all('{'.$rule.'}i', $request, $matches)){
					// Remove first match (the full string)
					array_shift($matches);
					// Template rule compare
					if(is_array($matches)){
						foreach($matches as $mkey => $mval){
							if(isset($mval[0])){
								$val = str_ireplace('{$'.$mkey.'}', $mval[0], $val);	
							}							
						}
						$request = $val;
						break;
					}
				}
			}
			
			// If not found - use a standard way
			$urlFormat = '';
		}		
	
		if($standardCheck){			
			$split = explode('/', trim($request, '/'));
			if($split){
				foreach($split as $index => $part){
					if(!$this->_controller){
						$this->_controller = ucfirst($part);					
						CDebug::addMessage('params', 'controller', $this->_controller);
					}elseif(!$this->_action){
						$this->_action = $part;			
						CDebug::addMessage('params', 'action', $this->_action);
					}else{					
						if(!self::$_params || end(self::$_params) !== null){
							self::$_params[$part] = null;																
						}else{
                            $arrayArg = array_keys(self::$_params);
							self::$_params[end($arrayArg)] = $part;
						}
						CDebug::addMessage('params', 'params', print_r(self::$_params, true));					
					}
				}			
			}
		}			

		$defaultController = CConfig::get('defaultController');
		$defaultAction = CConfig::get('defaultAction');
		// There is no controller - use default controller/action setings
		if(!$this->_controller){
			$this->_controller = !empty($defaultController) ? CFilter::sanitize('alphanumeric', $defaultController) : $this->_defaultController;
			$this->_action = !empty($defaultAction) ? CFilter::sanitize('alphanumeric', $defaultAction) : $this->_defaultAction; 
		}
		// There is a controller, but no action - use default action setings
		elseif($this->_controller && !$this->_action){
			if($this->_controller == $defaultController){
				$this->_action = !empty($defaultAction) ? CFilter::sanitize('alphanumeric', $defaultAction) : $this->_defaultAction; 	
			}else{
				$this->_action = $this->_defaultAction; 	
			}			
		}
	}	
 
	/**	 
	 * Router
	 */
	public function route()
	{
        $appDir = APPHP_PATH.DS.'protected'.DS.'controllers'.DS;
        $file = $this->_controller.'Controller.php';
		$errorClass = '';
		// Get default error controller
		$errorController = CConfig::get('defaultErrorController', 'Error');
		$errorController = preg_replace('/controller/i', '', $errorController);

		if(is_file($appDir.$file)){
			// Framework Controller
			$class = $this->_controller.'Controller';
        }else{
			$modulePath = A::app()->mapAppModule($this->_controller);
            $moduleDir = APPHP_PATH.DS.'protected'.DS.$modulePath.'controllers'.DS;
			$classWithNamespace = A::app()->mapAppModuleClass($this->_controller);
            if(!empty($classWithNamespace)){
				// Module Controller with namespace (new syntax in framework >= v0.8.0)
				$class = A::app()->mapAppModuleClass($this->_controller).'Controller';
			}elseif(is_file($moduleDir.$file)){
				// Module Controller
                $class = $this->_controller.'Controller';
            }else{
				$errorClass = A::app()->mapAppModuleClass($errorController);
				if(!empty($errorClass)){
					$class = $errorClass.'Controller';
				}else{
					$class = $errorController.'Controller';
				}
				
                A::app()->setResponseCode('404');
            	CDebug::addMessage('errors', 'controller', A::t('core', 'Router: unable to resolve the request "{controller}".', array('{controller}' => $this->_controller)));
            }
        } 
		A::app()->view->setController(($class == $errorController ? 'Error' : $this->_controller));
		$controller = new $class();

		if(is_callable(array($controller, $this->_action.'Action'))){
			$action = $this->_action.'Action';
		}elseif($class != $errorController){
			// For non-logged users and classes where errorAction was not redeclared - force using standard 404 error controller
			$reflector = new ReflectionMethod($class, 'errorAction');
			if(!CAuth::isLoggedIn() && $reflector->getDeclaringClass()->getName() == 'CController'){
				$errorClass = A::app()->mapAppModuleClass($errorController);
				if(!empty($errorClass)){
					$errorClass .= 'Controller';
					$controller = new $errorClass();
				}else{
					$errorController .= 'Controller';
					$controller = new $errorController();
				}				
				
				$action = 'indexAction';				
			}else{
				$action = 'errorAction';	
			}			
            CDebug::addMessage('errors', 'action', A::t('core', 'The system is unable to find the requested action "{action}".', array('{action}' => $this->_action)));
		}else{
			$action = 'indexAction';
		}		
		A::app()->view->setAction(($action == 'errorAction' ? 'error' : $this->_action));
        
		// Call controller::action + pass parameters
		call_user_func_array(array($controller, $action), self::getParams());		 

		CDebug::addMessage('params', 'running_controller', (!empty($errorClass) ? $errorClass : $class));
		CDebug::addMessage('params', 'running_action', $action);		
	}
    
 	/**	 
	 * Returns current URL
	 * @return string 
	 */
	public function getCurrentUrl()
	{
        $path = A::app()->getRequest()->getBaseUrl();
        $path .= strtolower(A::app()->view->getController()).'/';
        $path .= A::app()->view->getAction();
        
        $params = self::getParams();
        foreach($params as $key => $val){
            $path .= '/'.$key.'/'.$val;    
        }
        
        return $path;
    }   
 
	/**
	 * Get array of parameters
	 * @return array
	 */
	public static function getParams()
	{
		return self::$_params;
	}
 
}