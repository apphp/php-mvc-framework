<?php
/**
 * CLocale is a helper class that provides a set of helper methods for data localization
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * date
 * getDateTimeFormats
 * getDateFormats
 * getTimeFormats
 * getShortTimeFormats
 * 
 */

class CLocale
{

    protected static $_arrDateFormats = [
            'Y-m-d' => ['preview' => '[ Y-m-d ]', 'converted_format' => '%Y-%m-%d'],
            'm-d-Y' => ['preview' => '[ m-d-Y ]', 'converted_format' => '%m-%d-%Y'],
            'd-m-Y' => ['preview' => '[ d-m-Y ]', 'converted_format' => '%d-%m-%Y'],

            'Y M d'  => ['preview' => '[ Y M d ]', 'converted_format' => '%Y %M %d'],
            'M d Y'  => ['preview' => '[ M d Y ]', 'converted_format' => '%M %d %Y'],
            'd M Y'  => ['preview' => '[ d M Y ]', 'converted_format' => '%d %M %Y'],
            'M d, Y' => ['preview' => '[ M d, Y ]', 'converted_format' => '%M %d, %Y'],
            'd M, Y' => ['preview' => '[ d M, Y ]', 'converted_format' => '%d %M, %Y'],

            'Y M j'  => ['preview' => '[ Y M j ]', 'converted_format' => '%Y %M %j'],
            'M j, Y' => ['preview' => '[ M j, Y ]', 'converted_format' => '%M %j, %Y'],
            'j M, Y' => ['preview' => '[ j M, Y ]', 'converted_format' => '%j %M, %Y'],

            'Y F d' => ['preview' => '[ Y F d ]', 'converted_format' => '%Y %F %d'],
            'F d Y' => ['preview' => '[ F d Y ]', 'converted_format' => '%F %d %Y'],
            'd F Y' => ['preview' => '[ d F Y ]', 'converted_format' => '%d %F %Y'],

            'Y F j'  => ['preview' => '[ Y F j ]', 'converted_format' => '%Y %F %j'],
            'F j, Y' => ['preview' => '[ F j, Y ]', 'converted_format' => '%F %j, %Y'],
            'j F, Y' => ['preview' => '[ j F, Y ]', 'converted_format' => '%j %F, %Y'],
        ];

    protected static $_arrTimeFormats = [
            'H:i:s' => ['preview' => '[ H:i:s ]', 'converted_format' => '%H:%i:%s'],
            'h:i:s' => ['preview' => '[ h:i:s ]', 'converted_format' => '%h:%i:%s'],
            'g:i:s' => ['preview' => '[ g:i:s ]', 'converted_format' => '%g:%i:%s'],

            'h:i a' => ['preview' => '[ h:i a ]', 'converted_format' => '%h:%i %a'],
            'h:i A' => ['preview' => '[ h:i A ]', 'converted_format' => '%h:%i %A'],
            'g:i a' => ['preview' => '[ g:i a ]', 'converted_format' => '%g:%i %a'],
            'g:i A' => ['preview' => '[ g:i A ]', 'converted_format' => '%g:%i %A'],
        ];

    protected static $_arrShortTimeFormats
        = [
            'H:i' => ['preview' => '[ H:i ]', 'converted_format' => '%H:%i'],
            'h:i' => ['preview' => '[ h:i ]', 'converted_format' => '%h:%i'],
            'g:i' => ['preview' => '[ g:i ]', 'converted_format' => '%g:%i'],
        ];

