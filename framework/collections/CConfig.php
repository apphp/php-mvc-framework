<?php
/**
 * CConfig core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * set
 * get
 *
 */	  

class CConfig
{   
	/** @var array */	
	private static $_conf;


 	/**
 	 * Sets config parameters
 	 * @param array $config
 	 * @return void
 	 */
	public static function set($config)
 	{
		self::$_conf = $config;
		//json_decode(json_encode($arr));
 	}

 	/**
 	 * Get config parameters
 	 * @param string $params
 	 * @param mixed $default
 	 * @return mixed
 	 */
  	public static function get($params, $default = '')
 	{
		$result = '';
		if(!empty($params)){
			$paramsParts = explode('.', $params);
			$parts = count($paramsParts);
			if($parts == 1){
				if(isset(self::$_conf[$paramsParts[0]])){
					$result = self::$_conf[$paramsParts[0]];
				}
			}else if($parts == 2){
				if(isset(self::$_conf[$paramsParts[0]][$paramsParts[1]])){
					$result = self::$_conf[$paramsParts[0]][$paramsParts[1]];
				}
			}else if($parts == 3){
				if(isset(self::$_conf[$paramsParts[0]][$paramsParts[1]][$paramsParts[2]])){
					$result = self::$_conf[$paramsParts[0]][$paramsParts[1]][$paramsParts[2]];
				}
			}
		}			
		
		return (empty($result) && !empty($default)) ? $default : $result;
 	}
	
}