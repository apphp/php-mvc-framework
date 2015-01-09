<?php
/**
 * CFormView widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init                                                 _formField
 *                                                      _drawButtons
 * 
 */	  

class CFormView
{
    const NL = "\n";
    /** @var string */
    private static $_count = 0;

    /**
     * Draws HTML form
     * @param array $params
     * 
     * Notes:
     *   - to prevent double quotes issue use: 'encode'=>true in htmlOptions
     *   - insert code (for all fields): 'prependCode'=>'', 'appendCode'=>''
     *   - to use <button> tag for buttons use 'buttonTag'=>'button'
     *   - to show buttons at the top use 'buttonsPosition'=>'top' (bottom, top or both)
     *   - to disable any field or button use: 'disabled'=>true
     *   - 'viewType' optional values: '' or 'custom'
     *   
     * Usage: (in view)
     *  echo CWidget::create('CFormView', array(
     *       'action'=>'locations/update',
     *       'cancelUrl'=>'locations/view',
     *       'method'=>'post',
     *       'htmlOptions'=>array(
     *           'name'=>'form-contact',
     *           'enctype'=>'multipart/form-data',
     *           'autoGenerateId'=>false
     *       ),
     *       'requiredFieldsAlert'=>true,
     *       'fieldSetType'=>'frameset|tabs',
     *       'fields'=>array(
	 *         	 'separatorName' =>array(
	 *               'separatorInfo' => array('legend'=>A::t('app', 'Headers & Footers')),
	 *               'field_1'=>array('type'=>'textbox', 'title'=>'Field 1', 'tooltip'=>'', 'value'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'50')),
	 *               ...
	 *           ),
     *           'field_1'=>array('type'=>'hidden', 'value'=>'', 'htmlOptions'=>array()),
     *           'field_2'=>array('type'=>'textbox',  'title'=>'Field 2', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'50')),
     *           'field_3'=>array('type'=>'password', 'title'=>'Field 3', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'20')),
     *           'field_3_confirm'=>array('type'=>'password', 'title'=>'Confirm Field 3', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'20')),
     *           'field_4'=>array('type'=>'textarea', 'title'=>'Field 4', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'250')),
     *           'field_5'=>array('type'=>'file',     'title'=>'Field 5', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array()),
     *           'field_6'=>array('type'=>'image',    'title'=>'Field 6', 'tooltip'=>'', 'mandatoryStar'=>true, 'src'=>'', 'alt'=>'Field 6', 'htmlOptions'=>array()),
     *           'field_7'=>array('type'=>'html',     'title'=>'Field 7', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>''),
     *           'field_8'=>array('type'=>'label',    'title'=>'Field 8', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array()),
     *           'field_9'=>array('type'=>'link',     'title'=>'Field 9', 'tooltip'=>'', 'mandatoryStar'=>true, 'linkUrl'=>'path/to/param', 'linkText'=>'', 'htmlOptions'=>array()),
     *          'field_10'=>array('type'=>'datetime', 'title'=>'Field 10', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'definedValues'=>array(), 'format'=>'', 'minDate'=>'', 'maxDate'=>'', 'htmlOptions'=>array()),
     *          'field_11'=>array('type'=>'checkbox', 'title'=>'Field 11', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'checked'=>true, 'htmlOptions'=>array(), 'viewType'=>''),
     *          'field_12'=>array('type'=>'select',   'title'=>'Field 12', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>array(), 'htmlOptions'=>array()),
     *          'field_13'=>array('type'=>'radioButton', 'title'=>'Field 13', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'checked'=>'true', 'htmlOptions'=>array()),
     *          'field_14'=>array('type'=>'radioButtonList', 'title'=>'Field 14', 'tooltip'=>'', 'mandatoryStar'=>true, 'checked'=>0, 'data'=>array(), 'htmlOptions'=>array()),
	 *          'field_15'=>array('type'=>'imageUpload', 'title'=>'Field 15', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 
	 *          	'imageOptions' =>array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'imagePath'=>'templates/backend/images/accounts/', 'imageClass'=>'avatar'),
	 *          	'deleteOptions'=>array('showLink'=>true, 'linkUrl'=>'admins/edit/avatar/delete', 'linkText'=>'Delete'),
	 *          	'fileOptions'  =>array('showAlways'=>false, 'class'=>'file', 'size'=>'25')
	 *          ),
     *       ),
     *       'checkboxes'=>array(
     *           'remember'=>array('type'=>'checkbox', 'title'=>'Remember me', 'tooltip'=>'', 'value'=>'1', 'checked'=>false),
     *       ),
     *       'buttons'=>array(
     *          'submit'=>array('type'=>'submit', 'value'=>'Send', 'htmlOptions'=>array('name'=>'')),
     *          'submitUpdate'=>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
     *          'submitUpdateClose'=>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
	 *          'reset' =>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array()),
     *          'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
	 *          'custom' =>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"$(location).attr('href','categories/index');")),
     *       ),
     *       'buttonsPosition'=>'bottom',
     *       'events'=>array(
     *           'focus'=>array('field'=>$errorField)
     *       ),
     *       'return'=>true,
     *  ));
     */
    public static function init($params = array())
    {
        $output = '';
        $action = isset($params['action']) ? $params['action'] : '';
        $method = isset($params['method']) ? $params['method'] : 'post';
        $htmlOptions = (isset($params['htmlOptions']) && is_array($params['htmlOptions'])) ? $params['htmlOptions'] : array();
		$autoGenerateId = isset($htmlOptions['autoGenerateId']) ? (bool)$htmlOptions['autoGenerateId'] : false;
        $formName = isset($htmlOptions['name']) ? $htmlOptions['name'] : '';
		$requiredFieldsAlert = isset($params['requiredFieldsAlert']) ? $params['requiredFieldsAlert'] : false;
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $checkboxes = isset($params['checkboxes']) ? $params['checkboxes'] : array();
        $buttonsPosition = isset($params['buttonsPosition']) ? $params['buttonsPosition'] : 'bottom';
        $buttons = isset($params['buttons']) ? $params['buttons'] : array();
        $events = isset($params['events']) ? $params['events'] : array();
        $return = isset($params['return']) ? $params['return'] : true;

		$fieldSetType = (isset($params['fieldSetType']) && $params['fieldSetType'] == 'tabs') ? 'tabs' : 'frameset';                
		$tabs = array();
		$tabsCount = 0;
        
		if(isset($htmlOptions['autoGenerateId'])) unset($htmlOptions['autoGenerateId']);
        $output .= CHtml::openForm($action, $method, $htmlOptions).self::NL;

        // draw required fields alert
		if($requiredFieldsAlert){
			$output .= CHtml::tag('span', array('class'=>'required-fields-alert'), A::t('core','Items marked with an asterisk (*) are required'), true).self::NL;
		}
		
        // draw top buttons
        if($buttonsPosition == 'top' || $buttonsPosition == 'both') $output .= self::_drawButtons($buttons, 'top');

		// run in loop to remove disabled fields
		foreach($fields as $field => $fieldInfo){
            if(preg_match('/separator/i', $field) && is_array($fieldInfo)){
                foreach($fieldInfo as $iField => $iFieldInfo){						
                    if(isset($iFieldInfo['type']) && $iFieldInfo['type'] === 'data') unset($fields[$field][$iField]);
                    else if(isset($iFieldInfo['disabled']) && (bool)$iFieldInfo['disabled'] === true) unset($fields[$field][$iField]);
                }                
            }else{
                if(isset($fieldInfo['type']) && $fieldInfo['type'] === 'data') unset($fields[$field]);
                else if(isset($fieldInfo['disabled']) && (bool)$fieldInfo['disabled'] === true) unset($fields[$field]);
            }
		}

		// run in loop to draw fields
        foreach($fields as $field => $fieldInfo){
            if(preg_match('/separator/i', $field) && is_array($fieldInfo)){                
                $legend = isset($fieldInfo['separatorInfo']['legend']) ? $fieldInfo['separatorInfo']['legend'] : '';                
				unset($fieldInfo['separatorInfo']);				
                if($fieldSetType == 'tabs'){
					$content = '';
					foreach($fieldInfo as $iField => $iFieldInfo){						
					    $content .= self::_formField($iField, $iFieldInfo, $events, $formName, $autoGenerateId);
					}
					$tabsCount++;
					$tabs[$legend] = array('href'=>'#tab'.$field.$tabsCount, 'id'=>'tab'.$field.$tabsCount, 'content'=>$content);					
				}else{
					$output .= CHtml::openTag('fieldset').self::NL;
					$output .= CHtml::tag('legend', array(), $legend, true).self::NL;					
					foreach($fieldInfo as $iField => $iFieldInfo){
					    $output .= self::_formField($iField, $iFieldInfo, $events, $formName, $autoGenerateId);
					}                
					$output .= CHtml::closeTag('fieldset').self::NL;					
				}					
            }else{				
                $output .= self::_formField($field, $fieldInfo, $events, $formName, $autoGenerateId);
            }            
        }
		if($fieldSetType == 'tabs'){
			// collapsible 
			$output .= CWidget::create('CTabs', array(
				'tabsWrapper'=>array('tag'=>'div', 'class'=>'title formview-tabs'),
				'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs static', 'id'=>''),
				'contentWrapper'=>array('tag'=>'div', 'class'=>'content formview-content'),
				'contentMessage'=>'',
				'tabs'=>$tabs,
				'events'=>array(),
				'return'=>true,
			));
		}	
        