    protected static $_arrDateTimeFormats
        = [
            'Y-m-d H:i:s'   => ['preview' => '[ Y-m-d H:i:s ] ', 'converted_format' => '%Y-%m-%d %H:%i:%s'],
            'm-d-Y H:i:s'   => ['preview' => '[ m-d-Y H:i:s ] ', 'converted_format' => '%m-%d-%Y %H:%i:%s'],
            'd-m-Y H:i:s'   => ['preview' => '[ d-m-Y H:i:s ] ', 'converted_format' => '%d-%m-%Y %H:%i:%s'],
            'm-d-Y h:i:s'   => ['preview' => '[ m-d-Y H:i:s ] ', 'converted_format' => '%m-%d-%Y %h:%i:%s'],
            'd-m-Y h:i:s'   => ['preview' => '[ d-m-Y H:i:s ] ', 'converted_format' => '%d-%m-%Y %h:%i:%s'],
            'm-d-Y g:ia'    => ['preview' => '[ m-d-Y g:ia ] ', 'converted_format' => '%m-%d-%Y %g:%i%a'],
            'd-m-Y g:ia'    => ['preview' => '[ d-m-Y g:ia ] ', 'converted_format' => '%d-%m-%Y %g:%i%a'],
            'M d, Y g:ia'   => ['preview' => '[ M d, Y g:ia ] ', 'converted_format' => '%M %d, %Y %g:%i%a'],
            'd M, Y g:ia'   => ['preview' => '[ d M, Y g:ia ] ', 'converted_format' => '%d %M, %Y %g:%i%a'],
            'F j Y, g:ia'   => ['preview' => '[ F j Y, g:ia ] ', 'converted_format' => '%F %j %Y, %g:%i%a'],
            'j F Y, g:ia'   => ['preview' => '[ j F Y, g:ia ] ', 'converted_format' => '%j %F %Y, %g:%i%a'],
            'D, F j Y g:ia' => ['preview' => '[ D, F j Y g:ia ] ', 'converted_format' => '%D, %F %j %Y %g:%i%a'],
            'D, M d Y g:ia' => ['preview' => '[ D, M d Y g:ia ] ', 'converted_format' => '%D, %M %d %Y %g:%i%a'],
        ];

