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

            case '-h':
            case '--help':
            case 'help':
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

                $output .= CCacheClearCommand::handle($this->param);
                break;

            case 'make:controller':
                $output .= CMakeControllerCommand::handle($this->param);
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