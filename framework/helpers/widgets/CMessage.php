<?php
/**
 * CMessage widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init 
 * 
 */	  

class CMessage
{
	
    const NL = "\n";
    
    /**
     * Draws message
     * @param string $type
     * @param string $text
     * @param array $params
     * 
     * Usage:
     *  CWidget::create('CMessage', array(
     *     'info|success|error|warning|validation',
     *     'message',
     *     array(
     *     	   'id'=>'',
     *         'button'=>true,
     *         'return'=>true
     *     )
     *  ));
     */
    public static function init($type = '', $text = '', $params = array())
    {
        $output = '';
        $tagName = 'div';
        $htmlOptions = array();
        $allowedTypes = array('info', 'success', 'error', 'warning', 'validation');
        $return = isset($params['return']) ? $params['return'] : true;
        $button = isset($params['button']) ? $params['button'] : false;
        
        if(in_array($type, $allowedTypes)){
            $htmlOptions['class'] = 'alert alert-'.$type;
			if(isset($params['id'])) $htmlOptions['id'] = $params['id'];
            $output .= CHtml::openTag($tagName, $htmlOptions);
            if($button) $output .= '<button class="close" type="button">&times;</button>';
            $output .= $text;
            $output .= CHtml::closeTag($tagName).self::NL;            
        }
		
        if($return) return $output;
        else echo $output;
    }    
  
}