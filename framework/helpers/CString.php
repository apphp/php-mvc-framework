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
 * 
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * substr
 * 
 */	  

class CString
{

    /**
     * Cut string by last word
     * @param mixed $text
     * @param int $length
     * @param bool $encoding
     * @param bool $dots
     */
    public static function substr($text, $length = 0, $encoding = '', $dots = false)
    {
        $currentEncoding  = ($encoding ? $encoding : A::app()->charset);
		if(function_exists('mb_strlen') && $encoding !== false){
			$textLength = mb_strlen($text, $currentEncoding);
		}else{
			$textLength = strlen($text);
		}
        
        if($textLength > $length){
            if(function_exists('mb_strlen') && $encoding !== false){
                $output = mb_substr($text, 0, $length, $currentEncoding);
            }else{
                $output = substr($text, 0, (int)$length);
            }
            if($dots){
                $output = trim($output).'...';
            }
        }else{
            $output = $text;
        }
        
        return $output;
    }
    
}