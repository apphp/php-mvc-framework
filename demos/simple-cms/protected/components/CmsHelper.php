<?php

class CmsHelper extends CComponent
{

    /**
     * Cuts the string by whole words up to maximum length. 
     * @param string $string
     * @param int $maxLength
     * @return string
     */
    public static function strTruncate($string, $maxLength)
    {
        $string = substr($string, 0, $maxLength);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string;
    }
    
}