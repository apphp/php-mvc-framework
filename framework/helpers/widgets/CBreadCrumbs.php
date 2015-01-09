<?php
/**
 * CBreadCrumbs widget helper class file
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

class CBreadCrumbs
{
    const NL = "\n";

    /**
     * Draws breadcrumbs
     * @param array $params
     *
     * Usage:
     *  CWidget::create('CBreadCrumbs', array(
     *      'links' => array(
     *          array('label'=>'Label A'), 'url'=>'url1/'),
     *          array('label'=>'Label B'), 'url'=>'url2/'),
     *      ),
     *      'class' => '',
     *      'separator' => '&nbsp;/&nbsp;',
     *      'return' => true
     *  ));
     */
    public static function init($params = array())
    {
        $output = '';
        $tagName = 'div';
        $class = (isset($params['class']) && !empty($params['class'])) ? $params['class'] : 'breadcrumbs';        
        $links = isset($params['links']) ? $params['links'] : '';        
        $separator = isset($params['separator']) ? $params['separator'] : '&raquo;';        
        $return = isset($params['return']) ? $params['return'] : true;
        $htmlOptions = array('class'=>$class);
        
        if(is_array($links)){            
            $output .= CHtml::openTag($tagName, $htmlOptions).self::NL;
            $counter = 0;
            foreach($params['links'] as $item => $val){
                $url = isset($val['url']) ? $val['url'] : '';
                $label = isset($val['label']) ? $val['label'] : '';                

                if($counter) $output .= ' '.$separator.' ';
                if(!empty($url)) $output .= CHtml::link($label, $url);
                else $output .= CHtml::tag('span', array(), $label).self::NL;
                
                $counter++;
            }
            
            $output .= CHtml::closeTag($tagName).self::NL;
        }
        
        if($return) return $output;
        else echo $output;
    }    
    
}