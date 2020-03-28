<?php
/**
 * CConsoleCommand core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):            PROTECTED:               PRIVATE (static):
 * ---------------            ---------------           ---------------
 * __construct
 * run
 *
 */

class CConsoleCommand
{
    /**
     * @var string
     */
    private $param;
    private $command;

    /**
     * Class constructor
     *
     * @param  string  $command
     * @param  string  $param
     */
    public function __construct($command = '', $param = '')
    {
        $this->command = $command;
        $this->param = $param;
    }

    /**
     * Run command
     * @param bool $return
     * @return string
     */
    public function run($return = false)
    {
        $output = '';

        switch ($this->command) {

            case '':
            case '-h':
            case '--help':

                $output .= CHelpCommand::handle();
                break;

            case '-v':
            case '--version':

                $output .= CVersionCommand::handle();
                break;

            case 'cache:clear':
            case 'cache:clear-all':

                if ($this->command === 'cache:clear-all') {
                    $this->param = 'all';
                }

                if (empty($this->param)) {
                    $output .= CConsole::redbg("No cache type for deleting is defined. Type cache:clear -help") . PHP_EOL;
                } elseif ($this->param === '-h' || $this->param === '-help') {
                    $output .= CConsole::yellow("Usage:") . PHP_EOL;
                    $output .= "  cache:clear-all\tFlush all application cache". PHP_EOL;
                    $output .= "  cache:clear [type]\tFlush specific application cache". PHP_EOL;
                    $output .= "  \t\t\t[type] - the type of cache to be removed: 'db', 'css', 'js' or 'all'". PHP_EOL;
                } elseif (in_array($this->param, array('db', 'css', 'js', 'all'))) {
                    if($this->param == 'db' || $this->param == 'all'){
                        if (CConfig::get('cache.db.path') == '') {
                            $output .= CConsole::redbg("Config value 'cache.db.path' is not defined. Check your configuration file.") . PHP_EOL;
                        }else{
                            $result = CFile::emptyDirectory(CConfig::get('cache.db.path'), array('index.html'));
                            $output .= 'DB cache ' . ($result ? 'successfully cleaned' : 'error');
                        }
                    }
                    if($this->param == 'css' || $this->param == 'all'){
                        if (CConfig::get('compression.css.path') == '') {
                            $output .= CConsole::redbg("Config value 'compression.css.path' is not defined. Check your configuration file.") . PHP_EOL;
                        }else{
                            $result = CFile::emptyDirectory(CConfig::get('compression.css.path'), array('index.html'));
                            $output .= 'CSS cache ' . ($result ? 'successfully cleaned' : 'error');
                        }
                    }
                    if($this->param == 'js' || $this->param == 'all'){
                        if (CConfig::get('compression.js.path') == '') {
                            $output .= CConsole::redbg("Config value 'compression.js.path' is not defined. Check your configuration file.") . PHP_EOL;
                        }else{
                            $result = CFile::emptyDirectory(CConfig::get('compression.js.path'), array('index.html'));
                            $output .= 'JS cache ' . ($result ? 'successfully cleaned' : 'error');
                        }
                    }
                }

                break;

            default:

                $output .= PHP_EOL;
                $output .= CConsole::redbg("Command '".$this->command."' is not defined.") . PHP_EOL;
                $output .= 'Type "bin/aii --help" to check all commands and options.';
                break;
        }

        $output .= PHP_EOL;

        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }
}