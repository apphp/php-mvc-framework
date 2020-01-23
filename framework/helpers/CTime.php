<?php
/**
 * CTime is a helper class that provides a set of helper methods for timestamp operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * isValidDate
 * isValidTime
 * isEmptyDate
 * isEmptyDateTime
 * getTimeDiff
 * getMicrotime
 * dateParseFromFormat
 * parseDateFormat
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
	public static function isValidTime($hour, $minute, $second)
	{
		if ($hour < 0 || $hour > 23) return false;
		if ($minute > 59 || $minute < 0) return false;
		if ($second > 59 || $second < 0) return false;
		return true;
	}
	
	/**
	 * Checks if a given datetime has empty value
	 * @param string $datetime
	 * @return boolean
	 */
	public static function isEmptyDate($date)
	{
		return empty($date) ? true : false;
	}
	
	/**
	 * Checks if a given datetime has empty value
	 * @param string $datetime
	 * @return boolean
	 */
	public static function isEmptyDateTime($dateTime)
	{
		return empty($dateTime) ? true : false;
	}
	
	/**
	 *    Returns time difference in seconds
	 * @param string $endTime
	 * @param string $startTime
	 * @param string $units
	 * @return long
	 */
	public static function getTimeDiff($endTime, $startTime, $units = 'second')
	{
		$difference = strtotime($endTime) - strtotime($startTime);
		if ($units == 'day') return $difference / 86400;
		if ($units == 'hour') return $difference / 3600;
		if ($units == 'minute') return $difference / 60;
		return $difference;
	}
	
	/**
	 * Returns microtime value
	 * @return float
	 */
	public static function getMicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 *    Returns info about given date formatted according to the specified format
	 *    used to replace date_parse_from_format() function (PHP 5.3+)
	 * @param string $format
	 * @param string $date
	 * @return array
	 */
	public static function dateParseFromFormat($format, $date)
	{
		if (function_exists('date_parse_from_format')) {
			return date_parse_from_format($format, $date);
		} else {
			// Reverse engineer date formats
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
				's' => array('second', '\d{2}'),
			);
			
			// Converting format to regex
			$regex = '';
			$chars = str_split($format);
			foreach ($chars as $n => $char) {
				$lastChar = isset($chars[$n - 1]) ? $chars[$n - 1] : '';
				$skipCurrent = ('\\' == $lastChar);
				if (!$skipCurrent && isset($keys[$char])) {
					$regex .= '(?P<' . $keys[$char][0] . '>' . $keys[$char][1] . ')';
				} elseif ('\\' == $char) {
					$regex .= $char;
				} else {
					$regex .= preg_quote($char);
				}
			}
			
			$dt = array();
			$dt['error_count'] = 0;
			// Test for matching
			if (preg_match('#^' . $regex . '$#', $date, $dt)) {
				foreach ($dt as $key => $val) {
					if (is_int($key)) unset($dt[$key]);
				}
				if (!checkdate((int)$dt['month'], (int)$dt['day'], (int)$dt['year'])) {
					$dt['error_count'] = 1;
				}
			} else {
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
	
	/**
	 *    Returns info about given date formatted according to the specified format
	 * @param string $dateFormat
	 * @return array
	 */
	public static function parseDateFormat($dateFormat = '')
	{
		$return = array(
			'simple-format' => '',
			'calendar-format' => '',
			'year-index' => '',
			'month-index' => '',
			'day-index' => '',
		);
		
		switch ($dateFormat) {
			case 'd-m-Y':
			case 'd M Y':
			case 'd M, Y':
			case 'j M, Y':
			case 'd F Y':
			case 'j F, Y':
				$return = array(
					'simple-format' => 'dd-mm-yy',
					'calendar-format' => 'dd-mm-yyyy',
					'year-index' => '2',
					'month-index' => '1',
					'day-index' => '0',
				);
				break;
			case 'm-d-Y':
			case 'M d Y':
			case 'M d, Y':
			case 'M j, Y':
			case 'F d Y':
			case 'F j, Y':
				$return = array(
					'simple-format' => 'mm-dd-yy',
					'calendar-format' => 'mm-dd-yyyy',
					'year-index' => '2',
					'month-index' => '0',
					'day-index' => '1',
				);
				break;
			case 'Y-m-d':
			case 'Y M d':
			case 'Y M j':
			case 'Y F d':
			case 'Y F j':
			default:
				$return = array(
					'simple-format' => 'yy-mm-dd',
					'calendar-format' => 'yyyy-mm-dd',
					'year-index' => '0',
					'month-index' => '1',
					'day-index' => '2',
				);
				break;
		}
		
		return $return;
	}
	
}