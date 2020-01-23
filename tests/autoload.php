<?php

// change the following paths if necessary
defined('APPHP_PATH') || define('APPHP_PATH', dirname(__FILE__));
// directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
// production | debug | demo | test
defined('APPHP_MODE') or define('APPHP_MODE', 'hidden');

$apphp = dirname(__FILE__) . '/../framework/Apphp.php';
$config = '';

require_once($apphp);
\A::init($config)->run();


