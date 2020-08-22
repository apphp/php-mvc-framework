<?php

/**
 * CMakeControllerCommand console core class file
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

class CMakeControllerCommand implements IConsoleCommand
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
            $output .= CConsole::redbg("No model name is defined. Type make:controller -h or --help").PHP_EOL;
        }
        elseif ($param === '-h' || $param === '--help') {
            $output .= CConsole::yellow("Usage:") . PHP_EOL;
            $output .= "  make:controller [model]\t Create a new controller class". PHP_EOL;
            $output .= "  \t\t\t\t[model] - Generate a resource controller for the given model.". PHP_EOL;
        }
        elseif (CValidator::isVariable($param)) {
            // TODO: create controller
        }
        else {
            if (!CValidator::isVariable($param)){
                $output .= CConsole::redbg("The model name must be a valid controller name (alphanumeric, starts with letter and can contain an underscore)! Please re-enter.").PHP_EOL;
            } else {
                $output .= CConsole::redbg("No model name is defined or wrong parameters. Type make:controller -h or --help").PHP_EOL;
            }
        }

        //echo $param;
        //$output = 'Controller created: '.$param;

        return $output;
    }

}
