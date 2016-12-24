<?php
/**
 * CTabs widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init
 * 
 */	  

class CTabs extends CWidgs
{
	
	/**
	 * @const string new line
	 */
    const NL = "\n";    

	
    /**
     * Draws tabs
     * @param array $params
     * 
     * Notes:
     *   - to disable any field or button use: 'disabled'=>true
     *   
     * Usage:
     *
     *  // --- tabsWrapper
     *  <div class="title formview-tabs"> 
	 *      // --- tabsWrapperInner
     *  	<div class="tabs static"> 
	 *      	// --- tabsWrapperInnerItem 
     *  		<a class="active" id="tabseparatorPersonalInfo1_link" href="#tabseparatorPersonalInfo1">Personal Information</a>
     *  		<a id="tabseparatorContactInformation2_link" href="#tabseparatorContactInformation2">Contact Information</a>
     *  		<a id="tabseparatorAddressInformation3_link" href="#tabseparatorAddressInformation3">Address Information</a>
     *  		<a id="tabseparatorAccountInformation4_link" href="#tabseparatorAccountInformation4">Account Information</a>
     *  		<a id="tabseparatorOther5_link" href="#tabseparatorOther5">Other</a>
     *  	</div>
     *  </div>
	 * 
     *  echo CWidget::create('CTabs', array(
     *   'tabsWrapper'=>array('tag'=>'div', 'class'=>'title', 'id'=>''),
     *   'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs', 'id'=>''), -- optional - 
	 *   'tabsWrapperInnerItem'=>array('tag'=>'a', 'class'=>'', 'id'=>''), -- optional - 
     *   'contentWrapper'=>array('tag'=>'div', 'class'=>'content', 'id'=>''),
     *	 'contentWrapperItem'=>array('tag'=>'div', 'class'=>'', 'id'=>''),
     *   'contentMessage'=>$actionMessage,
     *   'contentAdditional'=>'',
     *   'firstTabActive'=>true, 
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
		parent::init($params);

		// Get param variables
        $tabs 					= self::params('tabs', array());
		$tabsWrapper 			= self::params('tabsWrapper', array());
		$tabsWrapperInner 		= self::params('tabsWrapperInner', array());
		$tabsWrapperInnerItem 	= self::params('tabsWrapperInnerItem', array());
		$contentWrapper 		= self::params('contentWrapper', array());
		$contentWrapperItem 	= self::params('contentWrapperItem', array());
		$contentMessage 		= self::params('contentMessage', '');
		$contentAdditional 		= self::params('contentAdditional', '');
		$firstTabActive 		= self::params('firstTabActive', false);
		$return 				= self::params('return', true);

        $output = '';
        $tagName = 'div';
		
		$output .= CHtml::openTag($tabsWrapper['tag'], array('class'=>$tabsWrapper['class'])).self::NL;
		if(!empty($tabsWrapperInner)){
			$twiTag = isset($tabsWrapperInner['tag']) ? $tabsWrapperInner['tag'] : 'div';
			$twiClass = isset($tabsWrapperInner['class']) ? $tabsWrapperInner['class'] : '';
			$twiId = isset($tabsWrapperInner['id']) ? $tabsWrapperInner['id'] : '';			
			$output .= CHtml::openTag($twiTag, array('class'=>$twiClass, 'id'=>$twiId)).self::NL;	
		}
		
		// Set the first tab to be "active" if nothing defined
		$activeTabExists = false;
		foreach($tabs as $tab => $tabInfo){
			if(isset($tabInfo['active']) && $tabInfo['active']){
				$activeTabExists = true;
				break;
			}
		}
		if(!$activeTabExists && $firstTabActive){
			$firstKey = key($tabs);
			$tabs[$firstKey]['active'] = true;
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
			
			if(!empty($tabsWrapperInnerItem)){
				$htmlOptions['role'] = 'tab';
				$htmlOptions['data-toggle'] = 'tab';
				$htmlOptions['aria-controls'] = trim($href, '#');
				$cssClass = '';
				if(isset($htmlOptions['class'])){
					$cssClass = $htmlOptions['class'];
					unset($htmlOptions['class']);
				}
				
				$twiiTag = isset($tabsWrapperInnerItem['tag']) ? $tabsWrapperInnerItem['tag'] : 'a';
				$output .= CHtml::openTag($twiiTag, array('class'=>$cssClass, 'id'=>false));	
				$output .= CHtml::link($tab, $href, $htmlOptions);
				$output .= CHtml::closeTag($twiiTag).self::NL;
			}else{
				$output .= CHtml::link($tab, $href, $htmlOptions).self::NL;
			}
		}
		if(!empty($tabsWrapperInner)) $output .= CHtml::closeTag($tabsWrapperInner['tag']).self::NL;       
		$output .= $contentAdditional; 
        $output .= CHtml::closeTag($tabsWrapper['tag']).self::NL;
		
		$output .= $contentMessage;
		
		// Show content only if contentWrapper defined as non-empty
		if(!empty($contentWrapper)){
			$output .= CHtml::openTag($contentWrapper['tag'], array('class'=>$contentWrapper['class'])).self::NL;
			foreach($tabs as $tab => $tabInfo){
				$id = isset($tabInfo['id']) ? $tabInfo['id'] : '';
				$content = isset($tabInfo['content']) ? $tabInfo['content'] : '';
				$style = isset($contentWrapperItem['style']) ? $contentWrapperItem['style'] : 'display:block;';
				$class = isset($contentWrapperItem['class']) ? $contentWrapperItem['class'] : '';
				
				if(isset($tabInfo['active']) && $tabInfo['active']){
					if(empty($class)) $class = 'active in';
					else $class .= ' active in';
				}			

				$output .= CHtml::tag('div', array('id'=>$id, 'class'=>$class, 'style'=>$style), $content).self::NL;
			}
			$output .= CHtml::closeTag($contentWrapper['tag']).self::NL;       	
		}
        
        if($return) return $output;
        else echo $output;       		
	}
	
}
