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
 * isEmpty          
 * isAlpha
 * isAlphaBetic
 * isNumeric
 * isAlphaNumeric
 * isVariable
 * isMixed
 * isText
 * isTimeZone
 * isPhone
 * isPhoneString
 * isPassword
 * isUsername
 * isEmail
 * isIdentityCode
 * isFileName
 * isDate
 * isDigit
 * isInteger
 * isPositiveInteger
 * isFloat
 * isHtmlSize
 * isUrl
 * isAlignment
 * inArray
 * validateLength
 * validateMinLength
 * validateMaxLength
 * validateMin
 * validateMax
 * validateMinDate
 * validateMaxDate
 * validateRange
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
	 * @param int $type 0 - digits only, 1 - with dot or comma
	 * @return boolean
	 */
    public static function isNumeric($value, $type = 0)
	{
        if($type == 1){
            // check also with dot or comma
            return preg_match('/^[0-9\.,]+$/', $value);
        }else{
            return preg_match('/^[0-9]+$/', $value);
        }
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
	 * Checks if the given value is a textual value and allowed HTML tags
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isText($value)
	{
        if((preg_match("/<[^>]*script*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*object*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*iframe*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*applet*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*meta*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*style*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*form*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*img*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*onmouseover*\"?[^>]*>/i", $value)) ||
            (preg_match("/<[^>]*body*\"?[^>]*>/i", $value)) ||
            (preg_match("/ftp:\/\//i", $value)) || 
            (preg_match("/https:\/\//i", $value)) || 
            (preg_match("/http:\/\//i", $value)) )
        {		
            return false;
        }	
        return true; 
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
        return preg_match('/^[+]{0,1}[\d]{3,12}[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,6}$/', $value);
    }

	/**
	 * Checks if the given value is a phone number in a free format:
	 * 7 or 10 digit number, with extensions allowed, delimiters are spaces, dashes or periods
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isPhoneString($value)
	{
        return preg_match('/^[+]?([\d]{0,3})?[-| ]{0,1}[\(\.\-\s]?([\d]{0,3})[\)\.\-\s]?[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,6}$/', $value);
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
	 * Checks if the given value is identity code
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isIdentityCode($value)
	{
	    return preg_match('/^[a-zA-Z0-9_\-]+$/', $value);
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
        $year = substr($value, 0, 4);
        $month = substr($value, 5, 2);
        $day = substr($value, 8, 2);
        if(strtotime($value) == strtotime($year.'-'.$month.'-'.$day)){
            return checkdate($month, $day, $year);
        }else{
            $date = strtotime($value);        
            return (!empty($date) && self::isInteger($date));            
        }
        $date = strtotime($value);        
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
	 * Checks if the given value is a positive integer value
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isPositiveInteger($value)
    {
		return (is_numeric($value) && $value > 0) ? intval($value) == $value : false;
    }
	
	/**
	 * Checks if the given value is a float value
	 * @param mixed $value
	 * @param string $format
	 * @return boolean
	 */
    public static function isFloat($value, $format = '')
    {
        if($format == 'european') $value = CNumber::europeanFormat($value);
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
	 * Checks if the given value is a valid URL address
	 * @param mixed $value
	 * @return boolean
	 */
    public static function isUrl($value)    
	{
        return (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)) ? false : true;
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
    public static function validateMinLength($value, $min, $encoding = true)
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
    public static function validateMaxLength($value, $max, $encoding = true)
    {
		$strlen = (function_exists('mb_strlen') && $encoding !== false) ? mb_strlen($value, A::app()->charset) : strlen($value);    
        return ($strlen > $max) ? false : true;
    }

	/**
	 * Validates if the given numeric value is grater or equal to specified value
	 * @param string $value
	 * @param integer $min
	 * @param string $format
	 * @return boolean
	 */
    public static function validateMin($value, $min, $format = '')
    {
        if($format == 'european') $value = CNumber::europeanFormat($value);
		if(!is_numeric($value)) return false;
        return ($value >= $min) ? true : false;
    }

	/**
	 * Validates if the given numeric value is less than or equal to specified value
	 * @param string $value
	 * @param integer $max
	 * @param string $format
	 * @return boolean
	 */
    public static function validateMax($value, $max, $format = '')
    {
        if($format == 'european') $value = CNumber::europeanFormat($value);        
		if(!is_numeric($value)) return false;
        return ($value <= $max) ? true : false;
    }

	/**
	 * Validates if the given date value is grater or equal to specified value
	 * @param string $value
	 * @param integer $min
	 * @return boolean
	 */
    public static function validateMinDate($value, $min)
    {
        if($format == 'european') $value = CNumber::europeanFormat($value);
        return ($value >= $min) ? true : false;
    }

	/**
	 * Validates if the given date value is less than or equal to specified value
	 * @param string $value
	 * @param integer $max
	 * @return boolean
	 */
    public static function validateMaxDate($value, $max)
    {
        if($format == 'european') $value = CNumber::europeanFormat($value);        
        return ($value <= $max) ? true : false;
    }
        
	/**
	 * Validates if the given numeric value in a specified range
	 * @param string $value
	 * @param integer $min
	 * @param integer $max
	 * @return boolean
	 */
    public static function validateRange($value, $min, $max)
    {
		if(!is_numeric($value)) return false;
        return ($value >= $min && $value <= $max) ? true : false;
    }
	
}    
