<?php
/**
 * CMessage widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2015 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init 
 * 
 */	  

class CMessage extends CWidgs
{
	
	/**
	 * @const string new line
	 */
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
		parent::init($params);

		// Change type to lowercase
		if(!CConfig::get('widgets.paramKeysSensitive')){
			$type = strtolower($type);
		}

		// Get param variables
        $return 	= self::params('return', true);
        $button 	= self::params('button', false);
		$param_id 	= self::params('id');

        $output = '';
        $tagName = 'div';
        $htmlOptions = array();	
        $type = (in_array($type, array('info', 'success', 'error', 'warning', 'validation'))) ? $type : '';
		
		if(!empty($text)){
			$htmlOptions['class'] = 'alert alert-'.$type;
			if($param_id) $htmlOptions['id'] = $param_id;
			$output .= CHtml::openTag($tagName, $htmlOptions);
			if($button) $output .= '<button class="close" type="button">&times;</button>';
			$output .= $text;
			$output .= CHtml::closeTag($tagName).self::NL;
		}

        if($return) return $output;
        else echo $output;
    }    

}
