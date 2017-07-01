<?php
/**
 * CLocale is a helper class that provides a set of helper methods for data localization
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * date
 * getDateTimeFormats
 * getDateFormats
 * getTimeFormats
 * 
 */	  

class CLocale
{

	protected static $_arrDateFormats = array(
		'Y-m-d' 		=> array('preview' => '[ Y-m-d ]', 				'converted_format' => '%Y-%m-%d'),
		'm-d-Y' 		=> array('preview' => '[ m-d-Y ]', 				'converted_format' => '%m-%d-%Y'),
		'd-m-Y' 		=> array('preview' => '[ d-m-Y ]', 				'converted_format' => '%d-%m-%Y'),
			
		'Y M d'			=> array('preview' => '[ Y M d ]', 				'converted_format' => '%Y %M %d'),
		'M d Y'			=> array('preview' => '[ M d Y ]', 				'converted_format' => '%M %d %Y'),
		'd M Y'			=> array('preview' => '[ d M Y ]', 				'converted_format' => '%d %M %Y'),
		'M d, Y'		=> array('preview' => '[ M d, Y ]', 			'converted_format' => '%M %d, %Y'),
		'd M, Y'		=> array('preview' => '[ d M, Y ]', 			'converted_format' => '%d %M, %Y'),

		'Y M j'			=> array('preview' => '[ Y M j ]', 				'converted_format' => '%Y %M %j'),
		'M j, Y'		=> array('preview' => '[ M j, Y ]', 			'converted_format' => '%M %j, %Y'),
		'j M, Y'		=> array('preview' => '[ j M, Y ]', 			'converted_format' => '%j %M, %Y'),
	
		'Y F d' 		=> array('preview' => '[ Y F d ]', 				'converted_format' => '%Y %F %d'),
		'F d Y' 		=> array('preview' => '[ F d Y ]', 				'converted_format' => '%F %d %Y'),
		'd F Y' 		=> array('preview' => '[ d F Y ]', 				'converted_format' => '%d %F %Y'),
			
		'Y F j'			=> array('preview' => '[ Y F j ]', 				'converted_format' => '%Y %F %j'),
		'F j, Y'		=> array('preview' => '[ F j, Y ]', 			'converted_format' => '%F %j, %Y'),
		'j F, Y'		=> array('preview' => '[ j F, Y ]', 			'converted_format' => '%j %F, %Y'),
	);		

	protected static $_arrTimeFormats = array(
		'H:i:s' 		=> array('preview' => '[ H:i:s ]', 				'converted_format' => '%H:%i:%s'),
		'h:i:s' 		=> array('preview' => '[ h:i:s ]', 				'converted_format' => '%h:%i:%s'),
		'g:i:s' 		=> array('preview' => '[ g:i:s ]', 				'converted_format' => '%g:%i:%s'),
	
		'h:i a' 		=> array('preview' => '[ h:i a ]', 				'converted_format' => '%h:%i %a'),
		'h:i A' 		=> array('preview' => '[ h:i A ]', 				'converted_format' => '%h:%i %A'),
		'g:i a' 		=> array('preview' => '[ g:i a ]', 				'converted_format' => '%g:%i %a'),
		'g:i A' 		=> array('preview' => '[ g:i A ]', 				'converted_format' => '%g:%i %A'),
		
		'H:i' 			=> array('preview' => '[ H:i ]', 				'converted_format' => '%H:%i'),
		'h:i' 			=> array('preview' => '[ h:i ]', 				'converted_format' => '%h:%i'),
		'g:i' 			=> array('preview' => '[ g:i ]', 				'converted_format' => '%g:%i'),
	);

