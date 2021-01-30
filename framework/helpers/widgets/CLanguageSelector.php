<?php
/**
 * CLanguageSelector widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):         PROTECTED:                  PRIVATE:
 * ----------               ----------                  ----------
 * init
 *
 */

class CLanguageSelector extends CWidgs
{

    /**
     * @const string new line
     */
    const NL = "\n";


    /**
     * Draws language selector
     *
     * @param  array  $params
     *
     * Usage:
     *  echo CWidget::create('CLanguageSelector', [
     *      'languages' => ['en'=>['name'=>'English', 'icon'=>''], 'es'=>['name'=>'Espanol', 'icon'=>''], 'fr'=>['name'=>'Francias', 'icon'=>'']],
     *      'display' => 'names|keys|icons|dropdown|list',
     *      'imagesPath' => 'images/langs/',
     *      'forceDrawing' => false,
     *      'currentLanguage' => A::app()->getLanguage(),
     *      'return' => true
     *  ]);
     */
    public static function init($params = [])
    {
        parent::init($params);

        $output       = '';
        $tagName      = 'div';
        $languages    = self::params('languages', []);
        $display      = self::params('display', 'names');
        $imagesPath   = self::params('imagesPath', '');
        $currentLang  = self::params('currentLanguage', '');
        $forceDrawing = self::params('forceDrawing', false);
        $class        = self::params('class', '');
        $return       = (bool)self::params('return', true);

        $totalLangs = count($languages);
        if ($totalLangs == 1 && ! $forceDrawing) {
            return '';
        } elseif ($totalLangs < 6 && in_array($display, ['names', 'keys', 'icons'])) {
            // Render options
            $totalLanguages = count($languages);
            $count          = 0;
            foreach ($languages as $key => $lang) {
                $langName = isset($lang['name']) ? $lang['name'] : '';
                $langIcon = (isset($lang['icon']) && ! empty($lang['icon']) && file_exists($imagesPath.$lang['icon']))
                    ? $lang['icon'] : 'no_image.png';

                if ($display == 'names') {
                    $displayValue = $langName;
                } elseif ($display == 'icons') {
                    $displayValue = '<img src="'.$imagesPath.$langIcon.'" alt="'.$langName.'" />';
                } elseif ($display == 'keys') {
                    $displayValue = strtoupper($key);
                } else {
                    $displayValue = $key;
                }

                if ($key == $currentLang) {
                    $output .= CHtml::tag('span', ['class' => 'current', 'title' => $langName], $displayValue).self::NL;
                } else {
                    $output .= CHtml::link($displayValue, 'languages/change/lang/'.$key, ['title' => $langName])
                        .self::NL;
                }
                if (++$count < $totalLanguages) {
                    $output .= ($display == 'icons') ? ' ' : ' | ';
                }
            }

            $output = trim($output, ' | ');
        } elseif ($display == 'list') {
            $output .= CHtml::openTag('ul', ['class' => $class]).self::NL;
            foreach ($languages as $key => $lang) {
                $langName = isset($lang['name']) ? $lang['name'] : '';
                $langIcon = (isset($lang['icon']) && ! empty($lang['icon']) && file_exists($imagesPath.$lang['icon']))
                    ? $lang['icon'] : 'no_image.png';

                $output .= '<li'.($key == $currentLang ? ' class="current"' : '').'>'.CHtml::link(
                        '<img src="'.$imagesPath.$langIcon.'" alt="'.$langName.'" /> &nbsp;&nbsp; '.$langName,
                        'languages/change/lang/'.$key,
                        ['title' => $langName]
                    ).'</li>'.self::NL;
            }
            $output .= CHtml::closeTag('ul').self::NL;
        } else {
            // Render options as dropdown list
            $output       .= CHtml::openForm('languages/change/', 'get', ['name' => 'frmLangSelector']).self::NL;
            $arrLanguages = [];
            foreach ($languages as $key => $val) {
                $arrLanguages[$key] = $val['name'];
            }
            $output .= CHtml::dropDownList(
                'lang',
                $currentLang,
                $arrLanguages,
                [
                    'id'     => 'selLanguages',
                    'submit' => '',
                    'class'  => $class,
                ]
            );
            $output .= CHtml::closeForm().self::NL;
        }

        //$final_output = CHtml::openTag($tagName, ['id'=>'language-selector']);
        //$final_output .= $output;
        //$final_output .= CHtml::closeTag($tagName).self::NL;

        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

}