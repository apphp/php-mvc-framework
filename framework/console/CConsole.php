<?php
/**
 * CConsole core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
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
     *
     * @param  array  $argv
     */
    public function __construct($argv = [])
    {
        $this->argv = $argv;
    }

    /**
     * Get command
     *
     * @return mixed|string
     */
    public function getCommand()
    {
        return ! empty($this->argv[1]) ? $this->argv[1] : '';
    }

    /**
     * Get parameters
     *
     * @return mixed|string
     */
    public function getParams()
    {
        return ! empty($this->argv[2]) ? $this->argv[2] : '';
    }

    /**
     * Draw green line
     *
     * @param  string  $string
     *
     * @return string
     */
    public static function green($string = '')
    {
        return "\e[0;32m".$string."\e[0m";
    }

    /**
     * Draw yellow line
     *
     * @param  string  $string
     *
     * @return string
     */
    public static function yellow($string = '')
    {
        return "\e[0;33m".$string."\e[0m";
    }

    /**
     * Draw line with red background
     *
     * @param  string  $string
     * @param  bool  $padding
     *
     * @return string
     */
    public static function redbg($string = '', $padding = true)
    {
        $length = strlen($string) + 4;
        $output = '';

        if ($padding) {
            $output .= "\e[0;41m".str_pad(' ', $length, " ", STR_PAD_LEFT)."\e[0m".PHP_EOL;
        }
        $output .= "\e[0;41m".($padding ? '  ' : '').$string.($padding ? '  ' : '')."\e[0m".PHP_EOL;
        if ($padding) {
            $output .= "\e[0;41m".str_pad(' ', $length, " ", STR_PAD_LEFT)."\e[0m".PHP_EOL;
        }

        return $output;
    }

}