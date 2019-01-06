<?php
/**
 * CSessionCache is a helper class that provides a set of helper methods for session caching mechanism
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * set						_cacheName()
 * get
 * getAllCache
 *
 */

class CSessionCache
{
	
	/** The limit in amount of cache files */
	const CACHE_LIMIT = 100;
    
    /** @var string */
    private static $_cachePrefix = 'csc_';

    /**
     * Sets cache in cache variable
     * @param string $cacheVar
	 * @param mixed $content
	 * @return bool
     */
    public static function set($cacheVar = '', $content = '')
    {
        if(!empty($cacheVar)){
			$sessionCache = A::app()->getSession()->get(self::_cacheName());
	
			// Remove oldest session var if the limit of cache is reached
            if(count($sessionCache) >= self::CACHE_LIMIT){
            	$sessionCache = array_pop($sessionCache);
            }
	
			$sessionCache[$cacheVar] = $content;
            
            // Save the content to the cache variable
			A::app()->getSession()->set(self::_cacheName(), $sessionCache);
	
			return true;
        }
        
        return false;
    }
 
    /**
     * Checks if cache variable exists and valid and return it's content
     * @param string $cacheVar
     * @return mixed
     */
    public static function get($cacheVar = '')
    {
        $result = null;
        
        if(!empty($cacheVar)){
			$sessionCache = A::app()->getSession()->get(self::_cacheName());
	
			if(isset($sessionCache[$cacheVar])){
				$result = $sessionCache[$cacheVar];
			}
        }
        
        return $result;
    }
	
	/**
	 *	Returns all session cache
	 *	@return array
	 */
	public static function getAllCache()
	{
		return A::app()->getSession()->get(self::_cacheName());
	}
	
	/**
	 *	Returns session cache name
	 *	@return string
	 */
	protected static function _cacheName()
	{
		return self::$_cachePrefix.session_id();
	}
	
}