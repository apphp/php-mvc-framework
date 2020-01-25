<?php
/**
 * CClass is a helper class that provides a set of helper methods for common class operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * isExists
 * isMethodCallable
 * isMethodExists
 *
 */

class CClass
{
	
	/**
	 * Checks if class exists.
	 * This is an alternative for PHP class_exists() function to prevent issues with __autoload function
	 * @param string $className
	 * @param bool $autoload
	 * @return bool
	 * @since 1.2
	 */
	public static function isExists($className, $autoload = false)
	{
		if (empty($className)) {
			return false;
		}
		
		return class_exists($className, $autoload);
	}
	
	/**
	 * Checks if class method is callable
	 * @param string $className
	 * @param string $methodName
	 * @return bool
	 * @since 1.2
	 */
	public static function isMethodCallable($className, $methodName)
	{
		if (empty($className) || empty($methodName)) {
			return false;
		}
		
		return is_callable(array($className, $methodName));
	}
	
	/**
	 * Checks if class method exists.
	 * @param string $className
	 * @param string $methodName
	 * @return bool
	 * @since 1.2
	 */
	public static function isMethodExists($className, $methodName)
	{
		if (empty($className) || empty($methodName)) {
			return false;
		}
		
		return method_exists($className, $methodName);
	}
	
}
