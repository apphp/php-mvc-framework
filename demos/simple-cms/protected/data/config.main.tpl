<?php

return array(
    // application data
    'name' => 'Simple CMS',
    'version' => '1.0.1',
    
    // installation settings
    'installationKey' => '<INSTALLATION_KEY>',

    // Password keys settings (for database passwords only - don't change it)
    // md5, sha1, sha256, whirlpool, etc
	'password' => array(
        'encryption' => true,
        'encryptAlgorithm' => 'sha256',
		'encryptSalt' => true,
		'hashKey' => 'apphp_directy_cmf',    
    ),
    
    // Default email settings
	'email' => array(
        'mailer' => 'smtpMailer', /* phpMail | phpMailer | smtpMailer */
        'from' => 'info@email.me',
        'fromName' => '', /* John Smith */
        'isHtml' => true,
        'smtp' => array(
            'auth' => true, /* true or false */
            'secure' => 'ssl', /* 'ssl', 'tls' or '' */
            'host' => 'smtp.gmail.com',
            'port' => '465',
            'username' => '',
            'password' => '',
        ),
    ),
    
    // Validations
	// Define array of 'excluded' controllers, ex.: array('PaymentProviders', 'Checkout')
    'validation' => array(
        'csrf' => array('enable' => true, 'exclude' => array('PaymentProviders')),
        'bruteforce' => array('enable' => true, 'badLogins' => 5, 'redirectDelay' => 3)
    ),

    // Output compression
	'compression' => array(
		'gzip' => array('enable' => true),
		'html' => array('enable' => false),
	),

    // Session settings
    'session' => array(
        'customStorage' => false,	/* true value means use a custom storage (database), false - standard storage */
        'cacheLimiter' => '',		/* to prevent 'Web Page expired' message for POST request use "private,must-revalidate" */
        'lifetime' => 24,			/* session timeout in minutes, default: 24 min = 1440 sec */
    ),
    
    // Cookies settings
    'cookies' => array(
        'domain' => '', 
        'path' => '/' 
    ),

    // Cache settings 
    'cache' => array(
        'enable' => false,
		'type' => 'auto', 			/* 'auto' or 'manual' */
        'lifetime' => 20,  			/* in minutes */
        'path' => 'protected/tmp/cache/'
    ),

    // Logger settings 
    'log' => array(
		'enable' => false, 
        'path' => 'protected/tmp/logs/',
		'fileExtension' => 'php', 	
        'dateFormat' => 'Y-m-d H:i:s',
        'threshold' => 1,
		'filePermissions' => 0644,
		'lifetime' => 30			/* in days */
    ),

    // RSS Feed settings 
    'rss' => array(
        'path' => 'feeds/'
    ),

    // Datetime settings
    'defaultTimeZone' => 'UTC',
    
    // Template default settings  
	'template' => array(
		'default' => 'default'			
	),
	
	// Layout default settings  
	'layouts' => array(
		'enable' => true, 
		'default' => 'default'
	),
	
    // Application default settings
	'defaultErrorController' => 'Error', /* may be overridden by module settings */
	'defaultController' => 'Index',		 /* may be overridden by module settings */
    'defaultAction' => 'index',			 /* may be overridden by module settings */
	
	// Application payment complete page (controller/action - may be overridden by module settings)
	'paymentCompletePage' => '',
    
	// application components
    'components' => array(
        'cmsMenu' => array('enable'=>true, 'class'=>'CmsMenu'),
        'cmsHelper' => array('enable'=>true, 'class'=>'CmsHelper'),        
    ),

	// Widget settings
	'widgets' => array(
		'paramKeysSensitive' => true
	),

    // Application helpers
    'helpers' => array(
        //'helper' => array('enable' => true, 'class' => 'Helper'),
    ),

    // Application modules
    'modules' => array(
        'setup' => array('enable' => true, 'removable' => false, 'backendDefaultUrl' => ''),
    ),

    // Url manager
    'urlManager' => array(
        'urlFormat' => 'shortPath',  /* get | path | shortPath */
        'rules' => array(
        ),
    ),
    
);
