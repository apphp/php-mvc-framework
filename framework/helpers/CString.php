<?php
/**
 * CString is a helper class that provides a set of helper methods for common string operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * substr
 * quote
 * length
 * seoString
 * humanize
 * isSerialized
 * 
 */	  

class CString
{

    /**
     * Cut string by last word
     * @param mixed $string
     * @param int $length
     * @param bool $encoding
     * @param bool $dots
     */
    public static function substr($string, $length = 0, $encoding = '', $dots = false)
    {
        $currentEncoding  = ($encoding ? $encoding : A::app()->charset);
		if(function_exists('mb_strlen') && $encoding !== false){
			$stringLength = mb_strlen($string, $currentEncoding);
		}else{
			$stringLength = strlen($string);
		}
        
        if($stringLength > $length){
            if(function_exists('mb_strlen') && $encoding !== false){
                $output = mb_substr($string, 0, $length, $currentEncoding);
            }else{
                $output = substr($string, 0, (int)$length);
            }
            if($dots){
                $output = trim($output).'...';
            }
        }else{
            $output = $string;
        }
        
        return $output;
    }
    
	/**	
	 * Quotes a string for use (ex.: in a query)
	 * @param string $string
	 * @return string
	 */
	public static function quote($string)
	{
		$search	 = array("\\","\0","\n","\r","\x1a","'",'"',"\'",'\"');
		$replace = array("\\\\","\\0","\\n","\\r","\Z","\'",'\"',"\\'",'\\"');
		return str_replace($search, $replace, $string);
	} 

    /**
     * Returns a length of the given string 
     * @param mixed $string
     * @param bool $encoding
     */
    public static function length($string, $encoding = '')
    {
        $currentEncoding  = ($encoding ? $encoding : A::app()->charset);
		if(function_exists('mb_strlen') && $encoding !== false){
			$stringLength = mb_strlen($string, $currentEncoding);
		}else{
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
        foreach($stringParts as $key){
            if(trim($key) != ''){
                if($words++ < 7 && strlen($seoUrl) < 255){
                    $seoUrl .= ($seoUrl != '') ? '-'.$key : $key;   
                }else{
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
     * Checks is a given string is serialized 
     * @param mixed $string
     * @return bool
     */
	public static function isSerialized($string)
	{
		// If it isn't a string, it isn't serialized
		if (!is_string($string)){
			return false;
		}
		
		$string = trim($string);
		
		if('N;' == $string){
			return true;
		}
		
		if(!preg_match('/^([adObis]):/', $string, $badions)){
			return false;
		}
		
		switch($badions[1]){
			case 'a':
			case 'O':
			case 's':
				if(preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $string))
					return true;
				break;
			case 'b':
			case 'i':
			case 'd':
				if(preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $string))
					return true;
				break;
		}
		
		return false;
	}	
}