<?php
/**
 * CCurrencySelector widget helper class file
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

class CCurrencySelector extends CWidgs
{
	
    const NL = "\n";
 
    /**
     * Draws currency selector
     * @param array $params
     * 
     * Usage: 
     *  echo CWidget::create('CCurrencySelector', array(
     *      'currencies' => array('en'=>array('name'=>'English', 'icon'=>''), 'es'=>array('name'=>'Espanol', 'icon'=>''), 'fr'=>array('name'=>'Francias', 'icon'=>'')),
     *      'display' => 'names|keys|icons',
     *      'currentCurrency' => A::app()->getCurrency(),
     *      'return' => true
     *  ));
     */
    public static function init($params = array())
    {       
		parent::init($params);

        $output 			= '';
        $tagName 			= 'div';
        
        $display 			= self::params('display', 'names');
        $currentCurrency 	= self::params('currentCurrency', '');
        $return 			= (bool)self::params('return', true);
		$currencies 		= self::params('currencies', array(), 'is_array');
        $totalCurrencies 	= count($currencies);
        
        if($totalCurrencies == 1){
            return '';        
        }else if($totalCurrencies < 6){
            // Render options as links
            $lastCurr = end($currencies);
            foreach($currencies as $key => $val){
                $currName = self::keyAt('name', $val, '');
                if($display == 'names'){
                    $displayValue = $currName;
                }else if($display == 'code'){
                    $displayValue = $currName;
                }else if($display == 'keys'){
                    $displayValue = strtoupper($key);
                }
                if($key == $currentCurrency){
                    $output .= CHtml::tag('span', array('class'=>'current', 'title'=>$currName), $displayValue).self::NL;
                }else{
                    $output .= CHtml::link($displayValue, 'currencies/change/currency/'.$key, array('title'=>$currName)).self::NL;
                }
                if($currName != $lastCurr){
                    $output .= ' | '; 
                } 
            }            
        }else{
            // Render options as dropdown list
            $output .= CHtml::openForm('currencies/change/', 'get', array('name'=>'frmCurrencySelector')).self::NL;
            $arrCurrencies = array();
            foreach($currencies as $key => $val){
				$arrCurrencies[$key] = self::keyAt('name', $val, '');
			}
            $output .= CHtml::dropDownList(
                'currency', $currentCurrency, $arrCurrencies,
                array(
                    'id'=>'selCurrencies',
                    'submit'=>''
                )
            );
            $output .= CHtml::closeForm().self::NL;
        }
        $output = CHtml::openTag($tagName, array('id'=>'currency-selector')).$output;
        $output .= CHtml::closeTag($tagName).self::NL;       
        
        if($return) return $output;
        else echo $output;
    }
    
}