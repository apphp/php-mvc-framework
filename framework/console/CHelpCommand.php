<?php

/**
 * CHelpCommand console core class file
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

class CHelpCommand implements IConsoleCommand
{

    /**
     * Handle specific console command
     * @return string
     */
    public static function handle()
    {
        $output = '';
        $output .= 'ApPHP Framework ' . CConsole::green(A::version()) . PHP_EOL;
        $output .= PHP_EOL;

        $output .= CConsole::yellow("Usage:") . PHP_EOL;
        $output .= "  command [options] [arguments]" . PHP_EOL . PHP_EOL;

        $output .= CConsole::yellow("Options:") . PHP_EOL;
        $output .= "  ".CConsole::green("-h, --help")."\t\tDisplay this help message". PHP_EOL;
        $output .= "  ".CConsole::green("-v, --version")."\t\tDisplay this application version". PHP_EOL;

        //-q, --quiet           Do not output any message
        //-n, --no-interaction  Do not ask any interactive question
        $output .= PHP_EOL;

        $output .= CConsole::yellow("Available commands:") . PHP_EOL;

        $output .= "  ".CConsole::green("help")."\t\t\tHelp on console commands". PHP_EOL;

        $output .= CConsole::yellow("cache") . PHP_EOL;
        $output .= "  ".CConsole::green("cache:clear")."\t\tFlush specific application cache". PHP_EOL;
        $output .= "  ".CConsole::green("cache:clear-all")."\tFlush all application cache". PHP_EOL;

        $output .= CConsole::yellow("make") . PHP_EOL;
        $output .= "  ".CConsole::green("make:controller")."\tCreate controller". PHP_EOL;

        return $output;
    }

}
