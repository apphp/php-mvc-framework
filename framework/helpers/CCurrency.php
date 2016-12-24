<?php
/**
 * CCurrency is a helper class that provides a set of helper methods for common currency operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * format
 * 
 */	  

class CCurrency
{
	
    /**
     * Format price value
     * @param mixed $price
     * @param array $params
     */
    public static function format($price, $params = array())
    {
        // Determine the type of default separators
		$numberFormat = Bootstrap::init()->getSettings()->number_format;
		if($numberFormat == 'european'){
			$defaultDecimalSeparator = ',';
			$defaultThousandsSeparator = '.';
		}else{
			$defaultDecimalSeparator = '.';
			$defaultThousandsSeparator = ',';
		}

 		// Get currency info
        $rate               = isset($params['rate']) ? $params['rate'] : A::app()->getCurrency('rate');		
        $decimals           = isset($params['decimals']) ? $params['decimals'] : A::app()->getCurrency('decimals');		
        $symbol             = isset($params['symbol']) ? $params['symbol'] : A::app()->getCurrency('symbol');
        $symbolPlace        = isset($params['symbolPlace']) ? $params['symbolPlace'] : A::app()->getCurrency('symbol_place'); 
        $decimalSeparator   = isset($params['decimalSeparator']) ? $params['decimalSeparator'] : $defaultDecimalSeparator;
        $thousandsSeparator = isset($params['thousandsSeparator']) ? $params['thousandsSeparator'] : $defaultThousandsSeparator;
		
        $return  = ($symbolPlace == 'before') ? $symbol : '';
        $return .= ($decimals != '' && $rate != '') ? number_format((float)($price * $rate), $decimals, $decimalSeparator, $thousandsSeparator) : $price;
        $return .= ($symbolPlace == 'after') ? $symbol : '';
               
        return $return;
          
    }

}
