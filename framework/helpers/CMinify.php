<?php
/**
 * CMinify is a helper class that provides a set of helper methods for common minifying operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * html
 * css
 * js
 *
 */

class CMinify
{
	
	/**
	 * Minify html output
	 * @param string $buffer
	 * @return string
	 */
	public static function html($buffer = '')
	{
		$search = array(
			'/\n/',                    // Replace end of line by a space
			'/\>[^\S ]+/s',            // Strip whitespaces after tags
			'/[^\S ]+\</s',            // Strip whitespaces before tags
			'/(\s)+/s',                // Shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/',    // Remove HTML comments
			'~//[a-zA-Z0-9 ]+$~m'    // remove simple JS line comments (excluding lines containing URL)
		);
		
		$replace = array(
			' ',
			'>',
			'<',
			'\\1',
			'',
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
	
	/**
	 * Minify css output
	 * @param string $buffer
	 * @return string
	 */
	public static function css($buffer = '')
	{
		$search = array(
			'!/\*[^*]*\*+([^/][^*]*\*+)*/!',                // Remove comments
			'/;[\s\r\n\t]*?}[\s\r\n\t]*/ims',                // Remove trailing semicolon of selector's last property
			'/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims',        // Remove any whitespace between semicolon and property-name
			'/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims',    // Remove any whitespace surrounding property-colon
			'/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims',    // Remove any whitespace surrounding selector-comma			.body-color , .text-color{font-size:10px;} => .body-color,.text-color{font-size:10px;}
			'/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims',    // Remove any whitespace surrounding opening parenthesis	.body-color{ font-size:10px;} => .body-color{font-size:10px;}
			'/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims',        // Remove any whitespace between numbers and units			font-size:10 px; => font-size:10px;
			'/([^\d\.]0)(px|em|pt|%)/ims',                    // Shorten zero-values 										font-size:0px; => font-size:0;
			'/\p{Zs}+/ims',                                    // Constrain multiple whitespaces							font-size:10px;    font-weight:bold; => font-size:10px; font-weight:bold;
		);
		
		$replace = array(
			' ',
			"}\r\n",
			';$1',
			':$1',
			',$1',
			'{$1',
			'$1$2',
			'$1',
			' ',
		);
		
		// 1. Remove ﻿ character from the beginning of the files
		$buffer = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $buffer);
		
		// 2. Backup single and double quotes
		preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $buffer, $found, PREG_PATTERN_ORDER);
		for ($i = 0; $i < count($found[1]); $i++) {
			$buffer = str_replace($found[1][$i], '#####' . $i . '#####', $buffer);
		}
		
		// 3. Execute general minifying
		$buffer = preg_replace($search, $replace, $buffer);
		
		// 4. Remove newlines
		$buffer = str_replace(array("\r\n", "\r", "\n"), '', $buffer);
		
		// 5. Restore backuped single and double quotes
		for ($i = 0; $i < count($found[1]); $i++) {
			$buffer = str_replace('#####' . $i . '#####', $found[1][$i], $buffer);
		}
		
		return $buffer;
	}
	
	
	/**
	 * Minify js output
	 * @param string $buffer
	 * @return string
	 */
	public static function js($buffer = '')
	{
		// FIX - remove BOM character from the beginning of the files
		$bom = pack('H*', 'EFBBBF');
		$buffer = preg_replace("/^$bom/", '', $buffer);
		
		// COMMENTS
		// ----------------------------------
		// 1. Backup http:// and https://
		preg_match_all('/(https:\/\/|http:\/\/|\'\/\/)/ims', $buffer, $found, PREG_PATTERN_ORDER);
		for ($i = 0; $i < count($found[1]); $i++) {
			$buffer = str_replace($found[1][$i], '#####' . $i . '#####', $buffer);
		}
		
		// 2. Single-line comments
		$buffer = preg_replace('/\/\/.*$/m', '', $buffer);
		// 3. Multi-line comments
		$buffer = preg_replace('/\/\*.*?\*\//s', '', $buffer);
		
		// 4. Restore backupped http:// and https://
		for ($i = 0; $i < count($found[1]); $i++) {
			$buffer = str_replace('#####' . $i . '#####', $found[1][$i], $buffer);
		}
		
		// WHITE SPACES
		// ----------------------------------
		// 1. Uniform line endings, make them all line feed
		$buffer = str_replace(array("\r\n", "\r"), "\n", $buffer);
		// 2. Collapse all non-line feed whitespace into a single space
		$buffer = preg_replace('/[^\S\n]+/', ' ', $buffer);
		// 3. Strip leading & trailing whitespace
		$buffer = str_replace(array(" \n", "\n "), "\n", $buffer);
		// 4. Collapse consecutive line feeds into just 1
		$buffer = preg_replace('/\n+/', "\n", $buffer);
		
		/* 5.
		* Whitespace after `return` can be omitted in a few occasions
		* (such as when followed by a string or regex)
		* Same for whitespace in between `)` and `{`, or between `{` and some other keywords.
		*/
		$buffer = preg_replace('/\breturn\s+(["\'\/\+\-])/', 'return$1', $buffer);
		$buffer = preg_replace('/\)\s+\{/', '){', $buffer);
		$buffer = preg_replace('/}\n(else|catch|finally)\b/', '}$1', $buffer);
		$buffer = preg_replace('/\s*(\<|\>)\s*/', '$1', $buffer);
		$buffer = preg_replace('/\s*\(\s*/', '(', $buffer);
		$buffer = preg_replace('/\s*(\|\||&&|===|==|=|\:|\+|\/|\*)\s*/', '$1', $buffer);
		
		/* 6.
		 * Below will also keep `;` after a `do{}while();` along with `while();`
		 * While these could be stripped after do-while, detecting this * distinction is cumbersome, so we'll play it
		 * safe and make sure `;` after any kind of `while` is kept.
		 */
		$buffer = preg_replace('/(while\([^;\{]+\));(\}|$)/s', '\\1;;\\2', $buffer);
		
		/* 7.
		 * We also can't strip empty else-statements. Even though they're useless and probably shouldn't be in the code
		 * in the first place, we shouldn't be stripping the `;` that follows it as it breaks the code.
		 * We can just remove those useless else-statements completely.
		 * @see https://github.com/matthiasmullie/minify/issues/91
		 */
		$buffer = preg_replace('/else;/s', '', $buffer);
		
		// NEWLINES
		// ----------------------------------
		$buffer = str_replace(
			array(";\n", ",\n", "(\n", "\n)", "[\n", "\n]", "{\n", "\n}", "}\n}", "}\n)", "\n.", ":\n", "else\n", "\nif", "\nfunction", "\nreturn", "&&\n", "||\n", "+\n", "-\n"),
			array(";", ",", "(", ")", "[", "]", "{", "}", "}}", "})", ".", ": ", "else ", " if", " function", " return", "&&", "||", "+", "-"),
			$buffer);
		
		$buffer = trim($buffer, "\n");
		
		return $buffer;
	}
	
}