        // draw bottom buttons
        if($buttonsPosition == 'bottom' || $buttonsPosition == 'both') $output .= self::_drawButtons($buttons, 'bottom');
        
        // draw checkboxes
        if(count($checkboxes) > 0){
            $output .= CHtml::openTag('div', array('class'=>'checkboxes-wrapper'));
            foreach($checkboxes as $checkbox => $checkboxInfo){
                $title = isset($checkboxInfo['title']) ? $checkboxInfo['title'] : false;
                $checked = isset($checkboxInfo['checked']) ? $checkboxInfo['checked'] : false;
                $htmlOptions = (isset($checkboxInfo['htmlOptions']) && is_array($checkboxInfo['htmlOptions'])) ? $checkboxInfo['htmlOptions'] : array();
                $output .= CHtml::checkBox($checkbox, $checked, $htmlOptions).self::NL;
                if($title){                    
                    $output .= CHtml::label($title, $checkbox);
                }
            }
            $output .= CHtml::closeTag('div').self::NL;
        }
        
        $output .= CHtml::closeForm().self::NL;
        
        // attach events
        foreach($events as $event => $eventInfo){
            $field = isset($eventInfo['field']) ? $eventInfo['field'] : '';
            if($event == 'focus'){
                if(!empty($field)){
                    A::app()->getClientScript()->registerScript($formName, 'document.forms["'.$formName.'"].'.$field.'.focus();', 2);
                }
            }
        }
        
