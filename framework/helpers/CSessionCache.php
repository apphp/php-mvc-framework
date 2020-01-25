<?php
/**
 * CSessionCache is a helper class that provides a set of helper methods for session caching mechanism
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 *
 * A typical usage pattern of data cache is like the following:
 *
 * ```php
 * * // Try to retrieve $data from cache
 * $data = CSessionCache::get($key);
 * if(empty($data)){
 * 		// $data is not found in cache or empty, so calculate it from scratch
 * 		$data = $this->doSomething();
 * 		// Now store $data in cache so it can be retrieved next time
 * 		CSessionCache::set($data, $key);
 * }
 * ```
 *
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * __callStatic				_cacheName()				_set
 * 														_get
 * 														_getAllCache
 * 														_getOrSet
 * 														_remove
 *
 */

class CSessionCache
{
	
	/** The limit in amount of cache files */
	const CACHE_LIMIT = 100;
	
	/** @var string */
	private static $_cachePrefix = 'csc_';
	
	/**
	 * Triggered when invoking inaccessible methods in an object context
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		// Block usage of this class if not allowed
		if (!CConfig::get('cache.data.enable')) {
			switch (strtolower($method)) {
				case 'get':
				case 'getallcache':
					$return = null;
					break;
				case 'set':
				case 'getorset':
				case 'remove':
				default:
					$return = false;
					break;
			}
			return $return;
		}
		
		if (CClass::isMethodExists(__CLASS__, '_' . $method)) {
			return forward_static_call_array(array(__CLASS__, '_' . $method), $args);
		}
	}
	
	/**
	 *    Returns session cache name
	 * @return string
	 */
	protected static function _cacheName()
	{
		return self::$_cachePrefix . session_id();
	}
	
	/**
	 * Sets cache in cache variable
	 * @param string $cacheVar
	 * @param mixed $content
	 * @return bool
	 */
	private static function _set($cacheVar = '', $content = '')
	{
		if (!empty($cacheVar)) {
			$sessionCache = A::app()->getSession()->get(self::_cacheName());
			
			if (!is_array($sessionCache)) {
				$sessionCache = array();
			}
			
			// Remove oldest session var if the limit of cache is reached
			if (count($sessionCache) >= self::CACHE_LIMIT) {
				array_shift($sessionCache);
			}
			
			$sessionCache[$cacheVar] = serialize($content);
			
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
	private static function _get($cacheVar = '')
	{
		$result = null;
		
		if (!empty($cacheVar)) {
			$sessionCache = A::app()->getSession()->get(self::_cacheName());
			if (isset($sessionCache[$cacheVar])) {
				$result = unserialize($sessionCache[$cacheVar]);
			}
		}
		
		return $result;
	}
	
	/**
	 *    Returns all session cache
	 * @return array
	 */
	private static function _getAllCache()
	{
		return A::app()->getSession()->get(self::_cacheName());
	}
	
	/**
	 * Method combines both [[set()]] and [[get()]] methods to retrieve value identified by a $cacheVar,
	 * or to store the result of $callable execution if there is no cache available for the $cacheVar.
	 * @param string $cacheVar
	 * @param callable|\Closure
	 * @return bool
	 * @since 1.2.2
	 */
	private static function _getOrSet($cacheVar, $callable = '')
	{
		if (($value = self::get($cacheVar)) !== null) {
			return $value;
		}
		
		$value = call_user_func($callable);
		if (!self::set($cacheVar, $value)) {
			CDebug::addMessage('warnings', 'missing-helper', A::t('core', 'Failed to set cache value for key {key}', array('{key}' => $cacheVar)));
		}
		
		return $value;
	}
	
	/**
	 * Remove cache variable
	 * @param string $cacheVar
	 * @return bool
	 */
	private static function _remove($cacheVar = '')
	{
		if (!empty($cacheVar)) {
			$sessionCache = A::app()->getSession()->get(self::_cacheName());
			
			if (isset($sessionCache[$cacheVar])) {
				unset($sessionCache[$cacheVar]);
				
				// Save the content to the cache variable
				A::app()->getSession()->set(self::_cacheName(), $sessionCache);
				
				return true;
			}
		}
		
		return false;
	}
	
}