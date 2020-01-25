<?php
/**
 * CFilter is a helper class file that provides different filters, including set of helper methods for security operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
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
	 * @return mixed
	 */
	public static function sanitize($type, $data)
	{
		
		$type = strtolower($type);
		
		// Use CI_Security class for these special filters 		
		if ($type == 'filename' || $type == 'xss') {
			include(dirname(__FILE__) . '/../vendors/ci/security.php');
			$ciSecurity = new CI_Security();
		}
		
		if ($type == 'string') {
			// Strip tags, optionally strip or encode special characters
			return filter_var($data, FILTER_SANITIZE_STRING);
		} elseif ($type == 'email') {
			// Remove all characters excepting letters, digits and !#$%&'*+-=?^_`{|}~@.[].
			return filter_var($data, FILTER_SANITIZE_EMAIL);
		} elseif ($type == 'url') {
			// Remove all characters excepting letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
			return filter_var($data, FILTER_SANITIZE_URL);
		} elseif ($type == 'alpha') {
			// Leave only letters
			return preg_replace('/[^A-Za-z]/', '', $data);
		} elseif ($type == 'alphanumeric') {
			// Leave only letters and digits
			return preg_replace('/[^A-Za-z0-9]/', '', $data);
		} elseif ($type == 'hour' || $type == 'minute') {
			// Leave only digits and zero
			return preg_replace('/[^0-9]/', '', $data);
		} elseif ($type == 'integer' || $type == 'int') {
			// Remove all characters except digits, plus and minus sign
			return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
		} elseif ($type == 'float') {
			// Remove all characters except digits, +- and optionally .,eE
			return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT);
		} elseif ($type == 'dbfield') {
			// Leave only allowed characters for database field name
			return preg_replace('/[^A-Za-z0-9_\-]/', '', $data);
		} elseif ($type == 'filename') {
			// Sanitize filename
			return $ciSecurity->sanitize_filename($data);
		} elseif ($type == 'xss') {
			// Sanitize input with xss
			return $ciSecurity->xss_clean($data);
		}
		
		return $data;
	}
	
}

