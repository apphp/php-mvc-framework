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
        $output = 'Controller created: ' . $param;

        return $output;
    }

}
