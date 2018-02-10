<?php
/**
 * CFormView widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC(static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init			                                        _formField
 *                                                      _drawButtons 
 * 
 */	  

class CFormView extends CWidgs
{
	
	/** @const string */
    const NL = "\n";
    /** @var int */
    private static $_rowCount = 0;
    /** @var int */
    private static $_pickerCount = 0;
    /** @var int */
    private static $_autocompleteCount = 0;
    /** @var int */
    private static $_colorCount = 0;

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
     *   - select classes: 'class'=>'chosen-select-filter' or 'class'=>'chosen-select'
     *   - attribute 'autocomplete'=>array(..., 'varN'=>array('function'=>'jQuery("#id").val()')) passed as a parameter jQuery or javascript the function instead of use the variable
     *   
     * Usage: (in view)
     *  echo CWidget::create('CFormView', array(
     *       'action'		=> 'locations/update',
     *       'cancelUrl'	=> 'locations/view',
     *       'method'		=> 'post',
     *       'htmlOptions'	=> array(
     *           'name'			  => 'form-contact',
     *           'enctype'		  => 'multipart/form-data', // multipart/form-data, application/x-www-form-urlencoded, text/plain or ''
     *           'autoGenerateId' => false
     *       ),
     *       'requiredFieldsAlert'=>true,
     *       'fieldSets'	=> array('type'=>'frameset|tabs|tabsList', 'firstTabActive'=>true),
     *       'fieldWrapper' => array('tag'=>'div', 'class'=>'row'),
     *       'fields'		=> array(
	 *         	 'separatorName' =>array(
	 *               'separatorInfo' => array('legend'=>A::t('app', 'Headers & Footers')),
	 *               'field_1'=>array('type'=>'textbox', 'title'=>'Field 1', 'tooltip'=>'', 'value'=>'', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'50')),
	 *               ...
	 *           ),
     *           'field_1'=>array('type'=>'hidden', 'value'=>'', 'htmlOptions'=>array()),
     *           'field_2'=>array('type'=>'textbox',  'title'=>'Field 2', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'50')),
     *           'field_3'=>array('type'=>'textbox',  'title'=>'Autocomplete', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'autocomplete'=>array('enable'=>true, 'ajaxHandler'=>'part/to/handler/file', 'minLength'=>3, 'default'=>'', 'returnId'=>true, 'params'=>array()), 'htmlOptions'=>array('maxLength'=>'50')),
     *           'field_4'=>array('type'=>'password', 'title'=>'Field 4', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'20')),
     *           'field_4_confirm'=>array('type'=>'password', 'title'=>'Confirm Field 4', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'20')),
     *           'field_5'=>array('type'=>'textarea', 'title'=>'Field 5', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'250')),
     *           'field_6'=>array('type'=>'file',     'title'=>'Field 6', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array()),
     *           'field_7'=>array('type'=>'image',    'title'=>'Field 7', 'tooltip'=>'', 'mandatoryStar'=>true, 'src'=>'', 'alt'=>'Field 6', 'htmlOptions'=>array()),
     *           'field_8'=>array('type'=>'html',     'title'=>'Field 8', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'definedValues'=>array()),
     *           'field_9'=>array('type'=>'label',    'title'=>'Field 9', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'definedValues'=>array(), 'format'=>'', 'stripTags'=>false, 'htmlOptions'=>array()),
     *          'field_10'=>array('type'=>'link',     'title'=>'Field 10', 'tooltip'=>'', 'mandatoryStar'=>true, 'linkUrl'=>'path/to/param', 'linkText'=>'', 'videoPreview'=>false, 'htmlOptions'=>array()),
     *          'field_11'=>array('type'=>'videolink','title'=>'Field 11','tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'preview'=>false, 'htmlOptions'=>array('maxLength'=>'50')),
     *          'field_12'=>array('type'=>'datetime', 'title'=>'Field 12', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'definedValues'=>array(), 'format'=>'', 'minDate'=>'', 'maxDate'=>'', 'yearRange'=>'-100:+0', 'buttonTrigger'=>true, 'htmlOptions'=>array()),
     *          'field_13'=>array('type'=>'checkbox', 'title'=>'Field 13', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'checked'=>true, 'htmlOptions'=>array(), 'viewType'=>'|custom'),
     *          'field_14'=>array('type'=>'select',   'title'=>'Field 14', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'data'=>array(), 'emptyOption'=>false, 'emptyValue'=>'', 'viewType'=>'dropdownlist|checkboxes', 'multiple'=>false, 'storeType'=>'serialized|separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
     *          'field_15'=>array('type'=>'color',    'title'=>'Field 15', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'50')),
     *          'field_16'=>array('type'=>'email',    'title'=>'Field 16', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'htmlOptions'=>array('maxLength'=>'100')),
     *          'field_17'=>array('type'=>'radioButton', 'title'=>'Field 17', 'tooltip'=>'', 'mandatoryStar'=>true, 'value'=>'', 'checked'=>'true', 'htmlOptions'=>array()),
     *          'field_18'=>array('type'=>'radioButtonList', 'title'=>'Field 18', 'tooltip'=>'', 'mandatoryStar'=>true, 'checked'=>0, 'data'=>array(), 'htmlOptions'=>array()),
	 *          'field_19'=>array('type'=>'imageUpload', 'title'=>'Field 19', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 
	 *          	'imageOptions' =>array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'avatar'),
	 *          	'deleteOptions'=>array('showLink'=>true, 'linkUrl'=>'admins/edit/avatar/delete', 'linkText'=>'Delete'),
	 *          	'rotateOptions'=>array('showLinks'=>true, 'linkRotateLeft'=>'admins/edit/rotate/left', 'linkRotateRigth'=>'admin/edit/rotate/right', 'iconRotateLeft'=>'templates/backend/images/rotateLeft.png', 'iconRotateRight'=>'templates/backend/images/rotateRight.png'),
	 *          	'fileOptions'=>array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'templates/backend/files/accounts/')
	 *          ),
     *          'field_20'=>array('type'=>'fileUpload', 'title'=>'Field 20', 'tooltip'=>'', 'mandatoryStar'=>false, 'value'=>'', 'download'=>false,
	 *          	'iconOptions'=>array('showType'=>true, 'showFileName'=>true, 'showFileSize'=>true),
	 *          	'deleteOptions'=>array('showLink'=>true, 'linkUrl'=>'templates/backend/files/accounts/', 'linkText'=>'Delete'),
	 *          	'fileOptions'=>array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'templates/backend/files/accounts/')
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
	 *          'custom' =>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','categories/index');")),
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
		parent::init($params);		

        $output 	   			= '';		
		$action 		   		= self::params('action', '');
        $method 				= self::params('method', 'post');
        $htmlOptions 			= self::params('htmlOptions', array(), 'is_array');
		$autoGenerateId 		= self::params('htmlOptions.autoGenerateId', false);
        $formName 				= self::params('htmlOptions.name', '');
		$requiredFieldsAlert 	= self::params('requiredFieldsAlert', false);		
        $fields 				= self::params('fields', array());		
        $checkboxes 			= self::params('checkboxes', array());		
        $buttonsPosition 		= self::params('buttonsPosition', 'bottom');		
        $buttons 				= self::params('buttons', array());		
        $events 				= self::params('events', array());		
        $return 				= self::params('return', true);
		$fieldSetType			= self::params('fieldSets.type', 'frameset', 'in_array', array('tabs', 'tabsList', 'frameset'));
		$fieldSetFirstTabActive	= self::params('fieldSets.firstTabActive', true);
		$fieldWrapperTag		= self::params('fieldWrapper.tag', 'div');
		$fieldWrapperClass		= self::params('fieldWrapper.class', 'row');
		
		$tabs = array();
		$tabsCount = 0;
        
		// Run in loop:
		// 1. Remove disabled fields
		// 2. Add or remove 'enctype'=>'multipart/form-data' according to defined "file" fields in the form
		$fileFieldFound = false;
		foreach($fields as $field => $fieldInfo){
            if(preg_match('/separator/i', $field) && is_array($fieldInfo)){
                foreach($fieldInfo as $iField => $iFieldInfo){						
                    if(self::keyAt('type', $iFieldInfo) === 'data' || (bool)self::keyAt('disabled', $iFieldInfo) === true){
						unset($fields[$field][$iField]);
					}
					
					// Automatically add enctype according if "file" field found
					if(!$fileFieldFound && isset($iFieldInfo['type']) && in_array(strtolower($iFieldInfo['type']), array('file', 'fileupload', 'imageupload'))){
						$fileFieldFound = true;
						if(empty($htmlOptions['enctype'])){
							$htmlOptions['enctype'] = 'multipart/form-data';
						}
					}
                }                
            }else{
				if(self::keyAt('type', $fieldInfo) === 'data' || (bool)self::keyAt('disabled', $fieldInfo) === true){
					unset($fields[$field]);
				}
				
				// Automatically add enctype according if "file" field found
				if(!$fileFieldFound && isset($fieldInfo['type']) && in_array(strtolower($fieldInfo['type']), array('file', 'fileupload', 'imageupload'))){	
					$fileFieldFound = true;
					if(empty($htmlOptions['enctype'])){
						$htmlOptions['enctype'] = 'multipart/form-data';
					}
				}
            }			
		}
		
		// Automatically clean enctype if no "file" fields found
		if(!$fileFieldFound && isset($htmlOptions['enctype']) && $htmlOptions['enctype'] === 'multipart/form-data'){
			unset($htmlOptions['enctype']);
		}
		
		self::unsetKey('autoGenerateId', $htmlOptions);
		if(!self::issetKey('class', $htmlOptions)) $htmlOptions['class'] = 'widget-cformview';
		else $htmlOptions['class'] .= ' widget-cformview';
        $output .= CHtml::openForm($action, $method, $htmlOptions).self::NL;
		
        // Draw required fields alert
		if($requiredFieldsAlert){
			$output .= CHtml::tag('span', array('class'=>'required-fields-alert'), A::t('core','Items marked with an asterisk (*) are required'), true).self::NL;
		}
		
        // Draw top buttons
        if($buttonsPosition == 'top' || $buttonsPosition == 'both'){
			$output .= self::_drawButtons($buttons, 'top');
		}

		// Run in loop to draw fields
        foreach($fields as $field => $fieldInfo){
            if(preg_match('/separator/i', $field) && is_array($fieldInfo)){                
                $legend = self::keyAt('separatorInfo.legend', $fieldInfo, '');
				self::unsetKey('separatorInfo', $fieldInfo);

                if($fieldSetType == 'tabs' || $fieldSetType == 'tabsList'){
					$content = '';
					foreach($fieldInfo as $iField => $iFieldInfo){						
					    $content .= self::_formField($iField, $iFieldInfo, $events, $formName, $autoGenerateId, array('fieldWrapperTag'=>$fieldWrapperTag, 'fieldWrapperClass'=>$fieldWrapperClass));
					}
					$tabsCount++;
					$tabs[$legend] = array('href'=>'#tab'.$field.$tabsCount, 'id'=>'tab'.$field.$tabsCount, 'content'=>$content);					
				}else{
					$output .= CHtml::openTag('fieldset').self::NL;
					$output .= CHtml::tag('legend', array(), $legend, true).self::NL;					
					foreach($fieldInfo as $iField => $iFieldInfo){
					    $output .= self::_formField($iField, $iFieldInfo, $events, $formName, $autoGenerateId, array('fieldWrapperTag'=>$fieldWrapperTag, 'fieldWrapperClass'=>$fieldWrapperClass));
					}                
					$output .= CHtml::closeTag('fieldset').self::NL;					
				}					
            }else{				
                $output .= self::_formField($field, $fieldInfo, $events, $formName, $autoGenerateId, array('fieldWrapperTag'=>$fieldWrapperTag, 'fieldWrapperClass'=>$fieldWrapperClass));
            }            
        }
		if($fieldSetType == 'tabs'){
			// Collapsible 
			$output .= CWidget::create('CTabs', array(
				'tabsWrapper'=>array('tag'=>'div', 'class'=>'title formview-tabs'),
				'tabsWrapperInner'=>array('tag'=>'div', 'class'=>'tabs static', 'id'=>''),
				'contentWrapper'=>array('tag'=>'div', 'class'=>'content formview-content'),
				'contentMessage'=>'',
				'tabs'=>$tabs,
				'events'=>array(),
				'return'=>true,
			));
		}elseif($fieldSetType == 'tabsList'){
			// Collapsible 
			$output .= CWidget::create('CTabs', array(
				'tabsWrapper'=>array('tag'=>'div', 'class'=>''),
				'tabsWrapperInner'=>array('tag'=>'ul', 'class'=>'nav nav-tabs', 'id'=>''),
				'tabsWrapperInnerItem'=>array('tag'=>'li', 'class'=>'', 'id'=>''),
				'contentWrapper'=>array('tag'=>'div', 'class'=>'tab-content'),
				'contentWrapperItem'=>array('tag'=>'div', 'class'=>'tab-pane fade', 'id'=>'', 'style'=>''),
				'contentMessage'=>'',
				'firstTabActive'=>$fieldSetFirstTabActive,
				'tabs'=>$tabs,
				'events'=>array(),
				'return'=>true,
			));
		}	
        
        // Draw bottom buttons
        if($buttonsPosition == 'bottom' || $buttonsPosition == 'both') $output .= self::_drawButtons($buttons, 'bottom');
        
        // Draw checkboxes
        if(count($checkboxes) > 0){
            $output .= CHtml::openTag('div', array('class'=>'checkboxes-wrapper'));
            foreach($checkboxes as $checkbox => $checkboxInfo){
                $title = self::keyAt('title', $checkboxInfo, false);
                $checked = self::keyAt('checked', $checkboxInfo, false);
                $htmlOptions = (array)self::keyAt('htmlOptions', $checkboxInfo);
                $output .= CHtml::checkBox($checkbox, $checked, $htmlOptions).self::NL;
                if($title){                    
                    $output .= CHtml::label($title, $checkbox);
                }
            }
            $output .= CHtml::closeTag('div').self::NL;
        }
        
        $output .= CHtml::closeForm().self::NL;
        
        // Attach events
        foreach($events as $event => $eventInfo){
            $field = self::keyAt('field', $eventInfo, '');
            if($event == 'focus'){
                if(!empty($field)){
                    A::app()->getClientScript()->registerScript($formName, 'document.forms["'.$formName.'"].'.$field.'.focus();', 5);
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
     * @param array $params
     * @see init()
     */    
    private static function _formField($field, $fieldInfo, $events, $formName = '', $autoGenerateId = false, $params = array())
    {
        $output = '';
        
        $type 			= strtolower(self::keyAt('type', $fieldInfo, 'textbox')); 
        $value			= self::keyAt('value', $fieldInfo, '');
        $title 			= self::keyAt('title', $fieldInfo, false);
		$tooltip 		= self::keyAt('tooltip', $fieldInfo, '');
		$default 		= self::keyAt('default', $fieldInfo, '');
		$definedValues 	= self::keyAt('definedValues', $fieldInfo, '');
        $mandatoryStar 	= self::keyAt('mandatoryStar', $fieldInfo, false);
		$autocomplete 	= self::keyAt('autocomplete', $fieldInfo, array(), 'is_array');
        $htmlOptions 	= (array)self::keyAt('htmlOptions', $fieldInfo, array(), 'is_array');
		$prependCode 	= self::keyAt('prependCode', $fieldInfo, '');
		$appendCode 	= self::keyAt('appendCode', $fieldInfo, '');
		$appendLabel 	= '';

		$fieldWrapperTag	= isset($params['fieldWrapperTag']) ? $params['fieldWrapperTag'] : 'div';
		$fieldWrapperClass	= isset($params['fieldWrapperClass']) ? $params['fieldWrapperClass'] : 'row';
		
		// Encode special characters into HTML entities
		if(is_array($value)){
			$value = array_map(array('CHtml', 'encode'), $value);
		}elseif($type != 'textarea'){
			$value = CHtml::encode($value);
		}
        
		// Force removing of ID if not specified
        if(!self::issetKey('id', $htmlOptions)) $htmlOptions['id'] = false;
		if($autoGenerateId && !$htmlOptions['id']) $htmlOptions['id'] = $formName.'_'.$field;
		
        // Highlight error field
        if(self::issetKey('focus.field', $events) && self::keyAt('focus.field', $events) == $field){
            if(self::issetKey('class', $htmlOptions)) $htmlOptions['class'] .= ' field-error';
            else $htmlOptions['class'] = 'field-error';                     
        }
        
        switch($type){
            case 'checkbox':
                $viewType = self::keyAt('viewType', $fieldInfo, '');
				$checked = (bool)self::keyAt('checked', $fieldInfo, false);
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
			
            case 'videolink':
				$preview = self::keyAt('preview', $fieldInfo, false);                
				$fieldHtml = '';
				
				if($preview == true && !empty($value)){
					$fieldHtml = CHtml::openTag('div', array('style'=>'display:inline-block;'));
					$matches = array();
					if(preg_match('/vimeo\./', $value)){
						preg_match('/^(?:http(?:s)?:\/\/)?(?:www\.)?vimeo.com\/([0-9]+)/i', $value, $matches);
						$id = $matches[1];
						$fieldHtml .= '<iframe width="240" height="140" src="https://player.vimeo.com/video/'.$id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><br>';
					}elseif(preg_match('/youtube\./', $value)){
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $value, $matches);
						$id = $matches[1];
						$fieldHtml .= '<iframe width="240" height="140" src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe><br>';
					}else{
						$fieldHtml .= '<object width="240" height="140"><param name="movie" value="'.htmlspecialchars($value).'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'.htmlspecialchars($value).'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="240" height="140"></embed></object>';
					}
				}
				$fieldHtml .= CHtml::textField($field, $value, $htmlOptions);
				if($preview == true && !empty($value)) $fieldHtml .= CHtml::closeTag('div');
                break;
			
            case 'html':
				if(is_array($definedValues) && self::issetKey($value, $definedValues)){ /* don't use here self::keyAt */
                    $value = $definedValues[$value];
                }
                $fieldHtml = html_entity_decode($value);
                break;
			
            case 'label':				
				if($value === ''){
					$value = $default;
				}

				$format = self::keyAt('format', $fieldInfo, '');
                $stripTags = (bool)self::keyAt('stripTags', $fieldInfo, false);
                if($stripTags) $value = strip_tags(CHtml::decode($value));
                
				if(is_array($definedValues) && self::issetKey($value, $definedValues)){ /* don't use here self::keyAt */
                    $value = $definedValues[$value];
                }elseif($format != '' && $format != 'american' && $format != 'european'){
                    $value = date($format, strtotime($value));
                }

                $for = self::keyAt('for', $htmlOptions, false);
                $fieldHtml = CHtml::label($value, $for, $htmlOptions);
                break;
			
            case 'link':
				$linkUrl = self::keyAt('linkUrl', $fieldInfo, '#');
				$linkText = self::keyAt('linkText', $fieldInfo, '');
				$videoPreview = (bool)self::keyAt('videoPreview', $fieldInfo, false);
				$fieldHtml = CHtml::link($linkText, $linkUrl, $htmlOptions);	
                break;
			
            case 'datetime':
				$fieldId = self::keyAt('id',  $htmlOptions, $formName.'_'.$field);
				$format = self::keyAt('format', $fieldInfo, 'yy-mm-dd');
				if(empty($format)){
					$format = 'yy-mm-dd';
				}
				$buttonTrigger = self::keyAt('buttonTrigger', $fieldInfo, true);
                $minDate = (int)self::keyAt('minDate', $fieldInfo, ''); /* max days before current date */
                $maxDate = (int)self::keyAt('maxDate', $fieldInfo, ''); /* max days from current date */
				$yearRange = self::keyAt('yearRange', $fieldInfo, '');  /* ex.: "-100:+2" */
				if(is_array($definedValues) && self::issetKey($value, $definedValues)){ /* don't use here self::keyAt */
					$value = $definedValues[$value];				
				}
                if(!self::issetKey('autocomplete', $htmlOptions)) $htmlOptions['autocomplete'] = 'off';
				$fieldHtml = CHtml::textField($field, $value, $htmlOptions);
				
				A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
				// UI:
				//		dateFormat: dd/mm/yy | d M, y | mm/dd/yy  | yy-mm-dd 
				// Bootstrap:
				// 		dateFormat: dd/mm/yyyy | d M, y | mm/dd/yyyy  | yyyy-mm-dd
				//		autoclose: true,
				if($buttonTrigger){
					A::app()->getClientScript()->registerScript(
						'datepicker_'.self::$_pickerCount++,
						'jQuery("#'.$fieldId.'").datepicker({
							showOn: "button",
							buttonImage: "assets/vendors/jquery/images/calendar.png",
							buttonImageOnly: true,
							showWeek: false,
							firstDay: 1,
							'.($minDate ? 'minDate: '.$minDate.',' : '').'
							'.($maxDate ? 'maxDate: '.$maxDate.',' : '').'
							'.($yearRange ? 'yearRange: "'.$yearRange.'",' : '').'
							autoclose: true,
							format: "'.($format == 'yy-mm-dd' ? 'yyyy-mm-dd' : $format).'",
							dateFormat: "'.$format.'",
							changeMonth: true,
							changeYear: true,
							appendText : "'.A::t('core', 'Format').': yyyy-mm-dd"
						});'
					);					
				}else{
					A::app()->getClientScript()->registerScript(
						'datepicker_'.self::$_pickerCount++,
						'jQuery("#'.$fieldId.'").datepicker({
							showWeek: false,
							firstDay: 1,
							'.($minDate ? 'minDate: '.$minDate.',' : '').'
							'.($maxDate ? 'maxDate: '.$maxDate.',' : '').'
							autoclose: true,
							format: "'.($format == 'yy-mm-dd' ? 'yyyy-mm-dd' : $format).'",
							dateFormat: "'.$format.'",
							changeMonth: true,
							changeYear: true
						});'
					);
				}
                break;
			
			case 'hidden':
                $fieldHtml = CHtml::hiddenField($field, $value, $htmlOptions);
                break;
			
            case 'password':
                $fieldHtml = CHtml::passwordField($field, $value, $htmlOptions);
                break;
			
			case 'enum':
            case 'select':
            case 'dropdown':
            case 'dropdownlist':
                $data = self::keyAt('data', $fieldInfo, array());
				$viewType = self::keyAt('viewType', $fieldInfo, 'dropdownlist');
				$multiple = (bool)self::keyAt('multiple', $fieldInfo, false);
				$storeType = self::keyAt('storeType', $fieldInfo, 'separatedValues');
				$separator = self::keyAt('separator', $fieldInfo, ';');
				$emptyOption = (bool)self::keyAt('emptyOption', $fieldInfo, false);
				$emptyValue  = self::keyAt('emptyValue', $fieldInfo, '');
				
                $selectedValues = '';
                if(is_array($value)){
                    // Actually after form submit
                    $selectedValues = $value;
                }else{
                    // Actually after reading data from database
                    if($storeType == 'serialized'){
                        if(CString::isSerialized($value)){
                            $deserializeValue = htmlspecialchars_decode($value);
                            $selectedValues = unserialize($deserializeValue);	
                        }
                    }else{
                        $selectedValues = explode($separator, $value);	
                    }
                }

				if($viewType == 'checkboxes'){
					$htmlOptions['listWrapperTag'] = 'ul';
					$htmlOptions['listWrapperClass'] = 'checkboxes-list';
					$htmlOptions['template'] = '<li>{input} {label}</li>';
					$htmlOptions['separator'] = '';
					$htmlOptions['multiple'] = $multiple;
					$fieldHtml = CHtml::checkBoxList($field, $selectedValues, $data, $htmlOptions);
				}else{
					if($emptyOption){
						$data = array(''=>$emptyValue) + $data;
					}
					$htmlOptions['multiple'] = $multiple;
					$fieldHtml = CHtml::dropDownList($field, $selectedValues, $data, $htmlOptions);
				}                
                break;
            
			case 'file':
				if(APPHP_MODE == 'demo') $htmlOptions['disabled'] = 'disabled';
                $fieldHtml = CHtml::fileField($field, $value, $htmlOptions);
                break;
            
			case 'image':
                $src = self::keyAt('src', $fieldInfo, '');
                $alt = self::keyAt('alt', $fieldInfo, '');
                if(!self::issetKey('name', $htmlOptions)) $htmlOptions['name'] = $field;
                $fieldHtml = CHtml::image($src, $alt, $htmlOptions);
                break;
			
			case 'imageupload':
				// Image max size label
				$maxSize = self::keyAt('maxSize', $fieldInfo, 0);
				if($maxSize > 0) $appendLabel = ' ('.A::t('core', 'max.: {maxsize}', array('{maxsize}'=>$maxSize)).')';
				// Image options
				$showImage = (bool)self::keyAt('imageOptions.showImage', $fieldInfo, false);
				// ImagePath is deprecated from v0.6.0
				if(self::issetKey('imageOptions.imagePath', $fieldInfo)){
					$filePath = $fieldInfo['imageOptions']['imagePath'];	
				}else{
					$filePath = self::keyAt('fileOptions.filePath', $fieldInfo, '');	
				}				
				$showImageName = (bool)self::keyAt('imageOptions.showImageName', $fieldInfo, false);
				$showImageSize = (bool)self::keyAt('imageOptions.showImageSize', $fieldInfo, false);
				$showImageDimensions = (bool)self::keyAt('imageOptions.showImageDimensions', $fieldInfo, false);
				$imageClass = self::keyAt('imageOptions.imageClass', $fieldInfo, '');
				$imageHtmlOptions = array();
				if(!empty($imageClass)) $imageHtmlOptions['class'] = $imageClass;
				// Delete link options
				$showDeleteLink = (bool)self::keyAt('deleteOptions.showLink', $fieldInfo, false);
				$deleteLinkPath = self::keyAt('deleteOptions.linkUrl', $fieldInfo, '');
				$deleteLinkText = self::keyAt('deleteOptions.linkText', $fieldInfo, A::t('core', 'Delete'));
				$imageText = '';
                // Rotate link options
                $showRotateLinks = isset($fieldInfo['rotateOptions']['showLinks']) ? (bool)$fieldInfo['rotateOptions']['showLinks'] : false;
                $rotateRightLink = isset($fieldInfo['rotateOptions']['linkRotateRigth']) ? $fieldInfo['rotateOptions']['linkRotateRigth'] : '';
                $rotateLeftLink = isset($fieldInfo['rotateOptions']['linkRotateLeft']) ? $fieldInfo['rotateOptions']['linkRotateLeft'] : '';
                $iconRotateRight = isset($fieldInfo['rotateOptions']['iconRotateRight']) ? $fieldInfo['rotateOptions']['iconRotateRight'] : '';
                $iconRotateLeft = isset($fieldInfo['rotateOptions']['iconRotateLeft']) ? $fieldInfo['rotateOptions']['iconRotateLeft'] : '';
                $rotateText = '';
				// File options
				$fileHtmlOptions = self::keyAt('fileOptions', $fieldInfo, array());
				$showAlways = (bool)self::keyAt('fileOptions.showAlways', $fieldInfo, false);
				if($showAlways) unset($fileHtmlOptions['showAlways']);
								
				$fieldHtml = CHtml::openTag('div', array('style'=>'display:inline-block;'));
				// Image
				if($showImage && !empty($value)) $fieldHtml .= CHtml::image($filePath.$value, '', $imageHtmlOptions).'<br>';
                // Rotate buttons
                if($showRotateLinks && !empty($value) && APPHP_MODE !== 'demo'){
                    $rotateText .= CHtml::openTag('label', array('style'=>'width:100%;'));
                    if(is_file($iconRotateLeft) && is_file($iconRotateRight)){
                        $rotateText .= CHtml::openTag('a', array('href'=>(!empty($rotateLeftLink) ? $rotateLeftLink : ''), 'title'=>A::t('core', 'Rotate 90 degrees Left'), 'class'=>'link-rotate-left'));
                        $rotateText .= CHtml::image($iconRotateLeft, '', array('class'=>'icon-rotate-left'));
                        $rotateText .= CHtml::closeTag('a').' &nbsp;';
                        $rotateText .= CHtml::openTag('a', array('href'=>(!empty($rotateRightLink) ? $rotateRightLink : ''), 'title'=>A::t('core', 'Rotate 90 degrees Right'), 'class'=>'link-rotate-right'));
                        $rotateText .= CHtml::image($iconRotateRight, '', array('class'=>'icon-rotate-right'));
                        $rotateText .= CHtml::closeTag('a');
                    }else{
                        $rotateText .= CHtml::link(A::t('core', 'Rotate 90 degrees Left'), (!empty($rotateLeftLink) ? $rotateLeftLink : '#')).' &nbsp;';
                        $rotateText .= CHtml::link(A::t('core', 'Rotate 90 degrees Right'), (!empty($rotateRightLink) ? $rotateRightLink : '#'));
                    }
                    $rotateText .= CHtml::closeTag('label');
                    $fieldHtml .= $rotateText;
                }
				// Image text 
				if($showImageName && !empty($value)) $imageText .= $value.' ';
				// Image size and dimensions
				if(!empty($value)){					
					if($showImageSize || $showImageDimensions) $imageText .= ' (';
					$imageFileSize = '';
					if($showImageSize && !empty($value)){
						$imageFileSize = CFile::getFileSize($filePath.$value, 'kb').' Kb';
						$imageText .= $imageFileSize;
					}
					if($showImageDimensions && !empty($value)){
						$imageDimensions = CFile::getImageDimensions($filePath.$value);
						$imageText .= (!empty($imageFileSize) ? ', ' : '').$imageDimensions['width'].'x'.$imageDimensions['height'];
					}
					if($showImageSize || $showImageDimensions) $imageText .= ') ';
				}
				// Delete link
				if($showDeleteLink && !empty($value) && APPHP_MODE !== 'demo'){
					$imageText .= ' &nbsp;'.CHtml::link($deleteLinkText, (!empty($deleteLinkPath) ? $deleteLinkPath : '#'));	
				} 
				// Middle text
				if($imageText) $fieldHtml .= CHtml::label($imageText, '', array('style'=>'width:100%;margin-bottom:5px;'));				
				// File field
				if(!self::issetKey('style', $fileHtmlOptions)) $fileHtmlOptions['style'] = 'margin-bottom:5px;';
				else $fileHtmlOptions['style'] .= 'margin-bottom:5px;';				
				if(APPHP_MODE == 'demo') $fileHtmlOptions['disabled'] = 'disabled';
				if($showAlways || empty($value)) $fieldHtml .= CHtml::fileField($field, $value, $fileHtmlOptions);				
				$fieldHtml .= CHtml::closeTag('div');				
				break;
			
			case 'fileupload':
				// File max size label
				$maxSize = self::keyAt('maxSize', $fieldInfo, 0);
				if($maxSize > 0) $appendLabel = ' ('.A::t('core', 'max.: {maxsize}', array('{maxsize}'=>$maxSize)).')';

				// File options
				$showType = (bool)self::keyAt('iconOptions.showType', $fieldInfo, false);
				$showFileName = (bool)self::keyAt('iconOptions.showFileName', $fieldInfo, true);
				$showFileSize = (bool)self::keyAt('iconOptions.showFileSize', $fieldInfo, false);
				$filePath = self::keyAt('fileOptions.filePath', $fieldInfo, '');
				$fileDownload = self::keyAt('download', $fieldInfo, false);

				$imageHtmlOptions = array();
				if(!empty($imageClass)) $imageHtmlOptions['class'] = $imageClass;
				// Delete link options
				$showDeleteLink = (bool)self::keyAt('deleteOptions.showLink', $fieldInfo, false);
				$deleteLinkPath = self::keyAt('deleteOptions.linkUrl', $fieldInfo, '');
				$deleteLinkText = self::keyAt('deleteOptions.linkText', $fieldInfo, A::t('core', 'Delete'));			
				
				$icontText = '';
				// File options
				$fileHtmlOptions = self::keyAt('fileOptions', $fieldInfo, array());
				$showAlways = (bool)self::keyAt('fileOptions.showAlways', $fieldInfo, false);
				if($showAlways) unset($fileHtmlOptions['showAlways']);
								
				$fieldHtml = CHtml::openTag('div', array('style'=>'display:inline-block;'));
				// File icon
				if($showType && !empty($value)){
					$ext = CFile::getExtension($filePath.$value);
					$iconsPath = 'templates/backend/images/mimetypes/';
					if(file_exists($iconsPath.$ext.'.png')){
						$fieldHtml .= CHtml::image($iconsPath.$ext.'.png', 'mime type - '.$ext);
					}else{
						$fieldHtml .= CHtml::image($iconsPath.'file.png', 'unknown mime type');
					}
					$fieldHtml .= '<br>';
				}
				// File text 
                if($showFileName && !empty($value)){
                    if($fileDownload){
                        $icontText .= CHtml::link($value, $filePath.$value, array('download'=>$value)).' ';
                    }else{
                        $icontText .= $value.' ';
                    }
                }
				if($showFileSize && !empty($value)){
					$icontText .= ' ('.CFile::getFileSize($filePath.$value, 'kb').' Kb) ';
				}
				// Delete link
				if($showDeleteLink && !empty($value) && APPHP_MODE !== 'demo'){
					$icontText .= ' &nbsp;'.CHtml::link($deleteLinkText, (!empty($deleteLinkPath) ? $deleteLinkPath : '#'));	
				} 
				// Middle text
				if($icontText) $fieldHtml .= CHtml::label($icontText, '', array('style'=>'width:100%;margin-bottom:5px;'));
				// File field
				$fileHtmlOptions = array('style' => 'margin-bottom:5px;');
				if(APPHP_MODE == 'demo') $fileHtmlOptions['disabled'] = 'disabled';
				if($showAlways || empty($value)) $fieldHtml .= CHtml::fileField($field, $value, $fileHtmlOptions);				
				$fieldHtml .= CHtml::closeTag('div');				
				break;
            
			case 'textarea':
				$maxLength = (int)self::keyAt('maxLength', $htmlOptions, 0);
				if($maxLength > 0) $appendLabel = '<br>'.A::t('core', 'max.: {maxchars} chars', array('{maxchars}'=>$maxLength));
                $fieldHtml = CHtml::textArea($field, $value, $htmlOptions);
                break;
            
			case 'radio':
			case 'radiobutton':
				$checked = (bool)self::keyAt('checked', $fieldInfo, false);
				if(!empty($value)) $htmlOptions['value'] = $value;
				$fieldHtml = CHtml::radioButton($field, $checked, $htmlOptions);
				break;
			
			case 'radiobuttons':
			case 'radiobuttonlist':
				$data = self::keyAt('data', $fieldInfo, array(), 'is_array');
				$checked = self::keyAt('checked', $fieldInfo, false);
				$htmlOptions['separator'] = "\n";
				$fieldHtml = CHtml::radioButtonList($field, $checked, $data, $htmlOptions);
				break;
			
            case 'color':
                $fieldId = self::keyAt('id',  $htmlOptions, $formName.'_'.$field);
                $fieldHtml  = CHtml::colorField($field, $value, $htmlOptions);
                $fieldHtml .= '&nbsp;';
                $fieldHtml .= CHtml::tag('span', array('id'=>'val_color_'.self::$_colorCount), $value);
                A::app()->getClientScript()->registerScript(
                    'color_'.self::$_colorCount,
                    'jQuery("#'.$fieldId.'").change(function() {
                        var color = jQuery(this).val();
                        jQuery("#val_color_'.self::$_colorCount.'").text(color);
                    });',
                    4
                );
                self::$_colorCount++;
                break;

            case 'email':
                $fieldHtml = CHtml::emailField($field, $value, $htmlOptions);
                break;

            case 'textbox':
            default:
				$autocompleteEnabled = self::keyAt('enable', $autocomplete);
				$autocompleteParams = self::keyAt('params', $autocomplete);
				$autocompleteAjaxHandler = self::keyAt('ajaxHandler', $autocomplete, '');
				$autocompleteMinLength = self::keyAt('minLength', $autocomplete, 1);
				$autocompleteDefault = self::keyAt('default', $autocomplete, $value);
				$autocompleteReturnId = self::keyAt('returnId', $autocomplete, true);

				if($autocompleteEnabled){
					A::app()->getClientScript()->registerCssFile('assets/vendors/jquery/jquery-ui.min.css');
					// Already included in backend default.php
					if(A::app()->view->getTemplate() != 'backend'){
						A::app()->getClientScript()->registerScriptFile('assets/vendors/jquery/jquery-ui.min.js', 2);
					}
					
                    $params = '';
                    $numVar = 0;
					$fieldSearch = $field.'_result';
					$cRequest = A::app()->getRequest();
					$arrParams = array();

                    if(is_array($autocompleteParams)){
                        foreach($autocompleteParams as $paramKey => $paramValue){
                            if(is_array($paramValue)){
                                $numSubVar = 0;
                                $arrSubParams = array();

                                if(!CValidator::isVariable($paramKey)){
                                    $paramKey = 'var'.$numVar++;
                                }

                                if(isset($paramValue['function'])){
                                    $arrParams[] = $paramKey.': '.$paramValue['function'];
                                    continue;
                                }

                                $str = $paramKey.': {';
                                foreach($paramValue as $subKey => $subValue){
                                    if(!CValidator::isVariable($subKey)){
                                        $subKey = 'var'.$numSubVar++;
                                    }
                                    $arrSubParams[] = $subKey.': "'.CHtml::encode($subValue).'"';
                                }
                                $str .= implode(', ', $arrSubParams);
                                $str .= '}';
                                $arrParams[] = $str;
                            }else{
                                if(!CValidator::isVariable($paramKey)){
                                    $paramKey = 'var'.$numVar++;
                                }
                                $arrParams[] = $paramKey.': "'.CHtml::encode($paramValue).'"';
                            }
                        }
                        $params = (!empty($arrParams) ? implode(", \n", $arrParams) : '');
                    }

					A::app()->getClientScript()->registerScript(
						'autocomplete_'.self::$_autocompleteCount++,
						'jQuery("#'.$fieldSearch.'").autocomplete({
							source: function(request, response){
								$.ajax({
									url: "'.CHtml::encode($autocompleteAjaxHandler).'",
									global: false,
									type: "POST",
									data: ({
										'.$cRequest->getCsrfTokenKey().': "'.$cRequest->getCsrfTokenValue().'",
										act: "send",
										search : jQuery("#'.$fieldSearch.'").val(),
										'.$params.'
									}),
									dataType: "json",
									async: true,
									error: function(html){
										'.((APPHP_MODE == 'debug') ? 'alert("AJAX: cannot connect to the server or server response error! Please try again later.");' : '').'
									},
									success: function(data){
										if(data.length == 0){
											jQuery("#'.$htmlOptions['id'].'").val("");
											response({label: "'.A::te('core', 'No matches found').'"});
										}else{
											response($.map(data, function(item){
												if(item.label !== undefined){
													return {id: '.($autocompleteReturnId ? 'item.id' : 'item.label').', label: item.label}	
												}else{
													// Empty search value if nothing found													
													jQuery("#'.$htmlOptions['id'].'").val('.($autocompleteReturnId ? '""' : 'jQuery("#'.$fieldSearch.'").val()').');
												}
											}));
										}
									}
								});
							},
							minLength: '.(int)$autocompleteMinLength.',
							select: function(event, ui) {
								jQuery("#'.$htmlOptions['id'].'").val(ui.item.id);
								if(typeof(ui.item.id) == "undefined"){
									jQuery("#'.$fieldSearch.'").val("");
									return false;
								}
							}
						});',
						4
					);

					// Draw hidden field for field with autocomplete input
					$fieldHtml = CHtml::hiddenField($field, CHtml::encode($value), $htmlOptions);
					// Draw textbox					
					$fieldValueSearch = $cRequest->isPostRequest() ? $cRequest->getPost($fieldSearch, '', $autocompleteDefault) : $autocompleteDefault;
					$htmlOptions['id'] = $fieldSearch;
					$fieldHtml .= CHtml::textField($fieldSearch, CHtml::encode($fieldValueSearch), $htmlOptions);
				}else{
					// Draw textbox
					$fieldHtml = CHtml::textField($field, $value, $htmlOptions);
				}						
				break;
        }
		
        if($type == 'hidden'){
            $output .= $fieldHtml.self::NL;    
        }else{
            $output .= CHtml::openTag($fieldWrapperTag, array('class'=>$fieldWrapperClass, 'id'=>($autoGenerateId) ? $formName.'_row_'.self::$_rowCount++ : ''));
			// old placement: $output .= $prependCode;
            if($title){
				$for = self::keyAt('id', $htmlOptions, false);
				$tooltipText = !empty($tooltip) ? ' '.CHtml::link('', false, array('class'=>'tooltip-icon', 'title'=>$tooltip)) : '';
				$output .= CHtml::label($title.(trim($title) !== '' ? ':' : '').$tooltipText.(($mandatoryStar) ? CHtml::$afterRequiredLabel : '').$appendLabel, $for);
            }
			$output .= $prependCode;
            $output .= $fieldHtml;
			$output .= $appendCode;
            $output .= CHtml::closeTag($fieldWrapperTag).self::NL;                
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

 		// Remove disabled buttons
		foreach($buttons as $key => $val){
			if(self::issetKey('disabled', $val) && (bool)self::keyAt('disabled', $val) === true) unset($buttons[$key]);
		}

        // Draw buttons
        if(count($buttons) > 0){
            $additionalClass = ($placement == 'top') ? ' bw-top' : ' bw-bottom';
            $output .= CHtml::openTag('div', array('class'=>'buttons-wrapper'.$additionalClass)).self::NL;
            foreach($buttons as $button => $buttonInfo){
                $type = self::keyAt('type', $buttonInfo, '');
                $value = self::keyAt('value', $buttonInfo, '');
				$htmlOptions = self::keyAt('htmlOptions', $buttonInfo, array(), 'is_array');
                if(!self::issetKey('value', $htmlOptions)) $htmlOptions['value'] = $value;
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
