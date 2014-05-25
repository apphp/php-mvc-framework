<?php
/**
 * CTime is a helper class that provides a set of helper methods for timestamp operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * isValidDate
 * isValidTime
 * getTimeDiff
 * dateParseFromFormat
 * 
 */	  

class CTime
{

	/**
	 * Checks if the year, month and day are valid date value
	 * @param integer $year
	 * @param integer $month
	 * @param integer $day
	 * @return boolean 
	 */
	public static function isValidDate($year, $month, $day)
	{
		return checkdate($month, $day, $year);
	}

	/**
	 * Checks if the hour, minute and second are valid time value
	 * @param integer $hour
	 * @param integer $minute
	 * @param integer $second
	 * @return boolean 
	 */
	public static function isValidTime($hour, $minute, $static)
	{
		if($hour < 0 || $hour > 23) return false;
		if($minute  > 59 || $minute < 0) return false;
		if($second > 59 || $second < 0) return false;
		return true;
	}

    /**
     * 	Returns time difference in seconds
     * 	@param string $endTime
     * 	@param string $startTime
     * 	@param string $units
     * 	@return long
     */
    function getTimeDiff($endTime, $startTime, $units = 'second')
    {
        $difference = strtotime($endTime) - strtotime($startTime);
        if($units == 'day') return $difference / 86400;
        if($units == 'hour') return $difference / 3600;
        if($units == 'minute') return $difference / 60;
        return $difference;
    }
	
	
     /**
     * 	Returns info about given date formatted according to the specified format
     * 	used to replace date_parse_from_format() function (PHP 5.3+)
     * 	@param string $format
     * 	@param string $date
     * 	@return array
     */
    function dateParseFromFormat($format, $date)
    {
		if(function_exists('date_parse_from_format')){
			return date_parse_from_format($format, $date);
		}else{
			// reverse engineer date formats
			$keys = array(
				'Y' => array('year', '\d{4}'),              
				'y' => array('year', '\d{2}'),              
				'm' => array('month', '\d{2}'),             
				'n' => array('month', '\d{1,2}'),           
				'M' => array('month', '[A-Z][a-z]{3}'),     
				'F' => array('month', '[A-Z][a-z]{2,8}'),   
				'd' => array('day', '\d{2}'),               
				'j' => array('day', '\d{1,2}'),             
				'D' => array('day', '[A-Z][a-z]{2}'),       
				'l' => array('day', '[A-Z][a-z]{6,9}'),     
				'u' => array('hour', '\d{1,6}'),            
				'h' => array('hour', '\d{2}'),              
				'H' => array('hour', '\d{2}'),              
				'g' => array('hour', '\d{1,2}'),            
				'G' => array('hour', '\d{1,2}'),            
				'i' => array('minute', '\d{2}'),            
				's' => array('second', '\d{2}')             
			);
	
			// converting format to regex
			$regex = '';
			$chars = str_split($format);
			foreach($chars as $n => $char){
				$lastChar = isset($chars[$n-1]) ? $chars[$n-1] : '';
				$skipCurrent = ('\\' == $lastChar);
				if(!$skipCurrent && isset($keys[$char])){
					$regex .= '(?P<'.$keys[$char][0].'>'.$keys[$char][1].')';
				}else if('\\' == $char ){
					$regex .= $char;
				}else{
					$regex .= preg_quote($char);
				}
			}
	
			$dt = array();
			$dt['error_count'] = 0;
			// test for matching
			if(preg_match('#^'.$regex.'$#', $date, $dt)){
				foreach($dt as $key => $val){
					if(is_int($key)) unset($dt[$key]);
				}
				if(!checkdate((int)$dt['month'], (int)$dt['day'], (int)$dt['year'])){
					$dt['error_count'] = 1;
				}
			}else{
				$dt['error_count'] = 1;
			}
			$dt['errors'] = array();
			$dt['fraction'] = '';
			$dt['warning_count'] = 0;
			$dt['warnings'] = array();
			$dt['is_localtime'] = 0;
			$dt['zone_type'] = 0;
			$dt['zone'] = 0;
			$dt['is_dst'] = '';
			return $dt;			
		}
	}  
    
}