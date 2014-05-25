<?php
/**
 * CString is a helper class that provides a set of helper methods for common string operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * substr
 * quote
 * length
 * seoString
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
	
}