        if($return) return $output;
        else echo $output;       
    }

    /**
     * Draws HTML form field
     * @param string $field
     * @param array $fieldInfo
     * @param array $events
     * @param string $formName
     * @param bol $autoGenerateId
     * @see init()
     */    
    private static function _formField($field, $fieldInfo, $events, $formName = '', $autoGenerateId = false)
    {
        $output = '';
        
        $type = isset($fieldInfo['type']) ? strtolower($fieldInfo['type']) : 'textbox';
        $value = isset($fieldInfo['value']) ? $fieldInfo['value'] : '';		
        $title = isset($fieldInfo['title']) ? $fieldInfo['title'] : false;
		$tooltip = isset($fieldInfo['tooltip']) ? $fieldInfo['tooltip'] : '';
		$definedValues = isset($fieldInfo['definedValues']) ? $fieldInfo['definedValues'] : '';
        $mandatoryStar = isset($fieldInfo['mandatoryStar']) ? $fieldInfo['mandatoryStar'] : false;
        $htmlOptions = (isset($fieldInfo['htmlOptions']) && is_array($fieldInfo['htmlOptions'])) ? $fieldInfo['htmlOptions'] : array();
		$prependCode = isset($fieldInfo['prependCode']) ? $fieldInfo['prependCode'] : '';
		$appendCode = isset($fieldInfo['appendCode']) ? $fieldInfo['appendCode'] : '';
		$appendLabel = '';
		
		// encode special characters into HTML entities
		if($type != 'textarea'){
			$value = CHtml::encode($value);
		}
        
		// force removing of ID if not specified
        if(!isset($htmlOptions['id'])) $htmlOptions['id'] = false;
		if($autoGenerateId && !$htmlOptions['id']) $htmlOptions['id'] = $formName.'_'.$field;
		
        // highlight error field
        if(isset($events['focus']['field']) && $events['focus']['field'] == $field){
            if(isset($htmlOptions['class'])) $htmlOptions['class'] .= ' field-error';
            else $htmlOptions['class'] = 'field-error';                     
        }
        
        switch($type){
            case 'checkbox':
                $viewType = isset($fieldInfo['viewType']) ? $fieldInfo['viewType'] : '';
				$checked = isset($fieldInfo['checked']) ? (bool)$fieldInfo['checked'] : false;
				if(!empty($value)) $htmlOptions['value'] = $value;
                if($viewType == 'custom'){
                    $fieldHtml  = CHtml::openTag('div', array('class'=>'slideBox'));
                    $fieldHtml .= CHtml::checkBox($field, $checked, $htmlOptions);
                    $fieldHtml .= CHtml::label('', $htmlOptions['id']);
                    $fieldHtml .= CHtml::closeTag('div');				
                }else{
        			$fieldHtml = CHtml::checkBox($field, $checked, $htmlOptions);                    
                }
				break;
            case 'html':
                $fieldHtml = html_entity_decode($value);
                break;
            case 'label':				
				$format = isset($fieldInfo['format']) ? $fieldInfo['format'] : '';
                $stripTags = isset($fieldInfo['stripTags']) ? (bool)$fieldInfo['stripTags'] : false;
                if($stripTags) $value = strip_tags(CHtml::decode($value));
                
				if(is_array($definedValues) && isset($definedValues[$value])){
                    $value = $definedValues[$value];
                }else if($format != '' && $format != 'american' && $format != 'european'){
                    $value = date($format, strtotime($value));
                }

                $for = isset($htmlOptions['for']) ? (bool)$htmlOptions['for'] : false;
                $fieldHtml = CHtml::label($value, $for, $htmlOptions);
                break;
            case 'link':
				$linkUrl = isset($fieldInfo['linkUrl']) ? $fieldInfo['linkUrl'] : '#';
				$linkText = isset($fieldInfo['linkText']) ? $fieldInfo['linkText'] : '';
				$fieldHtml = CHtml::link($linkText, $linkUrl, $htmlOptions);	
                break;
            case 'datetime':
				$fieldId = isset($htmlOptions['id']) ? $htmlOptions['id'] : $formName.'_'.$field;
				$format = isset($fieldInfo['format']) ? $fieldInfo['format'] : 'yy-mm-dd';
                $minDate = isset($fieldInfo['minDate']) ? (int)$fieldInfo['minDate'] : '';
                $maxDate = isset($fieldInfo['maxDate']) ? (int)$fieldInfo['maxDate'] : '';
				if(is_array($definedValues) && isset($definedValues[$value])){
					$value = $definedValues[$value];				
				}
                if(!isset($htmlOptions['autocomplete'])) $htmlOptions['autocomplete'] = 'off';
				$fieldHtml = CHtml::textField($field, $value, $htmlOptions);
				
				A::app()->getClientScript()->registerCssFile('js/vendors/jquery/jquery-ui.min.css');
				/* formats: dd/mm/yy | d M, y | mm/dd/yy  | yy-mm-dd  | */
				A::app()->getClientScript()->registerScript(
					'datepicker',
					'$("#'.$fieldId.'").datepicker({
						showOn: "button",
						buttonImage: "js/vendors/jquery/images/calendar.png",
						buttonImageOnly: true,
						showWeek: false,
						firstDay: 1,					  
                        '.($minDate ? 'minDate: '.$minDate.',' : '').'
                        '.($maxDate ? 'maxDate: '.$maxDate.',' : '').'
						dateFormat: "'.$format.'",
						changeMonth: true,
						changeYear: true,
						appendText : "'.A::t('core', 'Format').': yyyy-mm-dd"
					});'
				);
                break;
            case 'hidden':
                $fieldHtml = CHtml::hiddenField($field, $value, $htmlOptions);
                break;
            case 'password':
                $fieldHtml = CHtml::passwordField($field, $value, $htmlOptions);
                break;
            case 'select':
            case 'dropdown':
            case 'dropdownlist':
                $data = isset($fieldInfo['data']) ? $fieldInfo['data'] : array();
                $fieldHtml = CHtml::dropDownList($field, $value, $data, $htmlOptions);
                break;
            case 'file':
				if(APPHP_MODE == 'demo') $htmlOptions['disabled'] = 'disabled';
                $fieldHtml = CHtml::fileField($field, $value, $htmlOptions);
                break;
            case 'image':
                $src = isset($fieldInfo['src']) ? $fieldInfo['src'] : '';
                $alt = isset($fieldInfo['alt']) ? $fieldInfo['alt'] : '';
                if(!isset($htmlOptions['name'])) $htmlOptions['name'] = $field;
                $fieldHtml = CHtml::image($src, $alt, $htmlOptions);
                break;
			case 'imageupload':
				// image options
				$showImage = isset($fieldInfo['imageOptions']['showImage']) ? (bool)$fieldInfo['imageOptions']['showImage'] : false;
				$imagePath = isset($fieldInfo['imageOptions']['imagePath']) ? $fieldInfo['imageOptions']['imagePath'] : '';
				$showImageName = isset($fieldInfo['imageOptions']['showImageName']) ? (bool)$fieldInfo['imageOptions']['showImageName'] : false;
				$showImageSize = isset($fieldInfo['imageOptions']['showImageSize']) ? (bool)$fieldInfo['imageOptions']['showImageSize'] : false;
				$imageClass = isset($fieldInfo['imageOptions']['imageClass']) ? $fieldInfo['imageOptions']['imageClass'] : '';
				$imageHtmlOptions = array();
				if(!empty($imageClass)) $imageHtmlOptions['class'] = $imageClass;
				// delete link options
				$showDeleteLink = isset($fieldInfo['deleteOptions']['showLink']) ? (bool)$fieldInfo['deleteOptions']['showLink'] : false;
				$deleteLinkPath = isset($fieldInfo['deleteOptions']['linkUrl']) ? $fieldInfo['deleteOptions']['linkUrl'] : '';
				$deleteLinkText = isset($fieldInfo['deleteOptions']['linkText']) ? $fieldInfo['deleteOptions']['linkText'] : A::t('core', 'Delete');
				$imageText = '';
				// file options
				$fileHtmlOptions = isset($fieldInfo['fileOptions']) ? $fieldInfo['fileOptions'] : '';
				$showAlways = isset($fieldInfo['fileOptions']['showAlways']) ? (bool)$fieldInfo['fileOptions']['showAlways'] : false;
				if($showAlways) unset($fileHtmlOptions['showAlways']);
								
				$fieldHtml = CHtml::openTag('div', array('style'=>'display:inline-block;'));
				// image
				if($showImage && !empty($value)) $fieldHtml .= CHtml::image($imagePath.$value, '', $imageHtmlOptions).'<br>';
				// image text 
				if($showImageName && !empty($value)) $imageText .= $value.' ';
				if($showImageSize && !empty($value)){
					$imageText .= ' ('.CFile::getFileSize($imagePath.$value, 'kb').' Kb) ';
				}
				// delete link
				if($showDeleteLink && !empty($value) && APPHP_MODE !== 'demo'){
					$imageText .= ' &nbsp;'.CHtml::link($deleteLinkText, (!empty($deleteLinkPath) ? $deleteLinkPath : '#'));	
				} 
				// middle text
				if($imageText) $fieldHtml .= CHtml::label($imageText, '', array('style'=>'width:100%;'));				
				// file field
				if(APPHP_MODE == 'demo') $fileHtmlOptions['disabled'] = 'disabled';
				if($showAlways || empty($value)) $fieldHtml .= CHtml::fileField($field, $value, $fileHtmlOptions);				
				$fieldHtml .= CHtml::closeTag('div');				
				break;
            case 'textarea':
				$maxLength = isset($htmlOptions['maxLength']) ? (int)$htmlOptions['maxLength'] : 0;
				if($maxLength > 0) $appendLabel = '<br>'.A::t('core', 'max.: {maxchars} chars', array('{maxchars}'=>$maxLength));
                $fieldHtml = CHtml::textArea($field, $value, $htmlOptions);
                break;
            case 'radio':
			case 'radiobutton':
				$checked = isset($fieldInfo['checked']) ? (bool)$fieldInfo['checked'] : false;
				if(!empty($value)) $htmlOptions['value'] = $value;
				$fieldHtml = CHtml::radioButton($field, $checked, $htmlOptions);
				break;
			case 'radiobuttons':
			case 'radiobuttonlist':
				$data = isset($fieldInfo['data']) ? $fieldInfo['data'] : array();
				$checked = isset($fieldInfo['checked']) ? $fieldInfo['checked'] : false;
				$htmlOptions['separator'] = "\n";
				$fieldHtml = CHtml::radioButtonList($field, $checked, $data, $htmlOptions);
				break;
            case 'textbox':
            default:
                $fieldHtml = CHtml::textField($field, $value, $htmlOptions);
                break;
        }
        if($type == 'hidden'){
            $output .= $fieldHtml.self::NL;    
        }else{
            $output .= CHtml::openTag('div', array('class'=>'row', 'id'=>($autoGenerateId) ? $formName.'_row_'.self::$_count++ : ''));
			$output .= $prependCode;
            if($title){
				$for = (isset($htmlOptions['id']) && $htmlOptions['id']) ? $htmlOptions['id'] : false;
				$tooltipText = (!empty($tooltip)) ? ' '.CHtml::link('', false, array('class'=>'tooltip-icon', 'title'=>$tooltip)) : '';
				$output .= CHtml::label($title.$tooltipText.(trim($title) !== '' ? ':' : '').(($mandatoryStar) ? CHtml::$afterRequiredLabel : '').$appendLabel, $for);
				
            }
            $output .= $fieldHtml;
			$output .= $appendCode;
            $output .= CHtml::closeTag('div').self::NL;                
        }
        return $output;
    }
    
 
    /**
     * Draws HTML form buttons
     * @param array $buttons
     * @param string $placement
     */    
    private static function _drawButtons($buttons, $placement = 'bottom')
    {
        $output = '';

 		// remove disabled buttons
		foreach($buttons as $key => $val){
			if(isset($val['disabled']) && (bool)$val['disabled'] === true) unset($buttons[$key]);
		}

       // draw buttons
        if(count($buttons) > 0){
            $additionalClass = ($placement == 'top') ? ' bw-top' : ' bw-bottom';
            $output .= CHtml::openTag('div', array('class'=>'buttons-wrapper'.$additionalClass)).self::NL;
            foreach($buttons as $button => $buttonInfo){
                $type = isset($buttonInfo['type']) ? $buttonInfo['type'] : '';
                $value = isset($buttonInfo['value']) ? $buttonInfo['value'] : '';
				$htmlOptions = (isset($buttonInfo['htmlOptions']) && is_array($buttonInfo['htmlOptions'])) ? $buttonInfo['htmlOptions'] : array();
                if(!isset($htmlOptions['value'])) $htmlOptions['value'] = $value;
                switch($type){
                    case 'button':
                        $htmlOptions['type'] = 'button';
                        $output .= CHtml::button('button', $htmlOptions).self::NL;
                        break;
                    case 'reset':
                        $output .= CHtml::resetButton('reset', $htmlOptions).self::NL;
                        break;
                    case 'submitUpdate':
                        $output .= CHtml::submitButton('submit', $htmlOptions).self::NL;
                        break;
                    case 'submitUpdateClose':
                        $output .= CHtml::submitButton('submit', $htmlOptions).self::NL;
                        break;
                    case 'submit':
                    default:
                        $output .= CHtml::submitButton('submit', $htmlOptions).self::NL;
                        break;
                }                        
            }            
            $output .= CHtml::closeTag('div').self::NL;
        }
        
        return $output;        
    }
}