<?php
/**
 * CArray is a helper class that provides a set of helper methods for common array operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * flipByField
 * uniqueByField
 * changeKeysCase
 * 
 */	  

class CArray
{

	/**
	 * Exchanges all keys with values from defined field in sub-arrays
	 * 
	 * Usage:
	 * $array = array(
	 *  [0] => array(
     *      'field1' => ..v1
     *      'field2' => ..v2
     *      'field3' => ..v3
     *   ),
	 *  [1] => array(
     *      'field1' => ..v1
     *      'field2' => ..v2
     *      'field3' => ..v3
     *   ));
     *   
     *  flipByField($array, 'field3');
     *   
	 * @param array $array 
	 * @param string $field
	 * @return array
	 */
	public static function flipByField($array, $field = '')
	{
        $return = array();
        
        if(is_array($array)){
            foreach($array as $k => $v){
                if(isset($v[$field])){
                    $return[$v[$field]] = $v;
                }                
            }            
        }
        
        return $return;        
    }

	/**
	 * Returns array of uniquie values from specified filed in sub-array
	 *
	 * Usage:
	 * $array = array(
	 *  [0] => array(
     *      'field1' => ..v1
     *      'field2' => ..v2
     *      'field3' => ..v3
     *   ),
	 *  [1] => array(
     *      'field1' => ..v1
     *      'field2' => ..v2
     *      'field3' => ..v3
     *   ));     
     *  uniqueByField($array, 'field3'); 
     *   
	 * @param array $array 
	 * @param string $field
	 * @return array
	 */
	public static function uniqueByField($array, $field = '')
	{
        $return = array();

        if(is_array($array)){
            foreach($array as $k => $v){
				if(isset($v[$field])){
					$return[] = $v[$field];
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