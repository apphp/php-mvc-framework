<?php
/**
 * CTabs widget helper class file
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

class CTabs
{
    const NL = "\n";

    /**
     * Draws tabs
     * @param array $params
     * 
     * Notes:
     *   - to disable any field or button use: 'disabled'=>true
     *   
     * Usage: 
     *  echo CWidget::create('CTabs', array(
     *   'tabsWrapper'=>array('tag'=>'div', 'class'=>'title', 'id'=>''),
     *   // - optional - 'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs', 'id'=>''),
     *   'contentWrapper'=>array('tag'=>'div', 'class'=>'content', 'id'=>''),
     *   'contentMessage'=>$actionMessage,
     *   'contentAdditional'=>'',
     *   'tabs'=>array(
     *  	'General Settings' 	 =>array('href'=>'#tab1', 'id'=>'tab1', 'content'=>$tab1, 'active'=>true, 'htmlOptions'=>array()),
     *  	'Visual Settings'  	 =>array('href'=>'#tab2', 'id'=>'tab2', 'content'=>'Content for tab 2', 'htmlOptions'=>array()),
     *  	'Local Settings'  	 =>array('href'=>'#tab3', 'id'=>'tab3', 'content'=>'Content for tab 3', 'htmlOptions'=>array()),
     *  	'Email Settings'  	 =>array('href'=>'#tab4', 'id'=>'tab4', 'content'=>'Content for tab 4', 'htmlOptions'=>array()),
     *  	'Templates & Styles' =>array('href'=>'#tab5', 'id'=>'tab5', 'content'=>'Content for tab 5', 'htmlOptions'=>array()),
     *  	'Server Info'  		 =>array('href'=>'#tab6', 'id'=>'tab6', 'content'=>'Content for tab 6', 'htmlOptions'=>array()),
     *  	'Site Info'  		 =>array('href'=>'#tab7', 'id'=>'tab7', 'content'=>'Content for tab 7', 'htmlOptions'=>array()),
     *  	'Cron Jobs'  		 =>array('href'=>'#tab8', 'id'=>'tab8', 'content'=>'Content for tab 8', 'htmlOptions'=>array()),
     *   ),
     *   'events'=>array(
     *  	//'click'=>array('field'=>$errorField)
     *   ),
     *   'return'=>true,
     *  ));
    */
    public static function init($params = array())
    {       
        $output = '';
        $tagName = 'div';
        $tabs = isset($params['tabs']) ? $params['tabs'] : array();
		$tabsWrapper = isset($params['tabsWrapper']) ? $params['tabsWrapper'] : array();
		$tabsWrapperInner = isset($params['tabsWrapperInner']) ? $params['tabsWrapperInner'] : array();
		$contentWrapper = isset($params['contentWrapper']) ? $params['contentWrapper'] : array();
		$contentMessage = isset($params['contentMessage']) ? $params['contentMessage'] : '';
		$contentAdditional = isset($params['contentAdditional']) ? $params['contentAdditional'] : '';
		$return = isset($params['return']) ? $params['return'] : true;
		
		$output .= CHtml::openTag($tabsWrapper['tag'], array('class'=>$tabsWrapper['class'])).self::NL;
		if(!empty($tabsWrapperInner)){
			$twiTag = isset($tabsWrapperInner['tag']) ? $tabsWrapperInner['tag'] : 'div';
			$twiClass = isset($tabsWrapperInner['class']) ? $tabsWrapperInner['class'] : '';
			$twiId = isset($tabsWrapperInner['id']) ? $tabsWrapperInner['id'] : '';			
			$output .= CHtml::openTag($twiTag, array('class'=>$twiClass, 'id'=>$twiId)).self::NL;	
		}
		foreach($tabs as $tab => $tabInfo){
			if(empty($tabInfo)) continue;
			if(isset($tabInfo['disabled']) && (bool)$tabInfo['disabled'] === true) continue;
			
			$htmlOptions = (isset($tabInfo['htmlOptions']) && is_array($tabInfo['htmlOptions'])) ? $tabInfo['htmlOptions'] : array();
			if(isset($tabInfo['active']) && $tabInfo['active']){
				if(!isset($htmlOptions['class'])) $htmlOptions['class'] = 'active';
				else $htmlOptions['class'] .= ' active';
			}			
			if(isset($tabInfo['id'])) $htmlOptions['id'] = $tabInfo['id'].'_link';
			$href = isset($tabInfo['href']) ? $tabInfo['href'] : '';
			
			$output .= CHtml::link($tab, $href, $htmlOptions).self::NL;
		}
		if(!empty($tabsWrapperInner)) $output .= CHtml::closeTag($tabsWrapperInner['tag']).self::NL;       
		$output .= $contentAdditional; 
        $output .= CHtml::closeTag($tabsWrapper['tag']).self::NL;
		
		$output .= $contentMessage;
		
		// show content only if contentWrapper defined as non-empty
		if(!empty($contentWrapper)){
			$output .= CHtml::openTag($contentWrapper['tag'], array('class'=>$contentWrapper['class'])).self::NL;
			foreach($tabs as $tab => $tabInfo){
				$id = isset($tabInfo['id']) ? $tabInfo['id'] : '';
				$content = isset($tabInfo['content']) ? $tabInfo['content'] : '';
				$output .= CHtml::tag('div', array('id'=>$id, 'style'=>'display:block;'), $content).self::NL;
			}
			$output .= CHtml::closeTag($contentWrapper['tag']).self::NL;       	
		}
        
        if($return) return $output;
        else echo $output;       		
	}
    
}