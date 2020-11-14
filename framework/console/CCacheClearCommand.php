<?php

/**
 * CCacheClearCommand console core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):            PROTECTED:               PRIVATE (static):
 * ---------------            ---------------           ---------------
 * handle (static)
 */

class CCacheClearCommand implements IConsoleCommand
{

    /**
     * Handle specific console command
     *
     * @param  string  $param
     *
     * @return string
     */
    public static function handle($param = '')
    {
        $output = '';

        if (empty($param)) {
            $output .= CConsole::redbg("No cache type for deleting is defined. Type cache:clear -h or --help").PHP_EOL;
        }
        elseif ($param === '-h' || $param === '--help') {
            $output .= CConsole::yellow("Usage:") . PHP_EOL;
            $output .= "  cache:clear-all\tFlush all application cache". PHP_EOL;
            $output .= "  cache:clear [type]\tFlush specific application cache". PHP_EOL;
            $output .= "  \t\t\t[type] - the type of cache to be removed: 'db', 'css', 'js' or 'all'". PHP_EOL;
        }
        elseif (in_array($param, ['db', 'css', 'js', 'all'])) {
            if($param == 'db' || $param == 'all'){
                if (CConfig::get('cache.db.path') == '') {
                    $output .= CConsole::redbg("Config value 'cache.db.path' is not defined. Check your configuration file.") . PHP_EOL;
                }else{
                    $result = CFile::emptyDirectory(CConfig::get('cache.db.path'), ['index.html']);
                    $output .= 'DB cache ' . ($result ? 'successfully cleaned' : 'error');
                }
            }
            if($param == 'css' || $param == 'all'){
                if (CConfig::get('compression.css.path') == '') {
                    $output .= CConsole::redbg("Config value 'compression.css.path' is not defined. Check your configuration file.") . PHP_EOL;
                }else{
                    $result = CFile::emptyDirectory(CConfig::get('compression.css.path'), ['index.html']);
                    $output .= 'CSS cache ' . ($result ? 'successfully cleaned' : 'error');
                }
            }
            if($param == 'js' || $param == 'all'){
                if (CConfig::get('compression.js.path') == '') {
                    $output .= CConsole::redbg("Config value 'compression.js.path' is not defined. Check your configuration file.") . PHP_EOL;
                }else{
                    $result = CFile::emptyDirectory(CConfig::get('compression.js.path'), ['index.html']);
                    $output .= 'JS cache ' . ($result ? 'successfully cleaned' : 'error');
                }
            }
        }
        else {
            $output .= CConsole::redbg("No cache type for deleting is defined or wrong parameters. Type cache:clear -h or --help").PHP_EOL;
        }

        return $output;
    }

}


