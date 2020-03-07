<?php

// change the following paths if necessary
defined('APPHP_PATH') || define('APPHP_PATH', dirname(__FILE__));
// directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
// hidden only
defined('APPHP_MODE') or define('APPHP_MODE', 'hidden');


$apphp = dirname(__FILE__) . '/../framework/Apphp.php';
$config = APPHP_PATH . '/protected/config/';

require_once($apphp);
A::init($config)->run();

function green($string = '')
{
    return "\e[0;32m".$string."\e[0m";
}
function yellow($string = '')
{
    return "\e[0;33m".$string."\e[0m";
}
function redbg($string = '')
{
    return "\e[0;41m".$string."\e[0m";
}

/**
 * Commands
 * --help
 * --version
 */

$output = '';

if (!empty($argv)) {

    $command = !empty($argv[1]) ? $argv[1] : '';
    $param = !empty($argv[2]) ? $argv[2] : '';

    switch ($command) {

        case '':
        case '-h':
        case '--help':

            $output .= 'ApPHP Framework ' . green(A::version()) . PHP_EOL;
            $output .= PHP_EOL;

            $output .= yellow("Usage:") . PHP_EOL;
            $output .= "  command [options] [arguments]" . PHP_EOL . PHP_EOL;

            $output .= yellow("Options:") . PHP_EOL;
            $output .= "  ".green("-h, --help")."\t\tDisplay this help message". PHP_EOL;
            $output .= "  ".green("-V, --version")."\t\tDisplay this application version". PHP_EOL;

            //-q, --quiet           Do not output any message
            //-n, --no-interaction  Do not ask any interactive question
            $output .= PHP_EOL;

            $output .= yellow("Available commands:") . PHP_EOL;
            $output .= "  ".green("cache:clear")."\t\tFlush specific application cache". PHP_EOL;
            $output .= "  ".green("cache:clearall")."\tFlush all application cache". PHP_EOL;

            break;

        case '-V':
        case '-version':

            $output .= 'ApPHP Framework ' . green(A::version());

            break;

        default:

            $output .= PHP_EOL;
            $output .= redbg("Command '".$command."' is not defined.") . PHP_EOL;
            $output .= 'Type "bin/aii --help" to check all commands and options.';
            break;
    }

    $output .= PHP_EOL;
}

echo $output;