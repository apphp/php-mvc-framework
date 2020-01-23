<?php

return array(
	// application data
	'name' => 'Static Site',
	'version' => '1.0.2',
	
	// Installation settings
	'installationKey' => '1wew3e4r5t',
	
	// Password keys settings (for database passwords only)
	// Remember: changing these settings after installation may lead to unstable work of application
	// encryptAlgorithm - md5, sha1 (not recommended), sha256, whirlpool, etc
	'password' => array(
		'encryption' => true,
		'encryptAlgorithm' => 'sha256',
		'encryptSalt' => true,
		'hashKey' => 'apphp_directy_cmf_hash',
	),
	
	// Text encryption settings (for database text fields only)
	// Remember: changing these settings after installation may lead to unstable work of application
	// Encryption level - PHP or DB
	// encryptAlgorithm - PHP: aes-256-cbc DB: AES
	'text' => array(
		'encryption' => true,
		'encryptAlgorithm' => 'aes-256-cbc',
		'encryptKey' => 'apphp_directy_cmf',
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
	// Token type: 'session', 'cookie' or 'multipages'
	'validation' => array(
		'csrf' => array('enable' => false, 'exclude' => array('PaymentProviders'), 'tokenType' => 'session'),
		'bruteforce' => array('enable' => true, 'badLogins' => 5, 'badRestores' => 5, 'redirectDelay' => 3),
	),
	
	// Exception handling
	// Define exceptions exceptions in application
	'exceptionHandling' => array(
		'enable' => true,
		'level' => 'global',
	),
	
	// Output compression
	'compression' => array(
		'gzip' => array('enable' => false),
		'html' => array('enable' => false),
		'css' => array('enable' => false, 'path' => 'assets/minified/css/', 'minify' => true),
		'js' => array('enable' => false, 'path' => 'assets/minified/js/', 'minify' => true),
	),
	
	// Session settings
	'session' => array(
		'customStorage' => false,    /* true value means use a custom storage (database), false - standard storage */
		'cacheLimiter' => '',        /* to prevent 'Web Page expired' message for POST request use "private,must-revalidate" */
		'lifetime' => 24,            /* session timeout in minutes, default: 24 min = 1440 sec */
	),
	
	// Cookies settings
	'cookies' => array(
		'domain' => '',
		'path' => '/',
	),
	
	// Cache settings
	'cache' => array(
		'enable' => false,
		'type' => 'auto',            /* 'auto' or 'manual' */
		'lifetime' => 20,            /* in minutes */
		'path' => 'protected/tmp/cache/',
	),
	
	// Logger settings
	'log' => array(
		'enable' => false,
		'path' => 'protected/tmp/logs/',
		'fileExtension' => 'php',
		'dateFormat' => 'Y-m-d H:i:s',
		'threshold' => 1,
		'filePermissions' => 0644,
		'lifetime' => 30            /* in days */
	),
	
	// RSS Feed settings
	'rss' => array(
		'path' => 'feeds/',
	),
	
	// Datetime settings
	'defaultTimeZone' => 'UTC',
	
	// Template default settings
	'template' => array(
		'default' => 'default',
	),
	
	// Layout default settings  
	'layouts' => array(
		'enable' => array('frontend' => false, 'backend' => false),
		'default' => 'default',
	),
	
	// Default settings (optional, if defined - will be used as application default settings)
	'defaultBackendDirectory' => 'backend',		/* default backend directory - don't change after installation */
	'defaultErrorController' => 'Error', /* may be overridden by module settings */
	'defaultController' => 'Index',		 /* may be overridden by module settings */
	'defaultAction' => 'index',			 /* may be overridden by module settings */
	
	// Application payment complete page (controller/action - may be overridden by module settings)
	'paymentCompletePage' => '',
	
	// Core components loading
	'coreComponentsLazyLoading' => true,
	
	// Application components
	'components' => array(
		'Bootstrap' => array('enable' => true, 'class' => 'Bootstrap'),
	),
	
	// Widget settings
	'widgets' => array(
		'paramKeysSensitive' => false
	),
	
	// Application Backend settings
	'restoreAdminPassword' => array(
		'enable' => true,
		'recoveryType' => 'direct'            /* 'direct' - send new password directly, 'recovery' - send link to recovery page */
	),
	
	// Widget settings
	'widgets' => array(
		'paramKeysSensitive' => true,
	),
	
	// Application helpers
	'helpers' => array(//'helper' => array('enable' => true, 'class' => 'Helper'),
	),
	
	// Application modules
	'modules' => array(
		'setup' => array('enable' => true, 'removable' => false, 'backendDefaultUrl' => ''),
	),
	
	// Url manager
	'urlManager' => array(
		'urlFormat' => 'shortPath',  /* get | path | shortPath */
		'rules' => array(
			// Required by payments module. If you remove these rules - make sure you define full path URL for pyment providers
			//'paymentProviders/handlePayment/provider/([a-zA-Z0-9\_]+)/handler/([a-zA-Z0-9\_]+)/module/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}/handler/{$1}/module/{$2}',
			'paymentProviders/handlePayment/([a-zA-Z0-9\_]+)/([a-zA-Z0-9\_]+)/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}/handler/{$1}/module/{$2}',
			//'paymentProviders/handlePayment/provider/([a-zA-Z0-9\_]+)/handler/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}/handler/{$1}',
			'paymentProviders/handlePayment/([a-zA-Z0-9\_]+)/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}/handler/{$1}',
			//'paymentProviders/handlePayment/provider/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}',
			'paymentProviders/handlePayment/([a-zA-Z0-9\_]+)[\/]?$' => 'paymentProviders/handlePayment/provider/{$0}',
			// Required by dynamic pages, if you want to use user-friendly URLs
			//'controller/action/value1/value2' => 'controllerName/action/param1/value1/param2/value2',
			//'sitepages/show/example-page-1' => 'sitePages/show/name/about-us',
			//'value1' => 'controllerName/action/param1/value1',
			//'about-us' => 'sitePages/show/name/about-us',
		),
	),

);
