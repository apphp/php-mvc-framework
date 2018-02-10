<?php
/**
 * CBreadCrumbs widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init
 * 
 */	  

class CBreadCrumbs extends CWidgs
{
	
    const NL = "\n";

    /**
     * Draws breadcrumbs
     * @param array $params
     *
     * Usage:
     *  CWidget::create('CBreadCrumbs', array(
     *      'links' => array(
     *          array('label'=>'Label A', 'url'=>'url1/'),
     *          array('label'=>'Label B', 'url'=>'url2/'),
     *      ),
     *      'wrapperClass' => '',
     *      'wrapperTag' => '',
     *      'linkWrapperTag' => '',
     *      'separator' => '&nbsp;/&nbsp;',
     *      'return' => true
     *  ));
     */
    public static function init($params = array())
    {
		parent::init($params);
		
        $output 		= '';
		$wrapperTag 	= self::params('wrapperTag', 'div');		
        $links 			= self::params('links', '');
		$linkWrapperTag = self::params('linkWrapperTag', '');
        $separator 		= self::params('separator', '&raquo;');
        $return 		= (bool)self::params('return', true);
		$wrapperClass 	= self::params('wrapperClass', 'breadcrumbs');
        $htmlOptions 	= array('class'=>$wrapperClass);
        
        if(is_array($links)){
			if(!empty($wrapperTag)) $output .= CHtml::openTag($wrapperTag, $htmlOptions).self::NL;
            $counter = 0;
            foreach($params['links'] as $item => $val){
                $url = self::keyAt('url', $val, '');
                $label = self::keyAt('label', $val, '');

				if(!empty($linkWrapperTag)) $output .= CHtml::openTag($linkWrapperTag, array()).self::NL;
				
                if($counter) $output .= ' '.$separator.' ';
                if(!empty($url)) $output .= CHtml::link($label, $url);
                else $output .= CHtml::tag('span', array(), $label).self::NL;
				
				if(!empty($linkWrapperTag)) $output .= CHtml::closeTag($linkWrapperTag).self::NL;
                
                $counter++;
            }
            
            if(!empty($wrapperTag)) $output .= CHtml::closeTag($wrapperTag).self::NL;
        }
        
        if($return) return $output;
        else echo $output;
    }    
    
}