<?php
/**
 * Public index file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 */	  

// change the following paths if necessary
defined('APPHP_PATH') || define('APPHP_PATH', dirname(__FILE__));
// directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
// production | debug | demo | test
defined('APPHP_MODE') or define('APPHP_MODE', 'production'); 


$apphp = dirname(__FILE__).'/../../framework/Apphp.php';
$config = APPHP_PATH.'/protected/config/';

require_once($apphp);
A::init($config)->run();
