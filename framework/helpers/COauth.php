<?php
/**
 * COauth is a helper class file that provides entrance to the site through a social network. Based on the protocol OAuth
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * USAGE:
 * ----------
 * Call COauth::config() + COauth::login()
 *
 * PUBLIC (static):         PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * config
 * login
 * getError
 *
 */

// Include the main library.
include(dirname(__FILE__).'/../vendors/opauth/Opauth.php');

class COauth
{
    /* @var */
    private static $config = array(
        'path' => '/',
        'callback_url' => '',
        'security_salt' => '',
        'security_iteration' => 300,
        'security_timeout' => '2 minutes',

        'callback_transport' => 'session',
        'debug' => false,
        'Strategy' => array(
            'Facebook' => array(
                'app_id' => '',
                'app_secret' => '',
                'scope' => 'public_profile,email',
                'fields' => 'id,name,first_name,last_name,gender,email',
            ),
            'Google' => array(
                'client_id' => '',
                'client_secret' => '',
                'scope' => 'email',
            ),
            'Twitter' => array(
                'key' => '',
                'secret' => '',
                'scope' => 'include_email',
            ),
            'LinkedIn' => array(
                'api_key' => '',
                'secret_key' => '',
                'scope' => 'r_basicprofile r_emailaddress'
            ),
        ),
    );

    /**
     * Sets a basic configuration
     * More information - https://github.com/opauth/opauth/wiki/Opauth-configuration
     * @param array $params
     * Usage:
     * COauth::config(array(
     *      'path' => '/customer/login/type/', // If url address: http://my_site/customer/login/type/facebook
     *      'callback_url' => 'http://my_site/customer/success_return',
     *      'security_salt' => '{random_string}',
     *      'security_iteration' => '300',
     *      'security_timeout' => '2 minutes',
     *      'callback_transport' => 'session', // It can take the following parameters: 'session', 'post' or 'get';
     *      'debug' => false,
     *      'Strategy' => array(
     *          'Facebook' => array(
     *              'app_id' => '{application_id}',
     *              'app_secret' => '{application_secret}',
     *              'score' => 'public_profile,email',    // More - https://developers.facebook.com/docs/facebook-login/permissions
     *              'fields' => 'id,name,first_name,last_name,gender,email' // More (look fields) - https://developers.facebook.com/docs/graph-api/reference/v2.6/user
     *          ),
     *          'Google' => array(
     *              'client_id' => '{application_id}',
     *              'client_secret' => '{application_secret}',
     *              'score' => 'email', // More - https://developers.google.com/+/web/api/rest/oauth#authorization-scopes
     *          ),
     *          'Twitter' => array(
     *              'key' => '{application_id}',
     *              'secret' => '{application_secret}'
     *          )
     *      ),
     * ))
     *
     * @return void
     */
    public static function config($params)
    {
        self::$config['path'] = A::app()->getRequest()->getBasePath();
        self::$config['callback_url'] = A::app()->getRequest()->getBaseUrl();
        self::$config['security_salt'] = A::app()->getRequest()->getCsrfTokenValue();
        self::$config = array_merge(self::$config, $params);
    }

    /**
     * An attempt to login the site with the help of social networks. If successful, the result is stored in the session or return through $_GET or $_POST. It depends on the parameter 'callback_transport'
     * @return false
     */
    public static function login()
    {
        try{
            // If successful, it will be performed on a redirect page $this->config["callback_url"]
            $opauth = new Opauth(self::$config);
        }catch(Exception $e) {
            CDebug::addMessage('errors', 'Social Login', $e->getMessage());
        }
        return false;
    }

    /*
     * The function returns all available strategies in lowercase
     * @return array
     * */
    public static function getStrategies()
    {
        return self::$config['Strategy'];
    }
}
