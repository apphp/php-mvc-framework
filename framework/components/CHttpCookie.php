<?php
/**
 * CHttpCookie provides cookie-level data management
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                  PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * __construct
 * init (static)
 * set
 * get
 * isExists
 * remove
 * clear
 * clearAll
 * setDomain
 * setPath
 * getAll
 *
 */

class CHttpCookie extends CComponent
{
    /** @var integer - timestamp at which the cookie expires. Default 0 means "until the browser is closed" */
    public $expire = 0;
    /** @var boolean */
    public $secure = false;
    /** @var boolean - defines whether the cookie should be accessible only through the HTTP protocol or not */
    public $httpOnly = true;

    /** @var string - the domain that the cookie is available to */
    private $_domain = '';
    /** @var string - path on the server where the cookie will be available on. The default is '/' */
    private $_path = '/';

    /**
     * Class default constructor
     */
    function __construct()
    {
        if (CConfig::get('cookies.domain') != '') {
            $this->setDomain(CConfig::get('cookies.domain'));
        }
        if (CConfig::get('cookies.path') != '') {
            $this->setPath(CConfig::get('cookies.path'));
        }
    }

    /**
     *    Returns the instance of object
     *
     * @return current class
     */
    public static function init()
    {
        return parent::init(__CLASS__);
    }

    /**
     * Sets cookie domain
     *
     * @param  string  $domain
     */
    public function setDomain($domain = '')
    {
        $this->_domain = $domain;
    }

    /**
     * Sets cookie path
     *
     * @param  string  $path
     */
    public function setPath($path = '')
    {
        $this->_path = $path;
    }

    /**
     * Sets cookie
     *
     * @param  string  $name
     * @param  mixed  $value
     * @param  mixed  $expire
     * @param  mixed  $path
     * @param  mixed  $domain
     */
    public function set($name, $value = '', $expire = '', $path = '', $domain = '')
    {
        $expire = ( ! empty($expire)) ? $expire : $this->expire;
        $path   = ( ! empty($path)) ? $path : $this->_path;
        $domain = ( ! empty($domain)) ? $domain : $this->_domain;

        setcookie($name, $value, $expire, $path, $domain, $this->secure, $this->httpOnly);
    }

    /**
     * Returns cookie value
     *
     * @param  string  $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
    }

    /**
     * Checks if cookie variable exists
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function isExists($name)
    {
        return isset($_COOKIE[$name]) ? true : false;
    }

    /**
     * Deletes cookie
     *
     * @param  string  $name
     *
     * @return void
     */
    public function remove($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        };
        setcookie($name, '', time() - 3600, $this->_path, $this->_domain, $this->secure, $this->httpOnly);
    }

    /**
     * Delete cookie
     *
     * @param  string  $name
     *
     * @return void|string
     */
    public function clear($name)
    {
        if ( ! isset($_COOKIE)) {
            return '';
        }

        if (isset($_COOKIE[$name])) {
            $this->remove($name);
        }
    }

    /**
     * Deletes all cookie
     *
     * @return void|string
     */
    public function clearAll()
    {
        if ( ! isset($_COOKIE)) {
            return '';
        }

        foreach ($_COOKIE as $key => $value) {
            $this->remove($key);
        }
    }

    /**
     * Get all cookies
     */
    public function getAll()
    {
        return isset($_COOKIE) ? $_COOKIE : [];
    }


}