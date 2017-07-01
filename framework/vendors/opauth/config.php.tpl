<?php
/**
 * Opauth basic configuration file to quickly get you started
 * ==========================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * If you require advanced configuration options, refer to opauth.conf.php.advanced
 */

return array(
/**
 * Path where Opauth is accessed.
 *  - Begins and ends with /
 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 *  - if Opauth is reached via http://auth.example.org/, path is '/'
 */
    'path' => '<PATH>',

/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
    'callback_url' => '<CALLBACK_URL>',

/**
 * A random string used for signing of $auth response.
 */
    'security_salt' => '<RANDOM_STRING>',
    'security_iteration' => 300,
    'security_timeout' => '2 minutes',

    'callback_transport' => 'session',
    'debug' => false,
/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 *
 * eg.
 * 'Strategy' => array(
 *   'Facebook' => array(
 *      'app_id' => 'APP_ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 * )
 *
 */
    'Strategy' => array(
        // Define strategies and their respective configs here

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
    ),
);
