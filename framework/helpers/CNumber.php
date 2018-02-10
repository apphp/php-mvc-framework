<?php
/**
 * CNumber is a helper class that provides a set of helper methods for common number operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * americanFormat
 * europeanFormat
 * format
 * percent
 * percentage
 * 
 */	  

class CNumber
{
	
    /**
     * Format number to american format (1.000,00 => 1,000.00)
     * @param mixed $number
     * @param array $params
     */
    public static function americanFormat($number, $params = array())
    {
        if(!empty($number)){
			$thousandSeparator = isset($params['thousandSeparator']) ? (bool)$params['thousandSeparator'] : true;
			$number = str_replace(',', '#', $number);
			$number = str_replace('.', (($thousandSeparator) ? ',' : ''), $number);
			$number = str_replace('#', '.', $number);
		}
        return $number;
    }

    /**
     * Format number to eropean format (1,000.00 => 1.000,00)
     * @param mixed $number
     * @param array $params
     */
    public static function europeanFormat($number, $params = array())
    {
		if(!empty($number)){
			$number = str_replace('.', '#', $number);
			$number = str_replace(',', '.', $number);
			$number = str_replace('#', ',', $number);			
		}
        return $number;
    }

    /**
     * Formats a number with grouped thousands
     * @param mixed $number
     * @param array $params
     * @return string
     */
    public static function format($number, $format = 'american', $params = array())
    {
		$decimalPoints = isset($params['decimalPoints']) ? $params['decimalPoints'] : 0;
		
		if($format === 'european'){
			$number = number_format((float)$number, $decimalPoints, ',', '.');
		}else{
			$number = number_format((float)$number, $decimalPoints, '.', ',');
		}
		
		return $number;
	}

	/**
	 * Formats a number as a pecrcent
	 * @param float $value
	 * @param int $decimalPoints
	 * @param string $sign
	 * @return string
	 */
	function percent($value, $decimalPoints = 2, $sign = '%')
	{
		return round((float)$value, $decimalPoints).$sign;
	}
	
	/**
	 * Gets two numbers and returns calculated percentage
	 * @param float $part
	 * @param float $whole
	 * @param int $decimalPoints
	 * @param string $sign
	 * @return string
	 */
	function percentage($part, $whole, $decimalPoints = 2, $sign = '%')
	{		
		$result = 0;
	
		// Prevent division by zero
		if(!empty($whole)){
			$result = percent(($part / $whole * 100), $decimalPoints, '');
		}
		
		return $result.$sign;
	}
}
