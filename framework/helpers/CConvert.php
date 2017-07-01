<?php
/**
 * CConvert is a helper class that provides a set of helper methods for different type of conversions
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * fileSize
 * 
 */	  

class CConvert
{

	/**
	 * Converts a given size into mb Mb or Kb
	 * @param integer $size
	 * @param array $params
	 * @return float
	 */
	public static function fileSize($size, $params = array())
	{
		$spaceBeforeUnit = isset($params['spaceBeforeUnit']) ? (bool)$params['spaceBeforeUnit'] : true;
		$unitCase = isset($params['unitCase']) ? $params['unitCase'] : '';
		$unit = array('b','kb','mb','gb','tb','pb');
		
		if($unitCase == 'camel'){
			$unit = array('b','Kb','Mb','Gb','Tb','Pb');
		}elseif($unitCase == 'upper'){
			$unit = array('B','KB','MB','GB','TB','PB');
		}

		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).($spaceBeforeUnit ? ' ' : '').$unit[$i];
	}
   
}