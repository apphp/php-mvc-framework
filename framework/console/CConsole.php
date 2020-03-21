<?php
/**
 * CConsole core class file
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
 * getCommand
 * getParams
 * green (static)
 * yellow (static)
 * redbg (static)
 */

class CConsole
{
    /**
     * @var array
     */
    private $argv;

    /**
     * Class constructor
     */
    public function __construct($argv = array())
    {
        $this->argv = $argv;
    }

    /**
     * Get command
     * @return mixed|string
     */
    public function getCommand()
    {
        return !empty($this->argv[1]) ? $this->argv[1] : '';
    }

    /**
     * Get parameters
     * @return mixed|string
     */
    public function getParams()
    {
        return !empty($this->argv[2]) ? $this->argv[2] : '';
    }

    /**
     * Draw green line
     * @param  string  $string
     * @return string
     */
    public static function green($string = '')
    {
        return "\e[0;32m".$string."\e[0m";
    }

    /**
     * Draw yellow line
     * @param  string  $string
     * @return string
     */
    public static function yellow($string = '')
    {
        return "\e[0;33m".$string."\e[0m";
    }

    /**
     * Draw line with red background
     * @param  string  $string
     * @return string
     */
    public static function redbg($string = '')
    {
        return "\e[0;41m".$string."\e[0m";
    }


}