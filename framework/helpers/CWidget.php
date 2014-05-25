<?php
/**
 * CWidget is a helper class file that represents base (factory) class for all widgets classes
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * create
 * 
 */	  

class CWidget
{

    /**
	 * Creates appropriate widget
	 * @param string $className
	 * @param array $params
	 */
	public static function create($className, $params = array())
    {
		include_once('widgets/'.$className.'.php');

        if(!class_exists($className)){
            CDebug::addMessage('warnings', 'missing-helper', A::t('core', 'Cannot find widget class: {class}', array('{class}'=>$className)));    
        }else{
            // for PHP_VERSION >= 5.3.0 you may use
            // $result = $className::init($params);			
			if(strtolower($className) == 'cmessage'){
				$result = @call_user_func_array($className.'::init', $params);
			}else{
				// params is assosiative array
				$result = @call_user_func($className.'::init', $params);
			}
			return $result;
		}
    }
  
}