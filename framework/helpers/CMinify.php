<?php
/**
 * CMinify is a helper class that provides a set of helper methods for common minifying operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 */	  

class CMinify
{

	/**
	 * Minify html output
	 * @param string $buffer
	 * @return string
	 */
	function html($buffer = '')
	{
		$search = array(
		  '/\n/',					// Replace end of line by a space
		  '/\>[^\S ]+/s',			// Strip whitespaces after tags
		  '/[^\S ]+\</s',			// Strip whitespaces before tags
		  '/(\s)+/s',				// Shorten multiple whitespace sequences
		  '/<!--(.|\s)*?-->/', 		// Remove HTML comments
		  '~//[a-zA-Z0-9 ]+$~m' 	// remove simple JS line comments (excluding lines containing URL)
		);
	
		$replace = array(
		  ' ',
		  '>',
		  '<',
		  '\\1',
		  ''
		);	
		
		// 1. Execute general minifying
		$buffer = preg_replace($search, $replace, $buffer);
		
		// 2. Remove optional ending tags
		// See: http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission
		$optionalEndingTags = array('</option>', '</li>', '</dt>', '</dd>', '</tr>', '</th>', '</td>');
		$buffer = str_ireplace($optionalEndingTags, '', $buffer);
		
		// Execute
		return $buffer;
	}
	
}
