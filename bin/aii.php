<?php

// Change the following paths if necessary
defined('APPHP_PATH') || define('APPHP_PATH', dirname(__FILE__));
// Directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
// Console mode only
defined('APPHP_MODE') or define('APPHP_MODE', 'console');


$apphp = dirname(__FILE__) . '/../framework/Apphp.php';
$config = APPHP_PATH . '/protected/config/';

require_once($apphp);
A::init($config)->run();

// We get automatically $argv and $argc, as we run command line command
$console = new CConsole($argv);
$consoleCommand = new CConsoleCommand(
    $console->getCommand(),
    $console->getParams()
);
$consoleCommand->run();
