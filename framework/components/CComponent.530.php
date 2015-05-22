<?php
/**
 * CComponent is the base class for all components
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 * @version PHP 5.3.0 or higer
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct											_parentInit (static)
 * __callStatic (static)
 *
 */	  

class CComponent
{

    /* class name => component */
    private static $_components = array();

	/**
	 * Class constructor
	 * @return void
	 */
	function __construct()
	{
		
	}
  
	/**
	 * Triggered when invoking inaccessible methods in an object context
	 * We use this method to avoid calling model($className = __CLASS__) in derived class
	 * @param string $method
	 * @param array $args
	 * @version PHP 5.3.0 or higher
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		if(strtolower($method) == 'init'){
			if(count($args) == 1){
				return self::_parentInit($args[0]);
			}
		}		
	}

	/**
	 * Returns the static component of the specified class
	 * @param string $className
	 * 
	 * EVERY derived component class must override this method in following way,
	 * <pre>
	 * public static function init()
	 * {
	 *     return parent::init(__CLASS__);
	 * }
	 * </pre>
	 */
	private static function _parentInit($className = __CLASS__)
	{
		if(isset(self::$_components[$className])){
			return self::$_components[$className];
		}else{
			return self::$_components[$className] = new $className(null);
		}        
    }

}