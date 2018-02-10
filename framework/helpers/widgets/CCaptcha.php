<?php
/**
 * CCaptcha widget helper class file
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

class CCaptcha extends CWidgs
{
	
    const NL = "\n";

    /**
     * Draws captcha
     * 
     * Usage:
     *  CWidget::create('CCaptcha', array(
     *     	'type' 	=> 'standard|math',		// Type
     *      'required' 	=> true,			// Whether required or not
     *     	'name'		=> '',				// Unique name for captcha, used to prevent names overlapping
	 *     	'id'        => '',              // Element ID
     *      'return'	=> true
     *  ));
     */
    public static function init($params = array())
    {
		parent::init($params);		
		
        $output 		= '';
		$type 			= self::params('type', 'math');
		$required 		= (bool)self::params('required', true);
        $name 			= self::params('name', 'captchaResult');
        $id             = self::params('id', 'captcha_validation');
        $return 		= (bool)self::params('return', true);

        $firstDigit 	= CHash::getRandomString(1, array('type'=>'positiveNumeric'));
        $secondDigit 	= CHash::getRandomString(1, array('type'=>'positiveNumeric'));
        $operations 	= array('+', '-', '*');
        $operator 		= $operations[rand(0, 2)];
        $requiredMark 	= '';// ($required) ? ' &#42; ' : '';
        
        // Set result in session var
        if($operator == '+') $captchaResult = $firstDigit + $secondDigit;
        elseif($operator == '-') $captchaResult = $firstDigit - $secondDigit;
        elseif($operator == '*') $captchaResult = $firstDigit * $secondDigit;
        else $captchaResult = 0;        

        A::app()->getSession()->set($name, $captchaResult);
            
        $output .= CHtml::openTag('div', array('class'=>'captcha'));
        $output .= CHtml::tag('label', array(), $requiredMark.A::t('core', 'How much it will be').'<br><span class="captcha-match">'.$firstDigit.' '.$operator.' '.$secondDigit.' = ?</span>').self::NL;
        $output .= CHtml::textField($id, '', array('class'=>'captcha-result', 'autocomplete'=>'off', 'data-required'=>($required ? 'true' : 'false'), 'maxlength'=>'20')).self::NL;
        $output .= CHtml::closeTag('div').self::NL;
		
        if($return) return $output;
        else echo $output;
    }    
   
}
