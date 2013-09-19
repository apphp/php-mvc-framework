<?php

return array(
    // application data
    'name'=>'Static Site',
    'version'=>'0.0.1',
    
    'installationKey' => '1wew3e4r5t',

	'email' => array(
        'mailer' => 'smtpMailer', /* phpMail | phpMailer | smtpMailer */
        'from'   => 'info@email.me',
        'isHtml' => true,
        'smtp' => array(
            'secure' => 'ssl',
            'host' => 'smtp.gmail.com',
            'port' => '465',
            'username' => '',
            'password' => '',
        ),
    ),
   	'validation' => array(
        'csrf' => true
    ),
    
    'defaultTimeZone' => 'UTC',
    'defaultTemplate' => 'default',
	'defaultController' => 'Index',
    'defaultAction' => 'index',

    'urlManager' => array(
        'urlFormat' => 'shortPath',  /* get | path | shortPath */
        'rules' => array(
            //'controller/action/value1/value2' => 'controller/action/param1/value1/param2/value2',
        ),
    ),
    
);