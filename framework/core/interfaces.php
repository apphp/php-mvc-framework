<?php
/**
 * This file contains core interfaces for ApPHP framework
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 *
 */	  

/**
 * IActiveRecord is the interface that must be implemented by model classes
 */
interface IActiveRecord
{
	/**
	 * Returns the static model of the specified AR class
	 */
	public static function model();
}