    /**
     * Transforms the given date into localazed date
     *
     * @param  string  $format
     * @param  string  $date
     * @param  bool  $unixFormat
     * @return string
     */
    public static function date($format = '', $date = '', $unixFormat = false)
    {
        $dateFormat = null;
        $search     = [];
        $replace    = [];
        $result     = '';
        $amPm       = '';

        if ($unixFormat) {
            $date = ! empty($date) ? date('Y-m-d H:i:s', $date) : date('Y-m-d H:i:s');
        } else {
            $date = ! empty($date) ? $date : date('Y-m-d H:i:s');
        }

        if (isset(self::$_arrDateTimeFormats[$format])) {
            $dateFormat = self::$_arrDateTimeFormats[$format];
            $parts      = explode(' ', $date);

            $dateParts = isset($parts[0]) ? explode('-', $parts[0]) : [];
            $year      = isset($dateParts[0]) ? $dateParts[0] : '';
            $month     = isset($dateParts[1]) ? $dateParts[1] : '';
            $day       = isset($dateParts[2]) ? $dateParts[2] : '';
            $weekDay   = date('w', strtotime($date)) + 1;

            $timeParts = isset($parts[1]) ? explode(':', $parts[1]) : [];
            $hour      = isset($timeParts[0]) ? $timeParts[0] : '';
            $hour24    = $hour;
            $hour12    = ($hour >= 13 ? $hour - 12 : $hour);
            $minute    = isset($timeParts[1]) ? $timeParts[1] : '';
            $second    = isset($timeParts[2]) ? $timeParts[2] : '';

            $amPm = ($hour24 < 12) ? A::t('i18n', 'amName') : A::t('i18n', 'pmName');

            $convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';
        } elseif (isset(self::$_arrDateFormats[$format])) {
            $dateFormat = self::$_arrDateFormats[$format];

            $parts     = explode(' ', $date);
            $dateParts = isset($parts[0]) ? explode('-', $parts[0]) : [];

            $year     = isset($dateParts[0]) ? $dateParts[0] : '';
            $month    = isset($dateParts[1]) ? $dateParts[1] : '';
            $day      = isset($dateParts[2]) ? $dateParts[2] : '';
            $dayParts = explode(' ', $day);
            $day      = isset($day[0]) ? $dayParts[0] : '';

            $convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';
        } elseif (isset(self::$_arrTimeFormats[$format])) {
            $dateFormat = self::$_arrTimeFormats[$format];

            if (strlen($date) > 8) {
                $parts     = explode(' ', $date);
                $timeParts = isset($parts[1]) ? explode(':', $parts[1]) : [];
            } else {
                $timeParts = explode(':', $date);
            }

            $hour   = isset($timeParts[0]) ? $timeParts[0] : '';
            $hour24 = $hour;
            $hour12 = ($hour >= 13 ? $hour - 12 : $hour);
            $minute = isset($timeParts[1]) ? $timeParts[1] : '';
            $second = isset($timeParts[2]) ? $timeParts[2] : '';

            $amPm = ($hour24 < 12) ? A::t('i18n', 'amName') : A::t('i18n', 'pmName');

            $convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';
        } elseif (isset(self::$_arrShortTimeFormats[$format])) {
            $dateFormat = self::$_arrShortTimeFormats[$format];

            if (strlen($date) > 5) {
                $parts     = explode(' ', $date);
                $timeParts = isset($parts[1]) ? explode(':', $parts[1]) : [];
            } else {
                $timeParts = explode(':', $date);
            }

            $hour   = isset($timeParts[0]) ? $timeParts[0] : '';
            $hour24 = $hour;
            $hour12 = ($hour >= 13 ? $hour - 12 : $hour);
            $minute = isset($timeParts[1]) ? $timeParts[1] : '';

            $convertedFormat = isset($dateFormat['converted_format']) ? $dateFormat['converted_format'] : '';
        } else {
            $result = date($format, strtotime($date));
        }

        if ($dateFormat) {
            switch ($format) {
                /*
                |---------------------------------------------------
                | Date Formats
                |---------------------------------------------------
                */
                case 'Y-m-d':    /* 2015-01-31 */
                case 'm-d-Y':    /* 01-31-2015 */
                case 'd-m-Y':    /* 31-01-2015 */

                    $search  = ['%Y', '%m', '%d'];
                    $replace = [$year, $month, $day];
                    break;

                case 'Y M d':    /* 2015 Oct 01 */
                case 'M d Y':    /* Oct 01 2015 */
                case 'd M Y':    /* 01 Oct 2015 */
                case 'M d, Y':    /* Oct 01, 2015 */
                case 'd M, Y':    /* 01 Oct, 2015 */

                    $search  = ['%Y', '%M', '%d'];
                    $replace = [$year, A::t('i18n', 'monthNames.abbreviated.'.(int)$month), $day];
                    break;

                case 'Y M j':    /* 2015 Oct 1 */
                case 'M j, Y':    /* Oct 1, 2015 */
                case 'j M, Y':    /* 1 Oct, 2015 */

                    $search  = ['%Y', '%M', '%j'];
                    $replace = [$year, A::t('i18n', 'monthNames.abbreviated.'.(int)$month), (int)$day];
                    break;

                case 'Y F d':    /* 2015 October 01 */
                case 'F d Y':    /* October 01 2015 */
                case 'd F Y':    /* 01 October 2015 */

                    $search  = ['%Y', '%F', '%d'];
                    $replace = [$year, A::t('i18n', 'monthNames.wide.'.(int)$month), $day];
                    break;

                case 'Y F j':    /* 2015 October 1 */
                case 'F j, Y':    /* October 1, 2015 */
                case 'j F, Y':  /* 1 October, 2015 */

                    $search  = ['%Y', '%F', '%j'];
                    $replace = [$year, A::t('i18n', 'monthNames.wide.'.(int)$month), (int)$day];
                    break;

                /*
                |---------------------------------------------------
                | Time Formats
                |---------------------------------------------------
                */
                case 'H:i:s':    /* 13:53:20 */
                case 'h:i:s':    /* 01:53:20 */
                case 'g:i:s':    /* 1:53:20 */

                    $search  = ['%H', '%h', '%g', '%i', '%s'];
                    $replace = [$hour24, $hour12, (int)$hour12, $minute, $second];
                    break;

                case 'h:i a':    /*  01:47 pm */
                case 'g:i a':    /*  1:47 pm */

                    $search  = ['%h', '%g', '%i', '%a'];
                    $replace = [$hour12, (int)$hour12, $minute, strtolower($amPm)];
                    break;

                case 'h:i A':    /*  01:47 PM */
                case 'g:i A':    /*  1:47 PM */

                    $search  = ['%h', '%g', '%i', '%A'];
                    $replace = [$hour12, (int)$hour12, $minute, strtoupper($amPm)];
                    break;

                case 'H:i':        /*  13:47 */
                case 'h:i':        /*  01:47 */
                case 'g:i':        /*   1:47 */

                    $search  = ['%H', '%h', '%g', '%i'];
                    $replace = [$hour24, $hour12, (int)$hour12, $minute];
                    break;

                /*
                |---------------------------------------------------
                | DateTime Formats
                |---------------------------------------------------
                */
                case 'Y-m-d H:i:s':        /* 2015-01-31 13:02:59 */
                case 'm-d-Y H:i:s':        /* 01-31-2015 13:02:59 */
                case 'd-m-Y H:i:s':        /* 31-01-2015 13:02:59 */
                case 'm-d-Y h:i:s':        /* 01-31-2015 01:02:59 */
                case 'd-m-Y h:i:s':        /* 31-01-2015 01:02:59 */

                    $search  = ['%Y', '%m', '%d', '%H', '%h', '%g', '%i', '%s'];
                    $replace = [$year, $month, $day, $hour24, $hour12, (int)$hour12, $minute, $second];
                    break;

                case 'm-d-Y g:ia':        /* 2015-01-31 1:02pm */
                case 'd-m-Y g:ia':        /* 31-01-2015 1:02pm */

                    $search  = ['%Y', '%m', '%d', '%g', '%i', '%a'];
                    $replace = [$year, $month, $day, (int)$hour12, $minute, strtolower($amPm)];
                    break;

                case 'M d, Y g:ia':        /* Oct 09, 2015 1:02pm */
                case 'd M, Y g:ia':        /* 09 Oct, 2015 1:02pm */

                    $monthAbbrev = A::t('i18n', 'monthNames.abbreviated.'.(int)$month);
                    $search      = ['%Y', '%M', '%d', '%g', '%i', '%a'];
                    $replace     = [$year, $monthAbbrev, $day, (int)$hour12, $minute, strtolower($amPm)];
                    break;

                case 'F j Y, g:ia':        /* October 1 2015, 1:02pm */
                case 'j F Y, g:ia':        /* 1 October 2015, 1:02pm */

                    $monthWide = A::t('i18n', 'monthNames.wide.'.(int)$month);
                    $search    = ['%Y', '%F', '%j', '%g', '%i', '%a'];
                    $replace   = [$year, $monthWide, (int)$day, (int)$hour12, $minute, strtolower($amPm)];
                    break;

                case 'D, F j Y g:ia':    /* Mon, October 1 2015 1:02pm */
                case 'D, M d Y g:ia':   /* Mon, Oct 1 2015 1:02pm */

                    $monthWide     = A::t('i18n', 'monthNames.wide.'.(int)$month);
                    $monthAbbrev   = A::t('i18n', 'monthNames.abbreviated.'.(int)$month);
                    $weekDayAbbrev = A::t('i18n', 'weekDayNames.abbreviated.'.(int)$weekDay);
                    $search        = ['%Y', '%F', '%M', '%j', '%d', '%D', '%g', '%i', '%a'];
                    $replace       = [
                        $year,
                        $monthWide,
                        $monthAbbrev,
                        (int)$day,
                        $day,
                        $weekDayAbbrev,
                        (int)$hour12,
                        $minute,
                        strtolower($amPm)
                    ];
                    break;

                default:
                    $result = $date;
                    break;
            }

            if ( ! empty($search) && ! empty($replace)) {
                $result = str_replace($search, $replace, $convertedFormat);
            }
        }

        return $result;
    }

    /**
     * Returns array of datetime formats supported by system
     *
     * @return array
     */
    public static function getDateTimeFormats()
    {
        $result = [];

        foreach (self::$_arrDateTimeFormats as $key => $dateTimeFormat) {
            $result[$key] = $dateTimeFormat['preview'];
        }

        return $result;
    }

    /**
     * Returns array of date formats supported by system
     *
     * @return array
     */
    public static function getDateFormats()
    {
        $result = [];

        foreach (self::$_arrDateFormats as $key => $dateFormat) {
            $result[$key] = $dateFormat['preview'];
        }

        return $result;
    }

    /**
     * Returns array of time formats supported by system
     *
     * @return array
     */
    public static function getTimeFormats()
    {
        $result = [];

        foreach (self::$_arrTimeFormats as $key => $timeFormat) {
            $result[$key] = $timeFormat['preview'];
        }

        return $result;
    }

    /**
     * Returns array of short time formats supported by system
     *
     * @return array
     */
    public static function getShortTimeFormats()
    {
        $result = [];

        foreach (self::$_arrShortTimeFormats as $key => $timeFormat) {
            $result[$key] = $timeFormat['preview'];
        }

        return $result;
    }
}
