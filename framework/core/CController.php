<?php
/**
 * CController base class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 * 
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct                                          _getCalledClass
 * testAction
 * errorAction
 * redirect
 * 
 * STATIC:
 * ---------------------------------------------------------------
 *
 */	  

class CController
{
	/** @var string */		
    protected $_view;

	/**
	 * Class constructor
	 * @return void
	 */
	function __construct()
	{
		$this->_view = A::app()->view;
	}
    
	/**
	 * Renders test action
	 */
	public function testAction()
	{
		if(APPHP_MODE == 'test'){            
			$controller = $this->_getCalledClass();
			if($controller.'/index' == $this->_view->render($controller.'/index')){
				return true; 
			}else{
				return false; 
			}
		}else{
			$this->redirect('error/index');
		}
    }

	/**
	 * Renders error 404 view
	 */
	public function errorAction()
	{
        $this->_view->header = 'Error 404';
        $this->_view->text = '';

        $errors = CDebug::getMessage('errors', 'action');
        if(is_array($errors)){
			foreach($errors as $error){
				$this->_view->text .= $error;		    
			}        
		}
        $this->_view->render('error/index');        
    }

	/**
	 * Redirects to another controller
	 * Parameter may consist from 2 parts: controller/action or just controller name
	 * @param string $path
	 */
    public function redirect($path)
	{
		if(APPHP_MODE == 'test') return true;
        
		$paramsParts = explode('/', $path);
		$calledController = str_replace('controller', '', strtolower($this->_getCalledClass()));
		$params = '';
		$baseUrl = A::app()->getRequest()->getBaseUrl();
		
		// set controller and action according to given parameters
		if(!empty($path)){
			$parts = count($paramsParts);
			if($parts == 1){
				$controller = $calledController;
				$action = isset($paramsParts[0]) ? $paramsParts[0] : '';
			}else if($parts == 2){
				$controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
				$action = isset($paramsParts[1]) ? $paramsParts[1] : '';
			}else if($parts > 2){
				$controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
				$action = isset($paramsParts[1]) ? $paramsParts[1] : '';
				for($i=2; $i<$parts; $i++){
					$params .= (isset($paramsParts[$i]) ? '/'.$paramsParts[$i] : '');
				}
			}
		}
                
        // close the session with user data
        A::app()->getSession()->closeSession();

        // perform redirection
        header('location: '.$baseUrl.$controller.'/'.$action.$params);
        exit;
    }
 
	/**
	 * Returns the name of called class
	 * @return string|bool
	 */
	private function _getCalledClass()
	{
		if(function_exists('get_called_class')) return get_called_class();
		$bt = debug_backtrace();
		if(!isset($bt[1])){
			return false; // cannot find called class -> stack level too deep
		}else if(!isset($bt[1]['type'])){
			return false; // type not set
		}else switch ($bt[1]['type']) { 
			case '::': 
				$lines = file($bt[1]['file']); 
				$i = 0; 
				$callerLine = ''; 
				do{ 
					$i++; 
					$callerLine = $lines[$bt[1]['line']-$i] . $callerLine; 
				}while (stripos($callerLine,$bt[1]['function']) === false); 
				preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/', $callerLine, $matches); 
				if(!isset($matches[1])){ 					
					return false; // could not find caller class: originating method call is obscured
				}
				return $matches[1]; 
				break;
			case '->': switch ($bt[1]['function']) { 
					case '__get': 
						// edge case -> get class of calling object 
						if(!is_object($bt[1]['object'])){							
							return false; // edge case fail. __get called on non object
						}
						return get_class($bt[1]['object']); 
					default: return $bt[1]['class']; 
				}
				break;
			default:
				// unknown backtrace method type
				return false;
				break;
		}
		return false;
	}	
    
}