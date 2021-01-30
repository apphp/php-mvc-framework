<?php
/**
 * CTabs widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
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
     *  echo CWidget::create('CTabs', [
     *   'tabsWrapper'=>['tag'=>'div', 'class'=>'title', 'id'=>''],
     *   'tabsWrapperInner'=>['tag'=>'div', 'class'=>'tabs', 'id'=>''], -- optional -
	 *   'tabsWrapperInnerItem'=>['tag'=>'a', 'class'=>'', 'id'=>''], -- optional -
     *   'contentWrapper'=>['tag'=>'div', 'class'=>'content', 'id'=>''],
     *	 'contentWrapperItem'=>['tag'=>'div', 'class'=>'', 'id'=>''],
     *   'contentMessage'=>$actionMessage,
     *   'contentAdditional'=>'',
     *   'firstTabActive'=>true, 
     *   'tabs'=>[
     *  	'General Settings' 	 =>['href'=>'#tab1', 'id'=>'tab1', 'content'=>$tab1, 'active'=>true, 'htmlOptions'=>[]],
     *  	'Visual Settings'  	 =>['href'=>'#tab2', 'id'=>'tab2', 'content'=>'Content for tab 2', 'htmlOptions'=>[]],
     *  	'Local Settings'  	 =>['href'=>'#tab3', 'id'=>'tab3', 'content'=>'Content for tab 3', 'htmlOptions'=>[]],
     *  	'Email Settings'  	 =>['href'=>'#tab4', 'id'=>'tab4', 'content'=>'Content for tab 4', 'htmlOptions'=>[]],
     *  	'Templates & Styles' =>['href'=>'#tab5', 'id'=>'tab5', 'content'=>'Content for tab 5', 'htmlOptions'=>[]],
     *  	'Server Info'  		 =>['href'=>'#tab6', 'id'=>'tab6', 'content'=>'Content for tab 6', 'htmlOptions'=>[]],
     *  	'Site Info'  		 =>['href'=>'#tab7', 'id'=>'tab7', 'content'=>'Content for tab 7', 'htmlOptions'=>[]],
     *  	'Cron Jobs'  		 =>['href'=>'#tab8', 'id'=>'tab8', 'content'=>'Content for tab 8', 'htmlOptions'=>[]],
     *   ],
     *   'events'=>array(
     *  	//'click'=>['field'=>$errorField]
     *   ),
     *   'return'=>true,
     *  ]);
    */
    public static function init($params = [])
    {
        parent::init($params);

        // Get param variables
        $tabs                 = self::params('tabs', []);
        $tabsWrapper          = self::params('tabsWrapper', []);
        $tabsWrapperInner     = self::params('tabsWrapperInner', []);
        $tabsWrapperInnerItem = self::params('tabsWrapperInnerItem', []);
        $contentWrapper       = self::params('contentWrapper', []);
        $contentWrapperItem   = self::params('contentWrapperItem', []);
        $contentMessage       = self::params('contentMessage', '');
        $contentAdditional    = self::params('contentAdditional', '');
        $firstTabActive       = self::params('firstTabActive', false);
        $return               = self::params('return', true);

        $output  = '';
        $tagName = 'div';

        $output .= CHtml::openTag($tabsWrapper['tag'], ['class' => $tabsWrapper['class']]).self::NL;
        if ( ! empty($tabsWrapperInner)) {
            $twiTag   = isset($tabsWrapperInner['tag']) ? $tabsWrapperInner['tag'] : 'div';
            $twiClass = isset($tabsWrapperInner['class']) ? $tabsWrapperInner['class'] : '';
            $twiId    = isset($tabsWrapperInner['id']) ? $tabsWrapperInner['id'] : '';
            $output   .= CHtml::openTag($twiTag, ['class' => $twiClass, 'id' => $twiId]).self::NL;
        }

        // Set the first tab to be "active" if nothing defined
        $activeTabExists = false;
        foreach ($tabs as $tab => $tabInfo) {
            if (isset($tabInfo['active']) && $tabInfo['active']) {
                $activeTabExists = true;
                break;
            }
        }
        if ( ! $activeTabExists && $firstTabActive) {
            $firstKey                  = key($tabs);
            $tabs[$firstKey]['active'] = true;
        }

        foreach ($tabs as $tab => $tabInfo) {
            if (empty($tabInfo)) {
                continue;
            }
            if (isset($tabInfo['disabled']) && (bool)$tabInfo['disabled'] === true) {
                continue;
            }

            $htmlOptions = (isset($tabInfo['htmlOptions']) && is_array($tabInfo['htmlOptions']))
                ? $tabInfo['htmlOptions'] : [];
            if (isset($tabInfo['active']) && $tabInfo['active']) {
                if ( ! isset($htmlOptions['class'])) {
                    $htmlOptions['class'] = 'active';
                } else {
                    $htmlOptions['class'] .= ' active';
                }
            }
            if (isset($tabInfo['id'])) {
                $htmlOptions['id'] = $tabInfo['id'].'_link';
            }
            $href = isset($tabInfo['href']) ? $tabInfo['href'] : '';

            if ( ! empty($tabsWrapperInnerItem)) {
                $htmlOptions['role']          = 'tab';
                $htmlOptions['data-toggle']   = 'tab';
                $htmlOptions['aria-controls'] = trim($href, '#');
                $cssClass                     = '';
                if (isset($htmlOptions['class'])) {
                    $cssClass = $htmlOptions['class'];
                    unset($htmlOptions['class']);
                }

                $twiiTag = isset($tabsWrapperInnerItem['tag']) ? $tabsWrapperInnerItem['tag'] : 'a';
                $output  .= CHtml::openTag($twiiTag, ['class' => $cssClass, 'id' => false]);
                $output  .= CHtml::link($tab, $href, $htmlOptions);
                $output  .= CHtml::closeTag($twiiTag).self::NL;
            } else {
                $output .= CHtml::link($tab, $href, $htmlOptions).self::NL;
            }
        }
        if ( ! empty($tabsWrapperInner)) {
            $output .= CHtml::closeTag($tabsWrapperInner['tag']).self::NL;
        }
        $output .= $contentAdditional;
        $output .= CHtml::closeTag($tabsWrapper['tag']).self::NL;

        $output .= $contentMessage;

        // Show content only if contentWrapper defined as non-empty
        if ( ! empty($contentWrapper)) {
            $output .= CHtml::openTag($contentWrapper['tag'], ['class' => $contentWrapper['class']]).self::NL;
            foreach ($tabs as $tab => $tabInfo) {
                $id      = isset($tabInfo['id']) ? $tabInfo['id'] : '';
                $content = isset($tabInfo['content']) ? $tabInfo['content'] : '';
                $style   = isset($contentWrapperItem['style']) ? $contentWrapperItem['style'] : 'display:block;';
                $class   = isset($contentWrapperItem['class']) ? $contentWrapperItem['class'] : '';

                if (isset($tabInfo['active']) && $tabInfo['active']) {
                    if (empty($class)) {
                        $class = 'active in';
                    } else {
                        $class .= ' active in';
                    }
                }

                $output .= CHtml::tag('div', ['id' => $id, 'class' => $class, 'style' => $style], $content).self::NL;
            }
            $output .= CHtml::closeTag($contentWrapper['tag']).self::NL;
        }

        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

}