	protected static $_arrDateTimeFormats = array(
		'Y-m-d H:i:s'	=>	array('preview' => '[ Y-m-d H:i:s ] ',		'converted_format' => '%Y-%m-%d %H:%i:%s'),
		'm-d-Y H:i:s'	=>	array('preview' => '[ m-d-Y H:i:s ] ',		'converted_format' => '%m-%d-%Y %H:%i:%s'),
		'd-m-Y H:i:s'	=>	array('preview' => '[ d-m-Y H:i:s ] ',		'converted_format' => '%d-%m-%Y %H:%i:%s'),
		'm-d-Y h:i:s'	=>	array('preview' => '[ m-d-Y H:i:s ] ',		'converted_format' => '%m-%d-%Y %h:%i:%s'),
		'd-m-Y h:i:s'	=>	array('preview' => '[ d-m-Y H:i:s ] ',		'converted_format' => '%d-%m-%Y %h:%i:%s'),
		'm-d-Y g:ia'	=>	array('preview' => '[ m-d-Y g:ia ] ',		'converted_format' => '%m-%d-%Y %g:%i%a'),
		'd-m-Y g:ia'	=>	array('preview' => '[ d-m-Y g:ia ] ',		'converted_format' => '%d-%m-%Y %g:%i%a'),
		'M d, Y g:ia'	=>	array('preview' => '[ M d, Y g:ia ] ',		'converted_format' => '%M %d, %Y %g:%i%a'),
		'd M, Y g:ia'	=>	array('preview' => '[ d M, Y g:ia ] ',		'converted_format' => '%d %M, %Y %g:%i%a'),
		'F j Y, g:ia'	=>	array('preview' => '[ F j Y, g:ia ] ',		'converted_format' => '%F %j %Y, %g:%i%a'),
		'j F Y, g:ia'	=>	array('preview' => '[ j F Y, g:ia ] ',		'converted_format' => '%j %F %Y, %g:%i%a'),		
		'D, F j Y g:ia'	=>	array('preview' => '[ D, F j Y g:ia ] ',	'converted_format' => '%D, %F %j %Y %g:%i%a'),
		'D, M d Y g:ia'	=>	array('preview' => '[ D, M d Y g:ia ] ',	'converted_format' => '%D, %M %d %Y %g:%i%a'),
	);

