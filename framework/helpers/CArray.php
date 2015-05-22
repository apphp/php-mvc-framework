<?php
/**
 * CArray is a helper class that provides a set of helper methods for common array operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * groupByValue
 * 
 */	  

class CArray
{

	/**
	 * Groups array by sub array value
	 * [0] => array {
     *      'value' => $value
     *   }
	 * @param array $array 
	 * @param string $value
	 * @return array
	 */
	public static function groupByValue($array, $value = '')
	{
        $return = array();
        
        if(is_array($array)){
            foreach($array as $k => $v){
                if(isset($v[$value])){
                    $return[$v[$value]][] = $v;    
                }                
            }            
        }
        
        return $return;        
    }

    /**
	 * Changes the case of all keys in a given array
	 * @param array $array
	 * @param int $case
	 */
	public static function changeKeysCase($array, $case = CASE_LOWER)
    {
		$function = ($case == CASE_UPPER) ? 'strtoupper' : 'strtolower';		
		$newArray = array();

        foreach($array as $key => $value) {
            if(is_array($value)){
				// $value is an array, handle keys too
                $newArray[$function($key)] = self::changeKeysCase($value, $case);
			}else if(is_string($key)){
                $newArray[$function($key)] = $value;
			}else{
				// $key is not a string
				$newArray[$key] = $value; 
			}
        }
		
		return $newArray;
	}	
	
}