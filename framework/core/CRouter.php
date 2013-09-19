<?php
/**
 * CRouter core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
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
 *           CONFIG	: 'urlFormat'=>'shortPath' (default)
 *           CALL	: $controller->$action($param1, $param2, $param3);
 *           GET	: actionName($param1 = '', $param2 = '', $param3 = '')
 *           FILTER	: automatically according to define type (not implemented yet)
 * 
 *           
 * 
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * route
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * getParams
 * 
 */	  

class CRouter
{
	/**	@var string */
	private $_path; 
	/**	@var string */
	private $_controller; 
	/**	@var string */
	private $_action; 
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
		
		// check if there are special URL rules 
		if($urlFormat == 'shortPath' && is_array($rules)){
			foreach($rules as $rule => $val){
				$matches = '';
				//if($rule === $request){
				//    $request = $val;
				//	break;
				//}else
				if(preg_match_all('/'.$rule.'/i', $request, $matches)){
					// template rule compare
					if(isset($matches[1]) && is_array($matches[1])){
						foreach($matches[1] as $mkey => $mval){
							$val = str_ireplace('{$'.$mkey.'}', $mval, $val);
						}
						$request = $val;
						break;
					}
				}
			}
			
			// if not found - use a standard way
			$urlFormat = '';
		}		
	
		if($standardCheck){			
			$split = explode('/', trim($request, '/'));
			if($split){
				foreach($split as $index => $part){
					if(!$this->_controller){
						$this->_controller = ucfirst($part);					
						CDebug::addMessage('params', 'controller', $this->_controller);
					}else if(!$this->_action){
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
		if(!$this->_controller){
			$defaultController = CConfig::get('defaultController');
			$this->_controller = !empty($defaultController) ? CFilter::sanitize('alphanumeric', $defaultController) : 'Index'; 
		}	
		if(!$this->_action){
			$defaultAction = CConfig::get('defaultAction');
			$this->_action = !empty($defaultAction) ? CFilter::sanitize('alphanumeric', $defaultAction) : 'index'; 
		}
	}	
 
	/**	 
	 * Router
	 */
	public function route()
	{
        $appDir = APPHP_PATH.DS.'protected'.DS.'controllers'.DS;
        $file = $this->_controller.'Controller.php';

		if(is_file($appDir.$file)){
			$class = $this->_controller.'Controller';
        }else{
            $comDir = APPHP_PATH.DS.'protected'.DS.A::app()->mapAppModule($this->_controller).'controllers'.DS;
            if(is_file($comDir.$file)){
                $class = $this->_controller.'Controller';
            }else{
            	$class = 'ErrorController';
                A::app()->setResponseCode('404');
            	CDebug::addMessage('errors', 'controller', A::t('core', 'Router: unable to resolve the request "{controller}".', array('{controller}' => $this->_controller)));
            }
        } 
		A::app()->view->setController($this->_controller);    
		$controller = new $class();

		if(is_callable(array($controller, $this->_action.'Action'))){
			$action = $this->_action.'Action';
		}else if($class != 'ErrorController'){
			// for non-logged users and classes where errorAction was not redeclared - force using standard 404 error controller
			$reflector = new ReflectionMethod($class, 'errorAction');
			if(!CAuth::isLoggedIn() && $reflector->getDeclaringClass()->getName() == 'CController'){
				$controller = new ErrorController();
				$action = 'indexAction';				
			}else{
				$action = 'errorAction';	
			}			
            CDebug::addMessage('errors', 'action', A::t('core', 'The system is unable to find the requested action "{action}".', array('{action}' => $this->_action)));
		}else{
			$action = 'indexAction';
		}
		
		A::app()->view->setAction($this->_action);
        
		// call controller::action + pass parameters
		call_user_func_array(array($controller, $action), $this->getParams());		 

		CDebug::addMessage('params', 'run_controller', $class);
		CDebug::addMessage('params', 'run_action', $action);		
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