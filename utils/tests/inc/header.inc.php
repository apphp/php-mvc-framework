<?php

// change the following paths if necessary
defined('APPHP_PATH') || define('APPHP_PATH', dirname(__FILE__).'/../../../demos/'.$default_project);
// directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
// production | debug | demo | test | hidden
defined('APPHP_MODE') or define('APPHP_MODE', 'test'); 

$apphp = dirname(__FILE__).'/../../../framework/Apphp.php';
$config = APPHP_PATH.'/protected/config/';

require_once($apphp);
A::init($config)->run(); 
