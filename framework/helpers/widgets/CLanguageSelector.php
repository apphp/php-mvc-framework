<?php
/**
 * CLanguageSelector widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init
 * 
 */	  

class CLanguageSelector
{
    const NL = "\n";
 
    /**
     * Draws language selector
     * @param array $params
     * 
     * Usage: 
     *  echo CWidget::create('CLanguageSelector', array(
     *      'languages' => array('en'=>array('name'=>'English', 'icon'=>''), 'es'=>array('name'=>'Espanol', 'icon'=>''), 'fr'=>array('name'=>'Francias', 'icon'=>'')),
     *      'display' => 'names|keys|icons',
     *      'imagesPath' => 'images/langs/',
     *      'currentLanguage' => A::app()->getLanguage(),
     *      'return' => true
     *  ));
     */
    public static function init($params = array())
    {       
        $output = '';
        $tagName = 'div';
        $languages = isset($params['languages']) ? $params['languages'] : array();
        $display = isset($params['display']) ? $params['display'] : 'names';
        $imagesPath = isset($params['imagesPath']) ? $params['imagesPath'] : '';
        $currentLang = isset($params['currentLanguage']) ? $params['currentLanguage'] : '';
        $return = isset($params['return']) ? (bool)$params['return'] : true;
        
        $totalLangs = count($languages);
        if($totalLangs == 1){
            return '';
        }else if($totalLangs < 6){
            // render options as links
            $lastLang = end($languages);
            foreach($languages as $key => $lang){
                $langName = isset($lang['name']) ? $lang['name'] : '';
                $langIcon = isset($lang['icon']) ? $lang['icon'] : '';
                if($display == 'names'){
                    $displayValue = $langName;
                }else if($display == 'icons'){
                    $displayValue = '<img src="'.$imagesPath.$langIcon.'" alt="'.$langIcon.'" />';
                }else if($display == 'keys'){
                    $displayValue = strtoupper($key);
                }
                if($key == $currentLang){
                    $output .= CHtml::tag('span', array('class'=>'current', 'title'=>$langName), $displayValue).self::NL;
                }else{
                    $output .= CHtml::link($displayValue, 'languages/change/lang/'.$key, array('title'=>$langName)).self::NL;
                }
                if($langName != $lastLang){
                    $output .= ($display == 'icons') ? ' ' : ' | '; 
                } 
            }
            
            $output = trim($output, ' | ');
        }else{
            // render options as dropdown list
            $output .= CHtml::openForm('languages/change/', 'get', array('name'=>'frmLangSelector')).self::NL;            
            $arrLanguages = array();
            foreach($languages as $key => $val) $arrLanguages[$key] = $val['name'];            
            $output .= CHtml::dropDownList(
                'lang', $currentLang, $arrLanguages,
                array(
                    'id'=>'selLanguages',
                    'submit'=>''
                )
            );
            $output .= CHtml::closeForm().self::NL;
        }
        
        $final_output = CHtml::openTag($tagName, array('id'=>'language-selector'));
        $final_output .= $output;
        $final_output .= CHtml::closeTag($tagName).self::NL;       
        
        if($return) return $output;
        else echo $output;
    }
    
}