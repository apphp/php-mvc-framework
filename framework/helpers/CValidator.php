<?php
/**
 * CValidator is a helper class file that provides different validations methods
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
 * isEmpty          
 * isAlpha
 * isAlphaBetic
 * isNumeric
 * isAlphaNumeric
 * isVariable
 * isMixed
 * isTimeZone
 * isPhone
 * isPassword
 * isUsername
 * isEmail
 * isFileName
 * isDate
 * isDigit
 * isInteger
 * isFloat
 * isHtmlSize
 * isAlignment
 * inArray
 * validateLength
 * validateMinlength
 * validateMaxlength
 * 
 */	  

class CValidator
{

	/**
	 * Checks if the given value is empty
	 * @param mixed $value 
	 * @param boolean $trim 
	 * @return boolean whether the value is empty
	 */
	public static function isEmpty($value, $trim = false)
	{
		return $value === null || $value === array() || $value === '' || ($trim && trim($value) === '');
	}

	/**
	 * Checks if the given value is an alphabetic value
	 * @param mixed $value 
	 * @return boolean 
	 */
    public static function isAlpha($value)
	{
        return preg_match('/^[a-zA-Z]+$/', $value);
    }

	/**
	 * Alias for isAlpha
	 * @see isAlpha
	 */
    public static function isAlphaBetic($value)
	{
		return isAlpha($value);
	}
	
	/**
	 * Checks if the given value is a numeric value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isNumeric($value)
	{
        return preg_match('/^[0-9]+$/', $value);
    }

	/**
	 * Checks if the given value is a alpha-numeric value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isAlphaNumeric($value)
	{
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }
    
	/**
	 * Checks if the given value is a variable name in PHP
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isVariable($value)
	{
        return preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]*$/', $value);
    }

	/**
	 * Checks if the given value is a alpha-numeric value and spaces
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isMixed($value)
	{
        return preg_match('/^[a-zA-Z0-9\s]+$/', $value);
    }

	/**
	 * Checks if the given value is a valid php timezone value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isTimeZone($value)
	{
        return preg_match('/^[a-zA-Z\/]+$/', $value);
    }

	/**
	 * Checks if the given value is a phone number
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isPhone($value)
	{
        return preg_match('/^[\d]{3,12}[-]{0,1}[\d]{0,6}[-]{0,1}[\d]{0,6}$/', $value);
    }

	/**
	 * Checks if the given value is a password 
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isPassword($value)
	{
        return preg_match('/^[a-zA-Z0-9_\-!@#$%^&*()]{6,20}$/', $value);
    }

	/**
	 * Checks if the given value is a username
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isUsername($value)
	{
		if(preg_match('/^[a-zA-Z0-9_\-]{6,20}$/', $value) && !self::isNumeric($value)){
			return true;
		}
        return false;
    }

	/**
	 * Checks if the given value is an email
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isEmail($value)
	{
        return preg_match('/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/', $value);
    }

	/**
	 * Checks if the given value is a file name
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isFileName($value)
	{
	    return preg_match('/^[a-zA-Z0-9_\-]+$/', $value);
    }

	/**
	 * Checks if the given value is a date value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isDate($value)
	{
        return self::isNumeric(strtotime($value));
    }

	/**
	 * Checks if the given value is a digit value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isDigit($value)
    {
		return ctype_digit($value);
    }

	/**
	 * Checks if the given value is an integer value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isInteger($value)
    {
		return is_numeric($value) ? intval($value) == $value : false;
    }

	/**
	 * Checks if the given value is a float value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isFloat($value)
    {
		return is_numeric($value) ? floatval($value) == $value : false;
    }
	
	/**
	 * Checks if the given value is a HTML size value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isHtmlSize($value)    
	{
		return preg_match('/^[0-9]{1,4}[\.]{0,1}[0-9]{0,1}(px|em|pt|%){0,1}$/i', $value) ? true : false;
    }
	
	/**
	 * Checks if the given value is an alignment value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isAlignment($value)
    {
		return in_array($value, array('left', 'right', 'center', 'middle', 'top', 'bottom'));
    }
	
	/**
	 * Checks if the given value presents in a given array
	 * @param mixed $value
	 * @param array $array
	 * @return boolean
	 */
    public static function inArray($value, $array = array())
    {
		if(!is_array($array)) return false;
		return in_array($value, $array);
    }	

	/**
	 * Validates the length of the given value
	 * @param string $value
	 * @param integer $min
	 * @param integer $max
	 * @param boolean $encoding
	 * @return boolean
	 */
    public static function validateLength($value, $min, $max, $encoding = true)
	{
		$strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, A::app()->charset) : strlen($value);    
        return ($strlen >= $min && $strlen <= $max);
    }
	
	/**
	 * Validates the minimum length of the given value
	 * @param string $value
	 * @param integer $min
	 * @param boolean $encoding
	 * @return boolean
	 */
    public static function validateMinlength($value, $min, $encoding = true)
    {
		$strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, A::app()->charset) : strlen($value);    
        return ($strlen < $min) ? false : true;
    }
	
	/**
	 * Validates the maximum length of the given value
	 * @param string $value
	 * @param integer $max
	 * @param boolean $encoding
	 * @return boolean
	 */
    public static function validateMaxlength($value, $max, $encoding = true)
    {
		$strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, A::app()->charset) : strlen($value);    
        return ($strlen > $max) ? false : true;
    }
	
}    
