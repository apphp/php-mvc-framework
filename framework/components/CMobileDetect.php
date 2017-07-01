<?php
/**
 * CMobileDetect provides work with mobile devices and browsers
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * init (static)
 *
 * USAGE:
 * 	->isMobile()
 * 	->isTablet()
 * 
 */	  

include(dirname(__FILE__).'/../vendors/mobiledetect/mobile_detect.php');

class CMobileDetect extends CComponent
{
	/** @var Mobile_detect */
	static private $_mobileDetect = null;

    
    /**
	 * Class default constructor
	 */
	function __construct()
	{

    }

    /**
     *	Returns the instance of object
     *	@return current class
     */
	public static function init()
	{
		if(self::$_mobileDetect == null){
			self::$_mobileDetect = new Mobile_Detect();	
		}
		
		return self::$_mobileDetect;
	}    

}