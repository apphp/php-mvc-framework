<?php
/**
 * CCaptcha widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):             PROTECTED:                  PRIVATE:
 * ----------               	----------                  ----------
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
	 *      'type'      => 'standard|math', // Type
	 *      'required'  => true,            // Whether required or not
	 *      'name'      => '',              // Unique name for captcha, used to prevent names overlapping
	 *      'id'        => '',              // Element ID
	 *      'return'    => true
	 *  ));
	 */
	public static function init($params = array())
	{
		parent::init($params);
		
		$output = '';
		$type = self::params('type', 'math');
		$required = (bool)self::params('required', true);
		$name = self::params('name', 'captchaResult');
		$value = self::params('value', '');
		$id = self::params('id', 'captcha_validation');
		$return = (bool)self::params('return', true);
		$htmlOptions = self::params('htmlOptions', array());
		
		$firstDigit = CHash::getRandomString(1, array('type' => 'positiveNumeric'));
		$secondDigit = CHash::getRandomString(1, array('type' => 'positiveNumeric'));
		$operations = array('+', '-', '*');
		$operator = $operations[rand(0, 2)];
		$requiredMark = '';// ($required) ? ' &#42; ' : '';
		
		// Set result in session var
		if ($operator == '+') $captchaResult = $firstDigit + $secondDigit;
		elseif ($operator == '-') $captchaResult = $firstDigit - $secondDigit;
		elseif ($operator == '*') $captchaResult = $firstDigit * $secondDigit;
		else $captchaResult = 0;
		
		A::app()->getSession()->set($name, $captchaResult);
		
		if (!empty($htmlOptions) && is_array($htmlOptions)) {
			$htmlOptions['class'] = isset($htmlOptions['class']) ? $htmlOptions['class'] . ' captcha-result' : 'captcha-result';
			$htmlOptions['autocomplete'] = 'off';
			$htmlOptions['data-required'] = ($required ? 'true' : 'false');
			$htmlOptions['maxlength'] = '20';
		} else {
			$htmlOptions = array('class' => 'captcha-result', 'autocomplete' => 'off', 'data-required' => ($required ? 'true' : 'false'), 'maxlength' => '20');
		}
		
		$output .= CHtml::openTag('div', array('class' => 'captcha'));
		$output .= CHtml::tag('label', array(), $requiredMark . A::t('core', 'How much it will be') . '<br><span class="captcha-match">' . $firstDigit . ' ' . $operator . ' ' . $secondDigit . ' = ?</span>') . self::NL;
		$output .= CHtml::textField($id, $value, $htmlOptions) . self::NL;
		$output .= CHtml::closeTag('div') . self::NL;
		
		if ($return) return $output;
		else echo $output;
	}
	
}
