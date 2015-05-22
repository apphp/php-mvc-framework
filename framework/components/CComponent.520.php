<?php
/**
 * CComponent is the base class for all components
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 * @version PHP 5.2.0 - 5.3.0
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct											
 * init (static)
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
	
	/**
	 * Initializes the class
	 * @param array $className
	 * @version PHP 5.2.0 - 5.3.0
	 */
	public static function init($className = __CLASS__)
	{
		if(isset(self::$_components[$className])){
			return self::$_components[$className];
		}else{
			return self::$_components[$className] = new $className(null);
		}        
    }

}