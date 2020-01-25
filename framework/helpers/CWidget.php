<?php
/**
 * CWidget is a helper class file that represents base (factory) class for all widgets classes
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
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
		include_once('widgets/CWidgs.php');
		include_once('widgets/' . $className . '.php');
		
		if (!CClass::isExists($className)) {
			CDebug::addMessage('warnings', 'missing-helper', A::t('core', 'Cannot find widget class: {class}', array('{class}' => $className)));
		} else {
			// Init
			if (strtolower($className) == 'cmessage') {
				$type = isset($params[0]) ? $params[0] : '';
				$text = isset($params[1]) ? $params[1] : '';
				$init_params = isset($params[2]) ? $params[2] : array();
				$result = $className::init($type, $text, $init_params);
			} else {
				$result = $className::init($params);
			}
			/// DEPRECATED 01.12.2018 - for PHP_VERSION | phpversion() < 5.3.0
			// if(strtolower($className) == 'cmessage'){
			//	 $result = call_user_func_array($className.'::init', $params);
			// }else{
			//	 // Params is assosiative array
			//	 $result = call_user_func($className.'::init', $params);
			// }
			return $result;
		}
	}
	
}
