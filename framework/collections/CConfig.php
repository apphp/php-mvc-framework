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
 * load
 * set
 * get
 * exists
 *
 */	  

class CConfig
{   
	/** @var array */	
	private static $_conf;


 	/**
 	 * Loads config parameters
 	 * @param array $config
 	 * @return void
 	 */
	public static function load($config)
 	{
		self::$_conf = $config;
		//json_decode(json_encode($arr));
 	}

 	/**
 	 * Sets config parameters
 	 * @param string $param
 	 * @param mixed $value
 	 * @return void
 	 */
  	public static function set($param = '', $value = '')
 	{
		if(!empty($param)){
			$paramParts = explode('.', $param);
			$parts = count($paramParts);
			if($parts == 1){
				if(isset(self::$_conf[$paramParts[0]])){
					self::$_conf[$paramParts[0]] = $value;
				}
			}else if($parts == 2){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]])){
					self::$_conf[$paramParts[0]][$paramParts[1]] = $value;
				}
			}else if($parts == 3){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]][$paramParts[2]])){
					self::$_conf[$paramParts[0]][$paramParts[1]][$paramParts[2]] = $value;
				}
			}
		}			
    }

 	/**
 	 * Get config parameters
 	 * @param string $param
 	 * @param mixed $default
 	 * @return mixed
 	 */
  	public static function get($param = '', $default = '')
 	{
		$result = '';
        
		if(!empty($param)){
			$paramParts = explode('.', $param);
			$parts = count($paramParts);
			if($parts == 1){
				if(isset(self::$_conf[$paramParts[0]])){
					$result = self::$_conf[$paramParts[0]];
				}
			}else if($parts == 2){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]])){
					$result = self::$_conf[$paramParts[0]][$paramParts[1]];
				}
			}else if($parts == 3){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]][$paramParts[2]])){
					$result = self::$_conf[$paramParts[0]][$paramParts[1]][$paramParts[2]];
				}
			}
		}			
		
		return (empty($result) && !empty($default)) ? $default : $result;
 	}

 	/**
 	 * Check if config parameter exists
 	 * @param string $param
 	 * @return mixed
 	 */
  	public static function exists($param = '')
 	{
		$result = false;
        
		if(!empty($param)){
			$paramParts = explode('.', $param);
			$parts = count($paramParts);
			if($parts == 1){
				if(isset(self::$_conf[$paramParts[0]])){
					$result = true;
				}
			}else if($parts == 2){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]])){
					$result = true;
				}
			}else if($parts == 3){
				if(isset(self::$_conf[$paramParts[0]][$paramParts[1]][$paramParts[2]])){
					$result = true;
				}
			}
		}			
		
		return $result;
 	}
	
}