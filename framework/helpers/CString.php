<?php
/**
 * CString is a helper class that provides a set of helper methods for common string operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * substr
 * strToLower
 * strToUpper
 * quote
 * length
 * seoString
 * humanize
 * plural
 * isSerialized
 * shortenUrl
 *
 */

class CString
{
	
	/**
	 * Returns sub-string by required length
	 * @param mixed $string
	 * @param int $length
	 * @param string $encoding
	 * @param bool $dots
	 * @return string
	 */
	public static function substr($string, $length = 0, $encoding = '', $dots = false)
	{
		$currentEncoding = ($encoding ? $encoding : A::app()->charset);
		if (function_exists('mb_strlen') && $encoding !== false) {
			$stringLength = mb_strlen($string, $currentEncoding);
		} else {
			$stringLength = strlen($string);
		}
		
		if ($stringLength > $length) {
			if (function_exists('mb_strlen') && $encoding !== false) {
				$output = mb_substr($string, 0, $length, $currentEncoding);
			} else {
				$output = substr($string, 0, (int)$length);
			}
			if ($dots) {
				$output = trim($output) . '...';
			}
		} else {
			$output = $string;
		}
		
		return $output;
	}
	
	/**
	 * Make a string lowercase
	 * @param mixed $string
	 * @return string
	 */
	public static function strToLower($string = '')
	{
		return function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
	}
	
	/**
	 * Make a string uppercase
	 * @param mixed $string
	 * @return string
	 */
	public static function strToUpper($string = '')
	{
		return function_exists('mb_strtoupper') ? mb_strtoupper($string) : strtoupper($string);
	}
	
	/**
	 * Quotes a string for use (ex.: in a query)
	 * @param string $string
	 * @return string
	 */
	public static function quote($string)
	{
		$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"', "\'", '\"');
		$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"', "\\'", '\\"');
		return str_replace($search, $replace, $string);
	}
	
	/**
	 * Returns a length of the given string
	 * @param mixed $string
	 * @param bool $encoding
	 */
	public static function length($string, $encoding = '')
	{
		$currentEncoding = ($encoding ? $encoding : A::app()->charset);
		if (function_exists('mb_strlen') && $encoding !== false) {
			$stringLength = mb_strlen($string, $currentEncoding);
		} else {
			$stringLength = strlen($string);
		}
		
		return $stringLength;
	}
	
	/**
	 * Returns a string converted into SEO string
	 * @param mixed $string
	 */
	public static function seoString($string)
	{
		$forbiddenSymbols = array("\\", '"', "'", '(', ')', '[', ']', '*', '.', ',', '&', ';', ':', '&amp;', '?', '!', '=');
		$seoUrl = '';
		$words = 0;
		
		$string = str_replace($forbiddenSymbols, '', strip_tags($string));
		$stringParts = explode(' ', $string);
		foreach ($stringParts as $key) {
			if (trim($key) != '') {
				if ($words++ < 7 && strlen($seoUrl) < 255) {
					$seoUrl .= ($seoUrl != '') ? '-' . $key : $key;
				} else {
					break;
				}
			}
		}
		
		return $seoUrl;
	}
	
	/**
	 * Humanize a given string
	 * @param mixed $string
	 * @return string
	 */
	public static function humanize($string)
	{
		$string = trim(strtolower($string));
		$string = preg_replace('/[^a-z0-9\-\_\s+]/', '', $string);
		$string = preg_replace('/\_/', ' ', $string);
		$string = preg_replace('/\-/', ' ', $string);
		$string = preg_replace('/\s+/', ' ', $string);
		$string = explode(' ', $string);
		$string = array_map('ucwords', $string);
		
		return implode(' ', $string);
	}
	
	/**
	 * Takes a singular word and converts it into plural form
	 * @param string $string
	 * @return string
	 */
	function plural($string)
	{
		$result = (string)$string;

        if (PHP_VERSION_ID > 70300) {
            if (!is_countable($result)) {
                return $result;
            }
        }

		$pluralRules = array(
			'/(quiz)$/' => '\1zes',      // quizzes
			'/^(ox)$/' => '\1\2en',     // ox
			'/([m|l])ouse$/' => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/' => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/' => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/' => '\1ies',      // query, ability, agency, jewelry
			'/(hive)$/' => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/' => '\1\2ves',    // half, safe, wife
			'/sis$/' => 'ses',        // basis, diagnosis
			'/([ti])um$/' => '\1a',        // datum, medium
			'/(p)erson$/' => '\1eople',    // person, salesperson
			'/(m)an$/' => '\1en',       // man, woman, spokesman
			'/(c)hild$/' => '\1hildren',  // child
			'/(buffal|tomat)o$/' => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/' => '\1\2ses',    // bus, campus
			'/(alias|status|virus)$/' => '\1es',       // alias
			'/(octop)us$/' => '\1i',        // octopus
			'/(ax|cris|test)is$/' => '\1es',       // axis, crisis
			'/s$/' => 's',          // no change (compatibility)
			'/$/' => 's',
		);
		
		foreach ($pluralRules as $rule => $replacement) {
			if (preg_match($rule, $result)) {
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Checks is a given string is serialized
	 * @param mixed $string
	 * @return bool
	 */
	public static function isSerialized($string)
	{
		// If it isn't a string, it isn't serialized
		if (!is_string($string)) {
			return false;
		}
		
		$string = trim($string);
		
		if ('N;' == $string) {
			return true;
		}
		
		$badions = array();
		
		if (!preg_match('/^([adObis]):/', $string, $badions)) {
			return false;
		}
		
		switch ($badions[1]) {
			case 'a':
			case 'O':
			case 's':
				if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $string))
					return true;
				break;
			case 'b':
			case 'i':
			case 'd':
				if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $string))
					return true;
				break;
		}
		
		return false;
	}

    /**
     * Returns shorten URL
     * @param  string  $url
     * @param  int  $len1
     * @param  int  $len2
     * @return string|string[]|null
     */
    public static function shortenUrl($url = '', $len1 = 0, $len2 = 0)
    {
        return preg_replace("/(?<=.{{$len1}})(.+)(?=.{{$len2}})/", '...', $url);
    }

}