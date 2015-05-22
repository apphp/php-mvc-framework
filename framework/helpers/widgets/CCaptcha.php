<?php
/**
 * CCaptcha widget helper class file
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

class CCaptcha
{
	
    const NL = "\n";

    /**
     * Draws captcha
     * 
     * Usage:
     *  CWidget::create('CCaptcha', array(
     *     'standard|math',
     *     true,
     *     array(
     *         'return'=>true
     *     )
     *  ));
     */
    public static function init($type = '', $required = true, $params = array())
    {
        $output = '';
        $return = isset($params['return']) ? $params['return'] : true;

        $firstDigit = CHash::getRandomString(1, array('type'=>'positiveNumeric'));
        $secondDigit = CHash::getRandomString(1, array('type'=>'positiveNumeric'));
        $operations = array('+', '-', '*');
        $operator = $operations[rand(0, 2)];
        $requiredMark = '';// ($required) ? ' &#42; ' : '';
        
        // set result in session var
        if($operator == '+') $captchaResult = $firstDigit + $secondDigit;
        else if($operator == '-') $captchaResult = $firstDigit - $secondDigit;
        else if($operator == '*') $captchaResult = $firstDigit * $secondDigit;
        else $captchaResult = 0;        

        A::app()->getSession()->set('captchaResult', $captchaResult);
            
        $output .= CHtml::openTag('div', array('class'=>'captcha'));
        $output .= CHtml::tag('label', array(), $requiredMark.A::t('core', 'How much it will be').'<br><span class="captcha-match">'.$firstDigit.' '.$operator.' '.$secondDigit.' = ?</span>').self::NL;
        $output .= CHtml::textField('captcha_validation', '', array('class'=>'captcha-result', 'data-required'=>($required ? 'true' : 'false'), 'maxlength'=>'20')).self::NL;
        $output .= CHtml::closeTag('div').self::NL;
		
        if($return) return $output;
        else echo $output;
    }    
   
}
