<?php
/**
 * CCache is a helper class that provides a set of helper methods for file caching mechanism
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * setCacheFile
 * getCacheFile
 * setCacheLifetime
 * getCacheLifetime
 * setContent
 * getContent
 * 
 */

class CCache
{
	
	/** The limit in amount of cache files */
	const CACHE_LIMIT = 100;
	
	/** @var string */
	private static $_cacheFile = '';
	/** @var integer */
	private static $_cacheLifetime = '';
	
	
	/**
	 * Sets cache file name
	 * @param $cacheFile
	 */
	public static function setCacheFile($cacheFile = '')
	{
		self::$_cacheFile = !empty($cacheFile) ? $cacheFile : '';
	}
	
	/**
	 * Gets cache file name
	 * @return string
	 */
	public static function getCacheFile()
	{
		return self::$_cacheFile;
	}
	
	/**
	 * Sets cache file name
	 * @param $cacheLifetime
	 */
	public static function setCacheLifetime($cacheLifetime = 0)
	{
		self::$_cacheLifetime = !empty($cacheLifetime) ? $cacheLifetime : 0;
	}
	
	/**
	 * Gets cache file name
	 * @return integer
	 */
	public static function getCacheLifetime()
	{
		return self::$_cacheLifetime;
	}
	
	/**
	 * Sets cache in cache file
	 * @param string $content
	 * @param string $cacheDir
	 */
	public static function setContent($content = '', $cacheDir = '')
	{
		if (!empty(self::$_cacheFile)) {
			// Remove oldest file if the limit of cache is reached
			if (CFile::getDirectoryFilesCount($cacheDir, '.cch') >= self::CACHE_LIMIT) {
				CFile::removeDirectoryOldestFile($cacheDir, 0, array('index.html'));
			}
			
			// Save the content to the cache file
			CFile::writeToFile(self::$_cacheFile, serialize($content));
		}
	}
	
	/**
	 * Checks if cache exists and valid and return it's content
	 * @param string $cacheFile
	 * @param integer $cacheLifetime
	 * @return mixed
	 */
	public static function getContent($cacheFile = '', $cacheLifetime = '')
	{
		$result = '';
		$cacheContent = '';
		
		if (!empty($cacheFile)) self::setCacheFile($cacheFile);
		if (!empty($cacheLifetime)) self::setCacheLifetime($cacheLifetime);
		
		if (!empty(self::$_cacheFile) && !empty(self::$_cacheLifetime)) {
			if (file_exists(self::$_cacheFile)) {
				$cacheTime = self::$_cacheLifetime * 60;
				// Serve from the cache if it is younger than $cacheTime
				if ((filesize(self::$_cacheFile) > 0) && ((time() - $cacheTime) < filemtime(self::$_cacheFile))) {
					// Output the contents of the cache file
					ob_start();
					include self::$_cacheFile;
					$cacheContent = ob_get_clean();
				}
				$result = !empty($cacheContent) ? unserialize($cacheContent) : $cacheContent;
			}
		}
		
		return $result;
	}
	
}