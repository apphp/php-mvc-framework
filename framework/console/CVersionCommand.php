<?php

/**
 * CVersionCommand console core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):            PROTECTED:               PRIVATE (static):
 * ---------------            ---------------           ---------------
 * handle (static)
 */

class CVersionCommand implements IConsoleCommand
{

    /**
     * Handle specific console command
     *
     * @return string
     */
    public static function handle()
    {
        $output = 'ApPHP Framework '.CConsole::green(A::version());

        return $output;
    }

}
