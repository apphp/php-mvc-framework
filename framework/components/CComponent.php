<?php
/**
 * CComponent is the base class for all components
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ---------- 
 * __construct                                          
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init
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
	public static function init($className = __CLASS__)
	{
		if(isset(self::$_components[$className])){
			return self::$_components[$className];
		}else{
			return self::$_components[$className] = new $className(null);
		}        
    }

}