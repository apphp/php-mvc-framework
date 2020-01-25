<?php
/**
 * CArray is a helper class that provides a set of helper methods for common array operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * flipByField
 * uniqueByField
 * changeKeysCase
 * toArray
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
     *      'field1' => value01
     *      'field2' => value02
     *      'field3' => value03
     *   ),
	 *  [1] => array(
     *      'field1' => value11
     *      'field2' => value12
     *      'field3' => value13
     *   ));
	 *
	 *  Result:
	 * $array = array(
	 *  ['value01'] => array(
	 *      'field1' => value01
	 *      'field2' => value02
	 *      'field3' => value03
	 *   ),
	 *  ['value11'] => array(
	 *      'field1' => value11
	 *      'field2' => value12
	 *      'field3' => value13
	 *   ));
	 *
     *  flipByField($array, 'field3');
     *   
	 * @param array $array 
	 * @param string $field
	 * @param bool $group - allows to group elements in more then one
	 * @return array
	 */
	public static function flipByField($array, $field = '', $group = false)
	{
		$return = array();
		
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (isset($v[$field])) {
					if ($group) {
						$return[$v[$field]][] = $v;
					} else {
						$return[$v[$field]] = $v;
					}
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns array of unique values from specified filed in sub-array
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
	 *  uniqueByField($array, 'field3');
	 *
	 * @param array $array
	 * @param string $field
	 * @param bool $unique
	 * @return array
	 */
	public static function uniqueByField($array, $field = '', $unique = false)
	{
		$return = array();
		
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (isset($v[$field])) {
					$return[] = $v[$field];
				}
			}
			
			if ($unique) {
				$return = array_unique($return);
			}
		}
		
		return $return;
	}
	
	/**
	 * Changes the case of all keys in a given array
	 * @param array $array
	 * @param int $case
	 * @return array
	 */
	public static function changeKeysCase($array, $case = CASE_LOWER)
	{
		$function = ($case == CASE_UPPER) ? 'strtoupper' : 'strtolower';
		$newArray = array();
		
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				// $value is an array, handle keys too
				$newArray[$function($key)] = self::changeKeysCase($value, $case);
			} elseif (is_string($key)) {
				$newArray[$function($key)] = $value;
			} else {
				// $key is not a string
				$newArray[$key] = $value;
			}
		}
		
		return $newArray;
	}
	
	/**
	 * Convert current object to array
	 * @param array $obj
	 * @return array
	 */
	public static function toArray($obj = null)
	{
		if (is_object($obj)) {
			$obj = (array)$obj;
		}
		
		if (is_array($obj)) {
			$new = array();
			foreach ($obj as $key => $val) {
				$new[$key] = self::toArray($val);
			}
		} else {
			$new = $obj;
		}
		
		return $new;
	}
	
}