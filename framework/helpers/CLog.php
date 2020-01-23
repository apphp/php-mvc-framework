<?php
/**
 * CLog is a helper class that provides a set of helper methods for system logger
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * addMessage
 * 
 */

class CLog
{
	
	/**
	 * Write to log file
	 * @param string $level The error level: 'error', 'debug' or 'info'
	 * @param string $msg   The error message
	 * @return bool
	 */
	public static function addMessage($level = '', $msg = '')
	{
		return A::app()->getLogger()->writeLog($level, $msg);
	}
	
}