	/**
	 * Transforms the given date into localazed date
	 * @param string $format
	 * @param string $date
	 * @param bool $unixFormat
	 * @return string
	*/
	public static function date($format = '', $date = '', $unixFormat = false)
	{
		$dateFormat = null;
		$search = array();
		$replace = array();
		$result = '';
		$amPm = '';
		
		if($unixFormat){
			$date = !empty($date) ? date('Y-m-d H:i:s', $date) : date('Y-m-d H:i:s');
		}else{
			$date = !empty($date) ? $date : date('Y-m-d H:i:s');
		}
		
		if(isset(self::$_arrDateTimeFormats[$format])){
			$dateFormat = self::$_arrDateTimeFormats[$format];			
			$parts 	= explode(' ', $date);
			
			$dateParts	= isset($parts[0]) ? explode('-', $parts[0]) : array();			
			$year 		= isset($dateParts[0]) ? $dateParts[0] : '';
			$month 		= isset($dateParts[1]) ? $dateParts[1] : '';
			$day 		= isset($dateParts[2]) ? $dateParts[2] : '';
			$weekDay	= date('w', strtotime($date)) + 1;
			
			$timeParts	= isset($parts[1]) ? explode(':', $parts[1]) : array();
			$hour 		= isset($timeParts[0]) ? $timeParts[0] : '';
			$hour24 	= $hour;
			$hour12 	= ($hour >= 13 ? $hour - 12 : $hour);
			$minute 	= isset($timeParts[1]) ? $timeParts[1] : '';
			$second 	= isset($timeParts[2]) ? $timeParts[2] : '';
			
			$amPm		= ($hour24 < 12) ? A::t('i18n', 'amName') : A::t('i18n', 'pmName');
			
			$convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';		
		}elseif(isset(self::$_arrDateFormats[$format])){
			$dateFormat = self::$_arrDateFormats[$format];
		
			$parts 	= explode(' ', $date);
			$dateParts	= isset($parts[0]) ? explode('-', $parts[0]) : array();

			$year 		= isset($dateParts[0]) ? $dateParts[0] : '';
			$month 		= isset($dateParts[1]) ? $dateParts[1] : '';
			$day 		= isset($dateParts[2]) ? $dateParts[2] : '';
			$dayParts 	= explode(' ', $day);
			$day 		= isset($day[0]) ? $dayParts[0] : '';
	
			$convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';		
		}elseif(isset(self::$_arrTimeFormats[$format])){			
			$dateFormat = self::$_arrTimeFormats[$format];
			
			$parts 	= explode(' ', $date);
			$timeParts	= isset($parts[1]) ? explode(':', $parts[1]) : array();
			
			$hour 		= isset($timeParts[0]) ? $timeParts[0] : '';
			$hour24 	= $hour;
			$hour12 	= ($hour >= 13 ? $hour - 12 : $hour);
			$minute 	= isset($timeParts[1]) ? $timeParts[1] : '';
			$second 	= isset($timeParts[2]) ? $timeParts[2] : '';
			
			$amPm		= ($hour24 < 12) ? A::t('i18n', 'amName') : A::t('i18n', 'pmName');
			
			$convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';
		}else{
			$result = date($format, strtotime($date));
		}
		
		if($dateFormat){
			
			switch($format){

				/*
				|---------------------------------------------------
				| Date Formats
				|---------------------------------------------------
				*/
				case 'Y-m-d':	/* 2015-01-31 */
				case 'm-d-Y':	/* 01-31-2015 */
				case 'd-m-Y':	/* 31-01-2015 */
					
					$search = array('%Y', '%m', '%d');
					$replace = array($year, $month, $day);
					break;
				
				case 'Y M d':	/* 2015 Oct 01 */
				case 'M d Y':	/* Oct 01 2015 */
				case 'd M Y':	/* 01 Oct 2015 */
				case 'M d, Y':	/* Oct 01, 2015 */
				case 'd M, Y':	/* 01 Oct, 2015 */

					$search = array('%Y', '%M', '%d');
					$replace = array($year, A::t('i18n', 'monthNames.abbreviated.'.(int)$month), $day);
					break;
				
				case 'Y M j':	/* 2015 Oct 1 */
				case 'M j, Y':	/* Oct 1, 2015 */
				case 'j M, Y':	/* 1 Oct, 2015 */

					$search = array('%Y', '%M', '%j');
					$replace = array($year, A::t('i18n', 'monthNames.abbreviated.'.(int)$month), (int)$day);
					break;
				
				case 'Y F d':	/* 2015 October 01 */
				case 'F d Y':	/* October 01 2015 */
				case 'd F Y':	/* 01 October 2015 */

					$search = array('%Y', '%F', '%d');
					$replace = array($year, A::t('i18n', 'monthNames.wide.'.(int)$month), $day);
					break;
				
				case 'Y F j':	/* 2015 October 1 */
				case 'F j, Y':	/* October 1, 2015 */
				case 'j F, Y':  /* 1 October, 2015 */

					$search = array('%Y', '%F', '%j');
					$replace = array($year, A::t('i18n', 'monthNames.wide.'.(int)$month), (int)$day);
					break;
				
				/*
				|---------------------------------------------------
				| Time Formats
				|---------------------------------------------------
				*/
				case 'H:i:s':	/* 13:53:20 */
				case 'h:i:s':	/* 01:53:20 */
				case 'g:i:s':	/* 1:53:20 */ 

					$search = array('%H', '%h', '%g', '%i', '%s');
					$replace = array($hour24, $hour12, (int)$hour12, $minute, $second);
					break;

				case 'h:i a':	/*  01:47 pm */
				case 'g:i a':	/*  1:47 pm */

					$search = array('%h', '%g', '%i', '%a');
					$replace = array($hour12, (int)$hour12, $minute, strtolower($amPm));
					break;

				case 'h:i A':	/*  01:47 PM */
				case 'g:i A':	/*  1:47 PM */

					$search = array('%h', '%g', '%i', '%A');
					$replace = array($hour12, (int)$hour12, $minute, strtoupper($amPm));
					break;

				case 'H:i':		/*  13:47 */
				case 'h:i':		/*  01:47 */
				case 'g:i':		/*   1:47 */

					$search = array('%H', '%h', '%g', '%i');
					$replace = array($hour24, $hour12, (int)$hour12, $minute);
					break;

				/*
				|---------------------------------------------------
				| DateTime Formats
				|---------------------------------------------------
				*/
				case 'Y-m-d H:i:s':		/* 2015-01-31 13:02:59 */
				case 'm-d-Y H:i:s':		/* 01-31-2015 13:02:59 */
				case 'd-m-Y H:i:s':		/* 31-01-2015 13:02:59 */
				case 'm-d-Y h:i:s':		/* 01-31-2015 01:02:59 */
				case 'd-m-Y h:i:s':		/* 31-01-2015 01:02:59 */

					$search = array('%Y', '%m', '%d', '%H', '%h', '%g', '%i', '%s');
					$replace = array($year, $month, $day, $hour24, $hour12, (int)$hour12, $minute, $second);
					break;

				case 'm-d-Y g:ia':		/* 2015-01-31 1:02pm */
				case 'd-m-Y g:ia':		/* 31-01-2015 1:02pm */	

					$search = array('%Y', '%m', '%d', '%g', '%i', '%a');
					$replace = array($year, $month, $day, (int)$hour12, $minute, strtolower($amPm));
					break;
				
				case 'M d, Y g:ia':		/* Oct 09, 2015 1:02pm */
				case 'd M, Y g:ia':		/* 09 Oct, 2015 1:02pm */
					
					$monthAbbrev = A::t('i18n', 'monthNames.abbreviated.'.(int)$month);
					$search = array('%Y', '%M', '%d', '%g', '%i', '%a');
					$replace = array($year, $monthAbbrev, $day, (int)$hour12, $minute, strtolower($amPm));					
					break;
				
				case 'F j Y, g:ia':		/* October 1 2015, 1:02pm */
				case 'j F Y, g:ia':		/* 1 October 2015, 1:02pm */

					$monthWide = A::t('i18n', 'monthNames.wide.'.(int)$month);
					$search = array('%Y', '%F', '%j', '%g', '%i', '%a');
					$replace = array($year, $monthWide, (int)$day, (int)$hour12, $minute, strtolower($amPm));					
					break;
					
				case 'D, F j Y g:ia':	/* Mon, October 1 2015 1:02pm */
				case 'D, M d Y g:ia':   /* Mon, Oct 1 2015 1:02pm */

					$monthWide = A::t('i18n', 'monthNames.wide.'.(int)$month);
					$monthAbbrev = A::t('i18n', 'monthNames.abbreviated.'.(int)$month);
					$weekDayAbbrev = A::t('i18n', 'weekDayNames.abbreviated.'.(int)$weekDay);
					$search = array('%Y', '%F', '%M', '%j', '%d', '%D', '%g', '%i', '%a');
					$replace = array($year, $monthWide, $monthAbbrev, (int)$day, $day, $weekDayAbbrev, (int)$hour12, $minute, strtolower($amPm));
					break;
				
				default:
					$result = $date;
					break;
			}

			if(!empty($search) && !empty($replace)){
				$result = str_replace($search, $replace, $convertedFormat);	
			}			
		}
		
		return $result;	
	}
	
	/**
	 * Returns array of datetime formats supported by system
	 * @return array
	 */
	public static function getDateTimeFormats()
	{
		$result = array();
		
		foreach(self::$_arrDateTimeFormats as $key => $dateTimeFormat){
			$result[$key] = $dateTimeFormat['preview'];
		}
		
		return $result;	
	}

	/**
	 * Returns array of date formats supported by system
	 * @return array
	 */
	public static function getDateFormats()
	{
		$result = array();
		
		foreach(self::$_arrDateFormats as $key => $dateFormat){
			$result[$key] = $dateFormat['preview'];
		}
		
		return $result;	
	}
   
	/**
	 * Returns array of time formats supported by system
	 * @return array
	 */
	public static function getTimeFormats()
	{
		$result = array();
		
		foreach(self::$_arrTimeFormats as $key => $timeFormat){
			$result[$key] = $timeFormat['preview'];
		}
		
		return $result;	
	}	

}
