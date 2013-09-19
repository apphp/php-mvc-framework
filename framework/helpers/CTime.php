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
 * 
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * isValidDate
 * isValidTime
 * getTimeDiff
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
    
    
}