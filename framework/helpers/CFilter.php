<?php
/**
 * CFilter is a helper class file that provides different filters
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * sanitize
 * 
 */	  

class CFilter
{
    /**
     * Sanitizes specified data
     * @param string $type
     * @param mixed $data
     */
    public static function sanitize($type, $data)
    {
        if($type == 'string'){
            // Strip tags, optionally strip or encode special characters
            return filter_var($data, FILTER_SANITIZE_STRING);        
        }else if($type == 'email'){
            // Remove all characters excepting letters, digits and !#$%&'*+-=?^_`{|}~@.[].
            return filter_var($data, FILTER_SANITIZE_EMAIL);       
        }else if($type == 'url'){
            // Remove all characters excepting letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
            return filter_var($data, FILTER_SANITIZE_URL);
        }else if($type == 'alpha'){
            // Leave only letters 
            return preg_replace('/[^A-Za-z]/', '', $data);       
        }else if($type == 'alphanumeric'){
            // Leave only letters and digits
            return preg_replace('/[^A-Za-z0-9]/', '', $data);       
        }else if($type == 'hour' || $type == 'minute'){
            // Leave only digits and zero
            return preg_replace('/[^0-9]/', '', $data);       
        }else if($type == 'integer' || $type == 'int'){
            // Remove all characters except digits, plus and minus sign
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);       
        }else if($type == 'float'){
            // Remove all characters except digits, +- and optionally .,eE
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT);
        }else if($type == 'dbfield'){
            // Leave only allowed characters for database field name
            return preg_replace('/[^A-Za-z0-9_\-]/', '', $data);
        }        
        
        return $data;        
    }
        
}