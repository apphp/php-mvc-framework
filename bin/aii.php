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

//print_r($_SERVER['argc']);
//print_r($_SERVER['argv']);

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
            $output .= "  ".green("-v, --version")."\t\tDisplay this application version". PHP_EOL;

            //-q, --quiet           Do not output any message
            //-n, --no-interaction  Do not ask any interactive question
            $output .= PHP_EOL;

            $output .= yellow("Available commands:") . PHP_EOL;
            $output .= "  ".green("cache:clear")."\t\tFlush specific application cache". PHP_EOL;
            $output .= "  ".green("cache:clear-all")."\tFlush all application cache". PHP_EOL;

            break;

        case '-v':
        case '-version':

            $output .= 'ApPHP Framework ' . green(A::version());

            break;

        case 'cache:clear':
        case 'cache:clear-all':

            if ($command === 'cache:clear-all') {
                $param = 'all';
            }

            if (empty($param)) {
                $output .= redbg("No cache type for deleting is defined. Type cache:clear -help") . PHP_EOL;
            } elseif ($param === '-h' || $param === '-help') {
                $output .= yellow("Usage:") . PHP_EOL;
                $output .= "  cache:clear-all\tFlush all application cache". PHP_EOL;
                $output .= "  cache:clear [type]\tFlush specific application cache". PHP_EOL;
                $output .= "  \t\t\t[type] - the type of cache to be removed: 'db', 'css', 'js' or 'all'". PHP_EOL;
            } elseif (in_array($param, array('db', 'css', 'js', 'all'))) {
                if($param == 'db' || $param == 'all'){
                    if (CConfig::get('cache.db.path') == '') {
                        $output .= redbg("Config value 'cache.db.path' is not defined. Check your configuration file.") . PHP_EOL;
                    }else{
                        $result = CFile::emptyDirectory(CConfig::get('cache.db.path'), array('index.html'));
                        $output .= 'DB cache ' . ($result ? 'successfully cleaned' : 'error');
                    }
                }
                if($param == 'css' || $param == 'all'){
                    if (CConfig::get('compression.css.path') == '') {
                        $output .= redbg("Config value 'compression.css.path' is not defined. Check your configuration file.") . PHP_EOL;
                    }else{
                        $result = CFile::emptyDirectory(CConfig::get('compression.css.path'), array('index.html'));
                        $output .= 'CSS cache ' . ($result ? 'successfully cleaned' : 'error');
                    }
                }
                if($param == 'js' || $param == 'all'){
                    if (CConfig::get('compression.js.path') == '') {
                        $output .= redbg("Config value 'compression.js.path' is not defined. Check your configuration file.") . PHP_EOL;
                    }else{
                        $result = CFile::emptyDirectory(CConfig::get('compression.js.path'), array('index.html'));
                        $output .= 'JS cache ' . ($result ? 'successfully cleaned' : 'error');
                    }
                }
            }

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