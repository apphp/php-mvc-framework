<?php
/**
 * CDataForm widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE (static):
 * ----------               ----------                  ----------
 * init			                                        _prepareFieldInfo
 *                                                      _additionalParams
 *														_setValidationAttributes
 */

class CDataForm extends CWidgs
{
	
	const NL = "\n";
	/** @var array */
	private static $_allowedActs = array('send', 'change');
	/** @var bool */
	private static $_clientValidation = true;
	/** @var bool */
	private static $_clientValidationOnlyRequired = true;
	
	/**
	 * Draws HTML form control
	 * @param array $params
	 *
	 * Notes:
	 *   - to prevent double quotes issue use 'encode'=>true in htmlOptions
	 *   - insert code (for all fields): 'prependCode=>'', 'appendCode'=>''
	 *   - for "checkbox" 'default'=>1 means checked
	 *
	 *   - for INSERT operations don't define 'primaryKey' option at all
	 *   - attribute 'default'=>'' or 'defaultAddMode'=>'' is used for Add mode only
	 *   - attribute 'defaultEditMode'=>'' is used for Edit mode only
	 *   - to use <button> tag for buttons use 'buttonTag'=>'button'
	 *   - to show buttons at the top use 'buttonsPosition'=>'top' (bottom, top or both)
	 *   - attribute 'validation'=>array('unique'=>true, 'uniqueCondition'=>'') is used for Add/Edit modes for standard fields (not for translation fields)
	 *   - to disable:
	 * 		- field or button use: 'disabled'=>true
	 *   	- the whole section use: 'separatorInfo' => array(..., 'disabled'=>true),
	 * 	 - validation types:
	 *  	alpha, alphabetic, alphanumeric, alphadash, numeric, variable, mixed(alphanumericspaces), seoLink, phone, phoneString, username, timeZone, zipCode,
	 *  	password, email, fileName, identity|identityCode, date, integer, positiveInteger, percent, htmlSize,
	 *  	float, any, text, confirm, url, ip, set, hexColor, captcha, regex
	 *  	range ('minValue'=>'' and 'maxValue'=>'') or ('minLength'=>'', 'maxLength'=>''),
	 *   - attribute 'validation'=>array(..., 'forbiddenChars'=>array('+', '$')) is used to define forbidden characters
	 *   - attribute 'validation'=>array(..., 'trim'=>true) - removes spaces from field value before validation
	 *   - 'successCallback' - callback methods of controller (must be public methods)
	 *   - 'callback'=>array('function'=>'functionName', 'params'=>$functionParams)
	 *      callback of closure function that is called when item created (available for "labels" and "html" only), $record - current record
	 *      PHP_VERSION >= 5.3.0 $functionName = function($record, $params){ return $record['field_name']; }
	 *      Ex.: function callbackFunction($value, $params){...}
	 * 		Ex.: 'callback'=>array('function'=>'nl2br', 'params'=>false) - passed a whole field value
	 *   - Encryption for standard text fields: 'encryption'=>array('enabled'=>CConfig::get('text.encryption'), 'encryptAlgorithm'=>CConfig::get('text.encryptAlgorithm'), 'encryptKey'=>CConfig::get('text.encryptKey'))
	 *   - separatorName must starts from word 'separator'
	 *   - select classes: 'class'=>'chosen-select-filter' or 'class'=>'chosen-select'
	 *   - attribute 'autocomplete'=>array(..., 'varN'=>array('function'=>'$("#id").val()')) passed as a parameter jQuery or javascript the function instead of use the variable
	 *
	 * Usage: (in view file)
	 *  echo CWidget::create('CDataForm', array(
	 *       'model'			=> 'tableName',
	 *		 'resetBeforeStart' => false,
	 *       'primaryKey'		=> 1,
	 *       'operationType'	=> 'add | edit',
	 *       'action'			=> 'locations/add | locations/edit/id/1',
	 *       'successUrl'		=> 'locations/manage/msg/1 | locations/manage/id/{id} | locations/manage (when alertType = flash)',
	 *       'successCallback'	=> array('add'=>'', 'edit'=>''),
	 *       'cancelUrl'		=> 'locations/manage',
	 *       'passParameters'	=> false,
	 *		 'linkType' 		=> 0,
	 *       'method'			=> 'post',
	 *       'htmlOptions'		=> array(
	 *       	 'id'				=> 'form-contact',
	 *           'name'				=> 'form-contact',
	 *           'enctype'			=> 'multipart/form-data', // multipart/form-data, application/x-www-form-urlencoded, text/plain or ''
	 *           'autoGenerateId'	=> true
	 *       ),
	 *       'requiredFieldsAlert'=>true,
	 *       'fieldSets'		=> array('type'=>'frameset|tabs|tabsList', 'firstTabActive'=>true),
	 *       'fieldWrapper'=>array('tag'=>'div', 'class'=>'row'),
	 *       'fields'=>array(
	 *         	 'separatorName' =>array(
	 *               'separatorInfo'=>array('legend'=>A::t('app', 'Headers & Footers'), 'disabled'=>false),
	 *               'field_1'=>array('type'=>'textbox', 'title'=>'Field 1', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'htmlOptions'=>array()),
	 *               ...
	 *           ),
	 *           'field_1'=>array('type'=>'textbox',        'title'=>'Username',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'username', 'minLength'=>6, 'maxLength'=>32), 'htmlOptions'=>array()),
	 *           'field_2'=>array('type'=>'password',       'title'=>'Password',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6, 'maxLength'=>25, 'simplePassword'=>false), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'encryptSalt'=>CConfig::get('password.encryptSalt')), 'htmlOptions'=>array()),
	 *           'field_3'=>array('type'=>'textbox',        'title'=>'Confirm P',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_2'), 'htmlOptions'=>array()),
	 *           'field_4'=>array('type'=>'textbox',        'title'=>'Email',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'email'), 'htmlOptions'=>array()),
	 *           'field_5'=>array('type'=>'textbox',        'title'=>'Confirm E',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_4'), 'htmlOptions'=>array()),
	 *           'field_6'=>array('type'=>'textbox',        'title'=>'Mixed',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'mixed'), 'htmlOptions'=>array()),
	 *           'field_7'=>array('type'=>'textbox',        'title'=>'Field',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array()),
	 *           'field_8'=>array('type'=>'textbox',        'title'=>'Format',     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5)), 'htmlOptions'=>array()),
	 *           'field_9'=>array('type'=>'textbox',        'title'=>'Autocomplete', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'autocomplete'=>array('enable'=>true, 'ajaxHandler'=>'part/to/handler/file', 'minLength'=>3, 'default'=>'', 'returnId'=>true, 'params'=>array()), 'htmlOptions'=>array()),
	 *          'field_10'=>array('type'=>'textarea',       'title'=>'Text',       'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'255')),
	 *          'field_11'=>array('type'=>'checkbox',       'title'=>'Checkbox',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>array()),
	 *          'field_12'=>array('type'=>'select',    		'title'=>'Select',     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array_keys(array(...))), 'data'=>array(), 'emptyOption'=>true, 'emptyValue'=>'', 'viewType'=>'dropdownlist|checkboxes', 'multiple'=>false, 'storeType'=>'serialized|separatedValues', 'separator'=>';', 'htmlOptions'=>array('class'=>'chosen-select-filter')),
	 *          'field_13'=>array('type'=>'color',          'title'=>'Color',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'hexColor'), 'htmlOptions'=>array()),
	 *          'field_14'=>array('type'=>'email',          'title'=>'Email',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'email'), 'htmlOptions'=>array()),
	 *          'field_15'=>array('type'=>'radioButton',    'title'=>'Radio',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'htmlOptions'=>array()),
	 *          'field_16'=>array('type'=>'radioButtonList','title'=>'RadioList',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'data'=>array(), 'htmlOptions'=>array()),
	 *          'field_17'=>array(
	 *              'type'			 	=> 'imageUpload',
	 *              'title'			 	=> 'Image Uploader',
	 *              'tooltip'		 	=> '',
	 *              'default'		 	=> '',
	 *              'validation'	 	=> array('required'=>true, 'type'=>'image', 'targetPath'=>'templates/backend/images/accounts/', 'maxSize'=>'100k', 'maxWidth'=>'120px', 'maxHeight'=>'90px', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>CHash::getRandomString(10), 'filePrefix'=>'', 'filePostfix'=>'', 'htmlOptions'=>array()),
	 *          	'imageOptions'	 	=> array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'showImageDimensions'=>true, 'imageClass'=>'avatar'),
	 *          	'thumbnailOptions'	=> array('create'=>true, 'directory'=>'', 'field'=>'', 'postfix'=>'_thumb', 'width'=>'', 'height'=>''),
	 *          	'deleteOptions'	 	=> array('showLink'=>true, 'linkUrl'=>'admins/edit/avatar/delete', 'linkText'=>'Delete'),
	 *          	'rotateOptions'		=> array('showLinks'=>true, 'linkRotateLeft'=>'admins/edit/rotate/left', 'linkRotateRigth'=>'admin/edit/rotate/right', 'iconRotateLeft'=>'templates/backend/images/rotateLeft.png', 'iconRotateRight'=>'templates/backend/images/rotateRight.png'),
	 *          	'watermarkOptions'	=> array('enable'=>true, 'text'=>'Test Text'),
	 *          	'fileOptions'	 	=> array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'templates/backend/files/accounts/'),
	 *          	'appendCode'		=> ' (allowed types): zip, pdf)'
	 *          ),
	 *          'field_18'=>array(
	 *              'type'			 	=> 'fileUpload',
	 *              'title'			 	=> 'File Uploader',
	 *              'tooltip'		 	=> '',
	 *              'default'		 	=> '',
	 *              'download'          => false,
	 *              'validation'	 	=> array('required'=>true, 'type'=>'file', 'targetPath'=>'templates/backend/files/accounts/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml, application/pdf', 'fileName'=>CHash::getRandomString(10), 'filePrefix'=>'', 'filePostfix'=>'', htmlOptions'=>array()),
	 *          	'iconOptions'	 	=> array('showType'=>true, 'showFileName'=>true, 'showFileSize'=>true),
	 *          	'deleteOptions'	 	=> array('showLink'=>true, 'linkUrl'=>'admins/edit/avatar/delete', 'linkText'=>'Delete'),
	 *          	'fileOptions'	 	=> array('showAlways'=>false, 'class'=>'file', 'size'=>'25', 'filePath'=>'templates/backend/files/accounts/')
	 *          	'appendCode'		=> ' (allowed types): jpeg, jpg, png, gif)'
	 *          ),
	 *          'field_19'=>array('type'=>'label',  	'title'=>'Label', 		'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>$functionName, 'params'=>$functionParams)),
	 *          'field_20'=>array('type'=>'html',  		'title'=>'Html', 		'tooltip'=>'', 'default'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>'', 'stripTags'=>false, 'callback'=>array('function'=>$functionName, 'params'=>$functionParams)),
	 *          'field_21'=>array('type'=>'link',   	'title'=>'Link', 		'tooltip'=>'', 'linkUrl'=>'path/to/param', 'linkText'=>'', 'htmlOptions'=>array()),
	 *          'field_22'=>array('type'=>'videoLink',  'title'=>'Video Link', 	'tooltip'=>'', 'default'=>'', 'preview'=>false, 'validation'=>array('required'=>false, 'type'=>'url', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>125)),
	 *          'field_23'=>array('type'=>'datetime', 	'title'=>'Date Picker', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'date', 'maxLength'=>19), 'htmlOptions'=>array('maxlength'=>'19'), 'definedValues'=>array(), 'viewType'=>'datetime|date|time', 'dateFormat'=>'yy-mm-dd', 'timeFormat'=>'HH:mm:ss', 'buttonTrigger'=>true, 'minDate'=>'', 'maxDate'=>'', 'yearRange'=>'-100:+0'),
	 *			'field_24'=>array('type'=>'captcha',  	'title'=>'Captcha', 	'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'captcha'), 'htmlOptions'=>array()),
	 *          'field_25'=>array('type'=>'vocabulary', 'value'=>$fileContent, 'title'=>A::t('app', 'Vocabulary'), 'htmlOptions'=>array('class'=>'full')),
	 *          'field_26'=>array('type'=>'hidden', 	'default'=>'', 'htmlOptions'=>array()),
	 *          'field_27'=>array('type'=>'data', 		'default'=>''),
	 *       ),
	 *       'translationInfo'=>array('relation'=>array('field_from', 'field_to'), 'languages'=>Languages::model()->findAll(array('condition'=>'is_active = 1', 'orderBy'=>'sort_order ASC')),
	 *       'translationFields'=>array(
	 *           'fields_1_1'=>array('type'=>'textbox', 'title'=>'Field 1-1', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any'), 'htmlOptions'=>array()),
	 *           'fields_1_2'=>array('type'=>'textarea', 'title'=>'Field 1-2', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>5000), 'htmlOptions'=>array('maxLength'=>'5000')),
	 *       ),
	 *       'buttons'=>array(
	 *          'submit'=>array('type'=>'submit', 'value'=>'Send', 'htmlOptions'=>array('name'=>'')),
	 *          'submitUpdate'=>array('type'=>'submit', 'value'=>'Update', 'htmlOptions'=>array('name'=>'btnUpdate')),
	 *          'submitUpdateClose'=>array('type'=>'submit', 'value'=>'Update & Close', 'htmlOptions'=>array('name'=>'btnUpdateClose')),
	 *          'reset'=>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array()),
	 *          'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
	 *          'custom'=>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"jQuery(location).attr('href','categories/index');")),
	 *       ),
	 *       'buttonsPosition'=>'bottom',
	 *       'messagesSource'=>'core',
	 * 		 'customMessages'=>array('insert'=>array('success'=>'', 'error'=>''), 'update'=>array('success'=>'', 'error'=>'')),
	 *       'showAllErrors'=>false,
	 *		 'alerts'=>array('type'=>standard|flash, 'itemName'=>A::t('app', 'Field Name').' #'.$id),
	 *		 'clientValidation'=>array('enabled'=>true, 'onlyRequired'=>false),
	 *       'return'=>true,
	 *  ));
	 */
	public static function init($params = array())
	{
		parent::init($params);
		
		$baseUrl = A::app()->getRequest()->getBaseUrl();
		$cRequest = A::app()->getRequest();
		$output = '';
		
		$model = self::params('model', '');
		$resetBeforeStart = (bool)self::params('resetBeforeStart', false);
		$primaryKey = (int)self::params('primaryKey', '');
		$operationType = self::params('operationType', 'add', 'in_array', array('edit', 'add'));
		$action = self::params('action', '');
		$successUrl = self::params('successUrl', '');
		$successCallbackAdd = self::params('successCallback.add', '');
		$successCallbackEdit = self::params('successCallback.edit', '');
		$cancelUrl = self::params('cancelUrl', '');
		$method = self::params('method', 'post');
		$htmlOptions = (array)self::params('htmlOptions', array(), 'is_array');
		$requiredFieldsAlert = self::params('requiredFieldsAlert', false);
		$fieldSets = self::params('fieldSets', array(), 'is_array');
		$fieldWrapperTag = self::params('fieldWrapper.tag', 'div');
		$fieldWrapperClass = self::params('fieldWrapper.class', 'row');
		$linkType = (int)self::params('linkType', 0); /* Link type: 0 - standard, 1 - SEO */
		$return = (bool)self::params('return', true);
		
		$fields = self::params('fields', array(), 'is_array');
		$translationInfo = self::params('translationInfo', array());
		$languages = self::keyAt('languages', $translationInfo, array());
		
		$relation = self::keyAt('relation', $translationInfo, array());
		$keyFrom = isset($relation[0]) ? $relation[0] : '';
		$keyTo = isset($relation[1]) ? $relation[1] : '';
		
		$translationFields = self::params('translationFields', array());
		$msgSource = self::params('messagesSource', 'core');
		$customMessages = self::params('customMessages', array());
		$showAllErrors = (bool)self::params('showAllErrors', false);
		$alertType = self::params('alerts.type', 'standard');
		$alertItemName = self::params('alerts.itemName', '');
		$buttonsPosition = self::params('buttonsPosition', 'bottom');
		$buttons = self::params('buttons', array());
		if (self::issetKey('cancel', $buttons) && !empty($cancelUrl)) {
			$buttons['cancel']['htmlOptions']['onclick'] = 'jQuery(location).attr(\'href\',\'' . $baseUrl . $cancelUrl . '\');';
		}
		
		// Set Form ID if missing
		if (empty($htmlOptions['id'])) {
			$htmlOptions['id'] = $htmlOptions['name'];
		}
		
		// Validation
		self::$_clientValidation = (bool)self::params('clientValidation.enabled', true);
		self::$_clientValidationOnlyRequired = (bool)self::params('clientValidation.onlyRequired', false);
		if (self::$_clientValidation) {
			$lang = A::app()->getLanguage();
			A::app()->getClientScript()->registerScriptFile('assets/vendors/form-validator/jquery.form-validator.min.js', 2);
			A::app()->getClientScript()->registerScript(
				'form-validation',
				'jQuery.validate({
					lang: "' . (in_array($lang, A::app()->supportedLanguages()) ? $lang : 'en') . '",
					scrollToTopOnError: true,
					form: "#' . $htmlOptions['id'] . '",
					onError: function($form){
						// Remove changed in submit button and form (jQuery.fn.preventButtonDoubleSubmission)
						$form.data("submitted", false);
						var $btn = $form.find(".btn-disabled");
						$btn.val($btn.data("old-value"));
						$btn.removeClass("btn-disabled btn-hover");
						// Set focus on error field
						$form.find(".has-error > .error:first").focus();
					}
				});'
			);
		}
		
		$objModel = call_user_func_array($model . '::model', array());
		if ($resetBeforeStart) $objModel->reset();
		$tableName = CConfig::get('db.prefix') . $objModel->getTableName();
		$records = ($operationType == 'edit') ? $objModel->findByPk($primaryKey) : call_user_func_array($model . '::model', array());
		$recordsAssoc = !empty($records) ? $records->getFieldsAsArray() : array();
		
		$passParameters = (bool)self::params('passParameters', false);
		// Add additional parameters if allowed
		if ($passParameters) {
			$separateSymbol = preg_match('/\?/', $successUrl) ? '&' : '?';
			$successUrl .= self::_additionalParams(true, $linkType, $separateSymbol);
			$separateSymbol = preg_match('/\?/', $action) ? '&' : '?';
			$action .= self::_additionalParams(true, $linkType, $separateSymbol);
			$separateSymbol = preg_match('/\?/', $cancelUrl) ? '&' : '?';
			$cancelUrl .= self::_additionalParams(true, $linkType, $separateSymbol);
			if (self::issetKey('cancel', $buttons) && !empty($cancelUrl)) $buttons['cancel']['htmlOptions']['onclick'] = 'jQuery(location).attr(\'href\',\'' . $baseUrl . $cancelUrl . '\');';
		}
		
		$errorField = '';
		$msg = '';
		$msgType = '';
		
		// -----------------------------------------------------------
		// HANDLE FORM SUBMISSION
		// -----------------------------------------------------------
		if ($cRequest->getPost('APPHP_FORM_ACT') == 'send') {
			
			// 1. Prepare fields without framesets
			// 2. Remove disabled fields or framesets
			$fieldsMainTable = array();
			foreach ($fields as $field => $fieldInfo) {
				if (preg_match('/separator/i', $field) && is_array($fieldInfo)) {
					if (self::keyAt('separatorInfo.disabled', $fieldInfo, '') == true) {
						// Unset disabled section
						unset($fields[$field]);
					} else {
						if (self::issetKey('separatorInfo', $fieldInfo)) {
							unset($fieldInfo['separatorInfo']);
						}
						foreach ($fieldInfo as $iField => $iFieldInfo) {
							// Unset disabled field
							if (!isset($fieldInfo['disabled']) || $fieldInfo['disabled'] !== true) {
								$fieldsMainTable[$iField] = $iFieldInfo;
							}
						}
					}
				} else {
					// Unset disabled field
					if (!isset($fieldInfo['disabled']) || $fieldInfo['disabled'] !== true) {
						$fieldsMainTable[$field] = $fieldInfo;
					}
				}
			}
			
			// Merge fields from main table with translation fields for validation
			$transFieldsByLangs = array();
			foreach ($translationFields as $transFieldKey => $transFieldVal) {
				foreach ($languages as $lang) {
					$transField = $translationFields[$transFieldKey];
					$transField['title'] .= ' (' . $lang['name_native'] . ')';
					$transFieldsByLangs[$transFieldKey . '_' . $lang['code']] = $transField;
					//$transFieldsByLangs[$lang['code']][$transFieldKey] = $cRequest->getPost($transFieldKey.'_'.$lang['code']);
				}
			}
			$mergedFieldsForValidation = array_merge($fieldsMainTable, $transFieldsByLangs);
			
			// Validate the form with all fields
			$result = CWidget::create('CFormValidation', array('fields' => $mergedFieldsForValidation, 'messagesSource' => $msgSource, 'showAllErrors' => $showAllErrors));
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$errorField = $result['errorField'];
			} else {
				// Check fields for unique values
				foreach ($fieldsMainTable as $field => $fieldInfo) {
					if (!self::issetKey($field, $recordsAssoc)) continue;
					$vfUnique = (bool)self::keyAt('validation.unique', $fieldInfo, false);
					$vfUniqueCondition = self::keyAt('validation.uniqueCondition', $fieldInfo, '');
					///$vfValue = ($operationType == 'edit') ? $cRequest->getPost($field) : $cRequest->getPost($field);
					$vfValue = $cRequest->getPost($field);
					if ($vfUnique && $vfValue !== '') {
						$fieldTitle = self::keyAt('title', $fieldInfo, '');
						$sqlCount = $tableName . '.' . $field . ' = :code' . (($operationType == 'edit') ? ' AND ' . $tableName . '.id != :id' : '');
						$sqlCount .= !empty($vfUniqueCondition) ? ' AND ' . $vfUniqueCondition : '';
						$sqlParams = ($operationType == 'edit') ? array(':code' => $vfValue, ':id' => $primaryKey) : array(':code' => $vfValue);
						if ($objModel->count($sqlCount, $sqlParams) > 0) {
							$errorField = $field;
							$msg = A::t($msgSource, 'The field {title} allows only unique values, please re-enter!', array('{title}' => '<b>' . $fieldTitle . '</b>'));
							$msgType = 'error';
							break;
						}
					}
				}
				
				if (!$msgType) {
					// Update/change fields values (according to definition in CDataForm)
					foreach ($fieldsMainTable as $field => $fieldInfo) {
						$fieldType = strtolower(self::keyAt('type', $fieldInfo, ''));
						$viewType = self::keyAt('viewType', $fieldInfo, '');
						$validationType = self::keyAt('validation.type', $fieldInfo, '');
						$validationFormat = self::keyAt('validation.format', $fieldInfo, '');
						
						if (!in_array($fieldType, array('label', 'html'))) {
							$fieldValue = $cRequest->getPost($field);
							if (self::issetKey('htmlOptions.disabled', $fieldInfo) || (self::issetKey('disabled', $fieldInfo) && $fieldInfo['disabled'] === true)) {
								unset($recordsAssoc[$field]);
							} elseif ($validationType == 'float' && $validationFormat == 'european') {
								$fieldValue = CNumber::americanFormat($fieldValue, array('thousandSeparator' => false));
							} elseif ($fieldType == 'checkbox' && $fieldValue == '') {
								// Set default value to specific fields: checkbox (if it's empty), encrypted fields and image
								$fieldValue = 0;
							} elseif ($fieldType == 'image') {
								unset($recordsAssoc[$field]);
							} elseif ($fieldType == 'data') {
								$fieldValue = self::keyAt('default', $fieldInfo, '');
							} elseif ($fieldType == 'imageupload') {
								if (!empty($_FILES[$field]['name'])) {
									$targetPath = self::keyAt('validation.targetPath', $fieldInfo, '');
									$fileName = self::keyAt('validation.fileName', $fieldInfo, '');
									$filePrefix = self::keyAt('validation.filePrefix', $fieldInfo, '');
									$filePostfix = self::keyAt('validation.filePostfix', $fieldInfo, '');
									
									$fileExtension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
									if (!empty($fileName)) {
										$fileName = strtolower($fileName);
										$fileExtension = strtolower($fileExtension);
									} else {
										$fileName = pathinfo($_FILES[$field]['name'], PATHINFO_FILENAME);
									}
									
									$fieldValue = $filePrefix . $fileName . $filePostfix . '.' . $fileExtension;
									
									$thumbnailCreate = (bool)self::keyAt('thumbnailOptions.create', $fieldInfo, false);
									$thumbnailDirectory = self::issetKey('thumbnailOptions.directory', $fieldInfo) ? trim(self::keyAt('thumbnailOptions.directory', $fieldInfo), '/') . '/' : '';
									$thumbnailField = self::keyAt('thumbnailOptions.field', $fieldInfo, '');
									$thumbnailFieldPostfix = self::keyAt('thumbnailOptions.postfix', $fieldInfo, '_thumb');
									$thumbnailWidth = self::keyAt('thumbnailOptions.width', $fieldInfo, 0);
									$thumbnailHeight = self::keyAt('thumbnailOptions.height', $fieldInfo, 0);
									if ($thumbnailCreate) {
										// Create thumbnail
										$path = APPHP_PATH . DS . $targetPath;
										$thumbFileExt = substr(strrchr($fieldValue, '.'), 1);
										$thumbFileName = str_replace('.' . $thumbFileExt, '', $fieldValue);
										$thumbFileFullName = $thumbFileName . $thumbnailFieldPostfix . '.' . $thumbFileExt;
										CFile::copyFile($path . $fieldValue, $path . $thumbnailDirectory . $thumbFileFullName);
										$thumbFileRealName = CImage::resizeImage($path . $thumbnailDirectory, $thumbFileFullName, $thumbnailWidth, $thumbnailHeight);
										// Delete file if we make thumbnail on the same file
										if ($thumbnailField == $field) {
											CFile::deleteFile($path . $thumbnailDirectory . $fieldValue);
										} elseif ($thumbnailField != '') {
											// Thumbnail created update database table
											$records->set($thumbnailField, $thumbFileRealName);
										}
									}
									
									$watermark = self::keyAt('watermarkOptions.enable', $fieldInfo, false);
									$watermarkText = self::keyAt('watermarkOptions.text', $fieldInfo, '');
									if ($watermark && !empty($watermarkText)) {
										CImage::addWatermark(APPHP_PATH . DS . $targetPath . $thumbnailDirectory . $fieldValue, $watermarkText);
									}
								} else {
									$fieldValue = '';
									unset($recordsAssoc[$field]);
								}
							} elseif ($fieldType == 'fileupload') {
								if (!empty($_FILES[$field]['name'])) {
									$targetPath = self::keyAt('validation.targetPath', $fieldInfo, '');
									$fileName = self::keyAt('validation.fileName', $fieldInfo, '');
									$filePrefix = self::keyAt('validation.filePrefix', $fieldInfo, '');
									$filePostfix = self::keyAt('validation.filePostfix', $fieldInfo, '');
									
									$fileExtension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
									if (!empty($fileName)) {
										$fileName = strtolower($fileName);
										$fileExtension = strtolower($fileExtension);
									} else {
										$fileName = pathinfo($_FILES[$field]['name'], PATHINFO_FILENAME);
									}
									
									$fieldValue = $filePrefix . $fileName . $filePostfix . '.' . $fileExtension;
								} else {
									$fieldValue = '';
									unset($recordsAssoc[$field]);
								}
							} elseif ($fieldType == 'password') {
								$fieldEncryption = (bool)self::keyAt('encryption.enabled', $fieldInfo, false);
								if ($fieldEncryption) {
									$encryptAlgorithm = self::keyAt('encryption.encryptAlgorithm', $fieldInfo, '');
									$encryptSalt = self::keyAt('encryption.encryptSalt', $fieldInfo, '');
									if (empty($fieldValue)) {
										unset($recordsAssoc[$field]);
									} else {
										$fieldValue = CHash::create($encryptAlgorithm, $fieldValue, $encryptSalt);
									}
								}
							} elseif ($fieldType == 'textbox') {
								$fieldEncryption = (bool)self::keyAt('encryption.enabled', $fieldInfo, false);
								if ($fieldEncryption) {
									$encryptAlgorithm = self::keyAt('encryption.encryptAlgorithm', $fieldInfo, '');
									$encryptKey = self::keyAt('encryption.encryptKey', $fieldInfo, '');
									if (empty($fieldValue)) {
										$fieldValue = '';
										unset($recordsAssoc[$field]);
									} else {
										$fieldValue = CHash::encrypt($fieldValue, $encryptKey, $encryptAlgorithm);
									}
								}
							} elseif ($fieldType == 'select' && in_array($viewType, array('checkboxes', 'dropdownlist'))) {
								$multiple = (bool)self::keyAt('multiple', $fieldInfo, false);
								if ($multiple) {
									$storeType = self::keyAt('storeType', $fieldInfo, 'separatedValues');
									$separator = self::keyAt('separator', $fieldInfo, ';');
									if (is_array($fieldValue)) {
										if ($storeType == 'serialized') {
											$fieldValue = serialize($fieldValue);
										} elseif (is_array($fieldValue)) {
											$fieldValue = implode($separator, $fieldValue);
										}
									}
								}
							} elseif ($fieldValue === '') {
								if ($operationType == 'add') {
									if (self::issetKey('default', $fieldInfo)) $fieldValue = $fieldInfo['default'];
									elseif (self::issetKey('defaultAddMode', $fieldInfo)) $fieldValue = $fieldInfo['defaultAddMode'];
								} elseif ($operationType == 'edit') {
									if (self::issetKey('default', $fieldInfo)) $fieldValue = $fieldInfo['default'];
									elseif (self::issetKey('defaultEditMode', $fieldInfo)) $fieldValue = $fieldInfo['defaultEditMode'];
								}
							}
							
							if (self::issetKey($field, $recordsAssoc)) {
								// Update field values (onlt fields that defined in database table)
								$records->set($field, $fieldValue);
							} else {
								// Store field values that not defined in database table in a special array
								$records->setSpecialField($field, $fieldValue);
							}
						}
					}
					
					// Save main table
					if ($operationType == 'add') $objModel->clearPkValue();
					if ($records->save()) {
						// Save data into translation table
						if (count($translationFields) > 0) {
							$translationParams = array();
							foreach ($translationFields as $transFieldKey => $transFieldVal) {
								foreach ($languages as $lang) {
									$transFieldValue = $cRequest->getPost($transFieldKey . '_' . $lang['code']);
									
									$translationParams[$lang['code']][$transFieldKey] = (self::keyAt('validation.trim', $transFieldVal, false) == true) ? trim($transFieldValue) : $transFieldValue;
								}
							}
							if ($cRequest->getPost('APPHP_FORM_ACT') == 'send') {
								$keyFromValue = ($records->primaryKey() == $keyFrom) ? $records->getPrimaryKey() : $records->get($keyFrom);
							} else {
								$keyFromValue = $recordsAssoc[$keyFrom];
							}
							$records->saveTranslations(array('key' => $keyTo, 'value' => $keyFromValue, 'fields' => $translationParams));
						}
						
						// Prepare custom messages
						if ($operationType == 'add') {
							$msg = !empty($customMessages['insert']['success']) ? $customMessages['insert']['success'] : 'The adding operation has been successfully completed!';
						} else {
							$msg = !empty($customMessages['update']['success']) ? $customMessages['update']['success'] : 'The updating operation has been successfully completed!';
						}
						$msgType = 'success';
						
						// Get last inserted ID
						if ($operationType == 'add') $primaryKey = $objModel->getPrimaryKey();
						
						// Perform success Callbacks
						if ($operationType == 'add') {
							// Add Mode
							if (!empty($successCallbackAdd)) {
								call_user_func_array(array($model . 'Controller', $successCallbackAdd), array($primaryKey));
							}
						} else {
							// Edit Mode
							if (!empty($successCallbackEdit)) {
								call_user_func_array(array($model . 'Controller', $successCallbackEdit), array($primaryKey));
							}
						}
						
						// Redirect to success URL
						if (!empty($successUrl)) {
							if (!empty($alertItemName)) {
								if ($operationType == 'add') {
									if (!empty($customMessages['insert'])) {
										$msg = str_ireplace('{item_type}', $alertItemName, $msg);
									} else {
										$msg = A::t($msgSource, 'New {item_type} has been successfully added!', array('{item_type}' => $alertItemName));
									}
								} else {
									if (!empty($customMessages['update'])) {
										$msg = str_ireplace('{item_type}', $alertItemName, $msg);
									} else {
										$msg = A::t($msgSource, 'The {item_type} has been successfully updated!', array('{item_type}' => $alertItemName));
									}
								}
							}
							
							// Create flash alert
							if ($alertType == 'flash') {
								A::app()->getSession()->setFlash('alertType', $msgType);
								A::app()->getSession()->setFlash('alert', $msg);
							}
							
							if ($cRequest->isPostExists('btnUpdateClose')) {
								header('location: ' . $baseUrl . $successUrl);
								exit;
							} elseif ($cRequest->isPostExists('btnUpdate')) {
								// Do nothing
							} else {
								if ($operationType == 'add') $successUrl = str_replace('{id}', $primaryKey, $successUrl);
								header('location: ' . $baseUrl . $successUrl);
								exit;
							}
						}
						
						// Refresh data
						$records = $objModel->findByPk($primaryKey);
						if ($records) $recordsAssoc = $records->getFieldsAsArray();
					} else {
						if (APPHP_MODE == 'demo') {
							$msg = CDatabase::init()->getErrorMessage();
							if (!$msg) $msg = A::t('core', 'This operation is blocked in Demo Mode!');
							$msgType = 'warning';
						} elseif ($records->getError()) {
							$msg = $records->getErrorMessage();
							$msgType = 'error';
						} else {
							// Prepare messages
							if ($operationType == 'add') {
								$msg = !empty($customMessages['insert']['success']) ? $customMessages['insert']['success'] : 'The error occurred while adding new record! To get more information please turn on debug mode.';
							} else {
								$msg = !empty($customMessages['update']['success']) ? $customMessages['update']['success'] : 'The error occurred while updating this record! To get more information please turn on debug mode.';
							}
							$msgType = 'error';
						}
					}
				}
			}
			
			// Remove uploaded images if error is detected
			if ($msgType == 'error' || $msgType == 'validation') {
				$uploadedFiles = (array)$result['uploadedFiles'];
				foreach ($uploadedFiles as $file) {
					CFile::deleteFile($file);
				}
			}
			
			if (!empty($msg)) {
				A::app()->getSession()->setFlash('alertType', $msgType);
				A::app()->getSession()->setFlash('alert', $msg);
				// Create flash alert
				if ($alertType == 'flash') {
					$msgType = A::app()->getSession()->getFlash('alertType');
					$msg = A::app()->getSession()->getFlash('alert');
				}
				$output .= CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
			}
		}
		
		// -----------------------------------------------------------
		// DRAW FORM ON THE SCREEN
		// -----------------------------------------------------------
		if ($operationType == 'edit' && empty($records)) {
			$output .= CWidget::create('CMessage', array('error', 'Could not complete the operation! One or more parameter values are not valid.'));
		} else {
			$formViewParams = array();
			$formViewParams['action'] = $action;
			$formViewParams['method'] = $method;
			$formViewParams['htmlOptions'] = $htmlOptions;
			$formViewParams['requiredFieldsAlert'] = $requiredFieldsAlert;
			$formViewParams['fieldSetSets'] = $fieldSets;
			$formViewParams['fieldWrapper'] = array('tag' => $fieldWrapperTag, 'class' => $fieldWrapperClass);
			
			$formViewParams['fields'] = array();
			$formViewParams['fields']['APPHP_FORM_ACT'] = array('type' => 'hidden', 'value' => 'send');
			
			// Remove disabled fields
			foreach ($fields as $field => $fieldInfo) {
				if (preg_match('/separator/i', $field) && is_array($fieldInfo)) {
					if (self::keyAt('separatorInfo.disabled', $fieldInfo, '') == true) {
						// Unset disabled section
						unset($fields[$field]);
					} else {
						foreach ($fieldInfo as $iField => $iFieldInfo) {
							if (self::keyAt('disabled', $iFieldInfo, false) === true) unset($fields[$field][$iField]);
						}
					}
				} else {
					if (self::keyAt('disabled', $fieldInfo, false) === true) unset($fields[$field]);
				}
			}
			
			// Draw fields
			foreach ($fields as $field => $fieldInfo) {
				if (preg_match('/separator/i', $field) && is_array($fieldInfo)) {
					// [27.03.2015] - removed - because doesn't draw LEGEND tag
					//if(isset($fieldInfo['separatorInfo'])){
					//	unset($fieldInfo['separatorInfo']);
					//}
					foreach ($fieldInfo as $iField => $iFieldInfo) {
						self::_prepareFieldInfo($cRequest, $operationType, $formViewParams, $iFieldInfo, $recordsAssoc, $field, $iField);
					}
				} else {
					self::_prepareFieldInfo($cRequest, $operationType, $formViewParams, $fieldInfo, $recordsAssoc, $field, '');
				}
			}
			
			// Draw translation fields
			if (count($translationFields) > 0) {
				$translationFieldsArray = array();
				foreach ($translationFields as $transFieldKey => $transFieldVal) {
					$translationFieldsArray[] = $transFieldKey;
				}
				$translationsArray = $objModel->getTranslations(array('key' => $keyTo, 'value' => self::keyAt($keyFrom, $recordsAssoc, ''), 'fields' => $translationFieldsArray));
				
				foreach ($languages as $lang) {
					$flagIcon = ($lang['icon'] != '') ? $lang['icon'] : 'no_image.png';
					$formViewParams['fields']['separator_' . $lang['code']] = array(
						'separatorInfo' => array('legend' => '<img width="16px" src="images/flags/' . $flagIcon . '" alt="' . $lang['code'] . '"> &nbsp;' . $lang['name_native']),
					);
					
					foreach ($translationFields as $transFieldKey => $transFieldVal) {
						$tfTitle = self::keyAt('title', $transFieldVal, '');
						$tfType = self::keyAt('type', $transFieldVal, '');
						$tfTooltip = self::keyAt('tooltip', $transFieldVal, '');
						$tfValidation = (array)self::keyAt('validation', $transFieldVal, array());
						$tfRequired = (bool)self::keyAt('validation.required', $transFieldVal, false);
						$tfHtmlOptions = self::keyAt('htmlOptions', $transFieldVal, array(), 'is_array');
						$tfViewType = self::keyAt('viewType', $transFieldVal, '');
						// Retrieve translation field value from POST or from database
						if (in_array($cRequest->getPost('APPHP_FORM_ACT'), self::$_allowedActs) && $cRequest->isPostExists($transFieldKey . '_' . $lang['code'])) {
							$tfValue = $cRequest->getPost($transFieldKey . '_' . $lang['code']);
						} else {
							if ($operationType == 'add') {
								$tfValue = self::keyAt('default', $transFieldVal, '');
							} else {
								$tfValue = isset($translationsArray[$lang['code']][$transFieldKey]) ? $translationsArray[$lang['code']][$transFieldKey] : '';
							}
						}
						
						// Add validation attributes
						self::_setValidationAttributes($tfHtmlOptions, $tfValidation, $tfTitle, $tfViewType);
						
						$formViewParams['fields']['separator_' . $lang['code']][$transFieldKey . '_' . $lang['code']] = array(
							'type' => $tfType,
							'tooltip' => $tfTooltip,
							'value' => $tfValue,
							'title' => $tfTitle,
							'mandatoryStar' => $tfRequired,
							'htmlOptions' => $tfHtmlOptions,
						);
					}
				}
			}
			
			$formViewParams['events'] = array('focus' => array('field' => $errorField));
			$formViewParams['buttons'] = $buttons;
			$formViewParams['buttonsPosition'] = $buttonsPosition;
			$output .= CWidget::create('CFormView', $formViewParams);
		}
		
		if ($return) return $output;
		else echo $output;
	}
	
	/**
	 * Prepares field info for CFormView
	 * @param object $cRequest
	 * @param string $operationType
	 * @param array &$formViewParams
	 * @param array &$fieldInfo
	 * @param array &$recordsAssoc
	 * @param string $field
	 * @param string $iField
	 */
	private static function _prepareFieldInfo($cRequest, $operationType, &$formViewParams, &$fieldInfo, &$recordsAssoc, $field, $iField = '')
	{
		$fieldInd = (!empty($iField)) ? $iField : $field;
		$title = self::keyAt('title', $fieldInfo, '');
		$validationRules = (array)self::keyAt('validation', $fieldInfo, array());
		$mandatoryStar = (bool)self::keyAt('validation.required', $fieldInfo, false);
		$validationType = self::keyAt('validation.type', $fieldInfo, '');
		$validationFormat = self::keyAt('validation.format', $fieldInfo, '');
		$maxSize = self::keyAt('validation.maxSize', $fieldInfo, '');
		$viewType = self::keyAt('viewType', $fieldInfo, '');
		$fieldType = strtolower(self::keyAt('type', $fieldInfo, ''));
		self::unsetKey('validation', $fieldInfo);
		
		// Retrieve field value from POST or from database
		if (in_array($cRequest->getPost('APPHP_FORM_ACT'), self::$_allowedActs)) {
			if (preg_match('/imageupload|fileupload|label/i', $fieldType)) {
				$fieldInfo['value'] = self::keyAt($fieldInd, $recordsAssoc, '');
			} elseif (self::issetKey('htmlOptions.disabled', $fieldInfo)) {
				$fieldInfo['value'] = self::keyAt($fieldInd, $recordsAssoc, '');
			} elseif ($fieldType == 'hidden') {
				$fieldInfo['value'] = self::keyAt('default', $fieldInfo, '');
			} else {
				$fieldInfo['value'] = $cRequest->getPost($fieldInd);
			}
			
			// Clear password field value after form submission
			if ($fieldType == 'password') {
				$fieldInfo['value'] = '';
			}
		} else {
			if ($operationType == 'add') {
				$fieldInfo['value'] = self::keyAt('default', $fieldInfo, '');
			} else {
				// Edit
				$fieldValue = self::keyAt($fieldInd, $recordsAssoc, '');
				if ($fieldType == 'password') {
					$fieldInfo['value'] = '';
				} elseif ($validationType == 'float' && $validationFormat == 'european') {
					$fieldInfo['value'] = CNumber::europeanFormat(self::keyAt($fieldInd, $recordsAssoc), '');
				} elseif (empty($fieldValue) && self::issetKey('defaultEditMode', $fieldInfo)) {
					$fieldInfo['value'] = self::keyAt('defaultEditMode', $fieldInfo, '');
				} elseif (in_array($fieldType, array('label', 'html'))) {
					// Call of closure function on item creating event
					$callbackFunction = self::keyAt('callback.function', $fieldInfo);
					$callbackParams = self::keyAt('callback.params', $fieldInfo, array());
					if (!empty($callbackFunction)) {
						if (is_callable($callbackFunction)) {
							// Call a function
							$fieldInfo['value'] = $callbackFunction($fieldValue, $callbackParams);
							/// DEPRECATED 01.12.2018 - for PHP_VERSION | phpversion() < 5.3.0
							/// $fieldInfo['value'] = call_user_func($callbackFunction, $fieldValue, $callbackParams);
						}
					} else {
						$fieldInfo['value'] = $fieldValue;
					}
				} elseif ($fieldType == 'textbox') {
					$fieldEncryption = (bool)self::keyAt('encryption.enabled', $fieldInfo, false);
					$fieldEncryptionLevel = (bool)self::keyAt('encryption.encryptionLevel', $fieldInfo, '');
					if ($fieldEncryption) {
						$encryptAlgorithm = self::keyAt('encryption.encryptAlgorithm', $fieldInfo, '');
						$encryptKey = self::keyAt('encryption.encryptKey', $fieldInfo, '');
						if (empty($fieldValue)) {
							$fieldValue = '';
						} else {
							$fieldValue = CHash::decrypt($fieldValue, $encryptKey, $encryptAlgorithm);
						}
					}
					$fieldInfo['value'] = $fieldValue;
				} else {
					$fieldInfo['value'] = $fieldValue;
				}
			}
		}
		
		if ($fieldType == 'videolink') {
			$formViewParams['preview'] = self::keyAt('preview', $fieldInfo, '');
		} elseif (preg_match('/imageupload|fileupload/i', $fieldType)) {
			// Save to show on CFormView field label
			$fieldInfo['maxSize'] = $maxSize;
		}
		
		$fieldInfo['mandatoryStar'] = $mandatoryStar;
		if (in_array($fieldType, array('checkbox', 'radio', 'radiobutton'))) {
			$fieldInfo['checked'] = ($fieldInfo['value']) ? true : false;
		}
		
		// Add validation attributes
		self::_setValidationAttributes($fieldInfo['htmlOptions'], $validationRules, $title, $viewType);
		
		if (!empty($iField)) $formViewParams['fields'][$field][$iField] = $fieldInfo;
		else $formViewParams['fields'][$field] = $fieldInfo;
	}
	
	/**
	 * Prepare additional parameters that will be passed
	 * @param bool $allow
	 * @param int $linkType
	 * @param char $symbol
	 * @return string
	 */
	private static function _additionalParams($allow = false, $linkType = 0, $separateSymbol = '&')
	{
		$output = '';
		
		if ($allow) {
			$page = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$sortBy = A::app()->getRequest()->getQuery('sort_by', 'string');
			$sortDir = A::app()->getRequest()->getQuery('sort_dir', 'string');
			
			if ($sortBy) {
				$output .= ($linkType) ? '/sort_by/' . $sortBy : (!empty($output) ? '&' : $separateSymbol) . 'sort_by=' . $sortBy;
			}
			if ($sortDir) {
				$output .= ($linkType) ? '/sort_dir/' . $sortDir : (!empty($output) ? '&' : $separateSymbol) . 'sort_dir=' . $sortDir;
			}
			if ($page) {
				$output .= ($linkType) ? '/page/' . $page : (!empty($output) ? '&' : $separateSymbol) . 'page=' . $page;
			}
		}
		
		return $output;
	}
	
	/**
	 * Set validation parameters to field for client validation
	 * @parm array &$htmlOptions,
	 * @parm array $validationRules
	 * @parm string $title
	 * @parm string $viewType
	 * @return void
	 */
	private static function _setValidationAttributes(&$htmlOptions, $validationRules = array(), $title = '', $viewType = '')
	{
		// Add validation attributes
		if (self::$_clientValidation) {
			
			///CDebug::d($validationRules);
			$required = !empty($validationRules['required']) ? $validationRules['required'] : false;
			$type = !empty($validationRules['type']) ? strtolower($validationRules['type']) : '';
			$validationFormat = !empty($validationRules['format']) ? $validationRules['format'] : 'american';
			$confirmField = !empty($validationRules['confirmField']) ? $validationRules['confirmField'] : '';
			$minValue = !empty($validationRules['minValue']) ? $validationRules['minValue'] : '';
			$maxValue = !empty($validationRules['maxValue']) ? $validationRules['maxValue'] : '';
			$minLength = !empty($validationRules['minLength']) ? $validationRules['minLength'] : '';
			$maxLength = !empty($validationRules['maxLength']) ? $validationRules['maxLength'] : '';
			$pattern = !empty($validationRules['pattern']) ? $validationRules['pattern'] : '';
			$viewType = strtolower($viewType);
			
			if ($required) {
				$htmlOptions['data-validation'] = 'required';
			} else {
				$htmlOptions['data-validation-optional'] = 'true';
			}
			
			if (!self::$_clientValidationOnlyRequired) {
				$title = '';
				switch ($type) {
					case 'alpha':
					case 'alphabetic':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid alphabetic value! Please re-enter.', array('{title}' => $title));
						break;
					case 'alphanumeric':
						$htmlOptions['data-validation'] = 'alphanumeric';
						///$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid alpha-numeric value! Please re-enter.', array('{title}'=>$title));
						break;
					case 'alphadash':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z\-]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid alphabetic value or dash! Please re-enter.', array('{title}' => $title));
						break;
					case 'numeric':
						$htmlOptions['data-validation-regexp'] = '^([0-9]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid numeric value! Please re-enter.', array('{title}' => $title));
						break;
					case 'variable':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z]+[0-9a-zA-Z_]*)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid label name (alphanumeric, starts with letter and can contain an underscore)! Please re-enter.', array('{title}' => $title));
						break;
					case 'mixed':
					case 'alphanumericspaces':
						$htmlOptions['data-validation'] = 'alphanumeric';
						$htmlOptions['data-validation-allowing'] = ' ';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} should include only alpha, space and numeric characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'seolink':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z0-9\-_]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} should include only alpha, hyphen, underscore and numeric characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'phone':
						$htmlOptions['data-validation-regexp'] = '^([+]{0,1}[\d]{3,12}[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,8})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid phone number! Please re-enter.', array('{title}' => $title));
						break;
					case 'phonestring':
						$htmlOptions['data-validation-regexp'] = '^([+]?([\d]{0,3})?[-| ]{0,1}[\(\.\-\s]?([\d]{0,3})[\)\.\-\s]?[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,6}[-| ]{0,1}[\d]{0,8})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid phone number! Please re-enter.', array('{title}' => $title));
						break;
					case 'username':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z0-9_\-]{6,32})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must have a valid username value! Please re-enter.', array('{title}' => $title));
						break;
					case 'timezone':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z\/]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} should include only alpha and slashes characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'zipcode':
						$htmlOptions['data-validation-regexp'] = '^([0-9a-zA-Z\-\s]*)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid zip/post code! Please re-enter.', array('{title}' => $title));
						break;
					case 'password':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z0-9_\.\,\-!@#$%^&*()]{6,25})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must have a valid password value! Please re-enter.', array('{title}' => $title));
						break;
					case 'email':
						$htmlOptions['data-validation'] = 'email';
						break;
					case 'filename':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z0-9_\-\.]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid file name! Please re-enter.', array('{title}' => $title));
						break;
					case 'identity':
					case 'identitycode':
						$htmlOptions['data-validation-regexp'] = '^([a-zA-Z0-9_\-]+)$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid identity code! Please re-enter.', array('{title}' => $title));
						break;
					case 'date':
					case 'datetime':
						if ($viewType == 'date') {
							$htmlOptions['data-validation'] = 'date';
							$htmlOptions['data-validation-format'] = 'yyyy-mm-dd';
							$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid date value! Please re-enter.', array('{title}' => $title));
						}
//						// requires module time
//						}elseif($viewType == 'time') {
//							$htmlOptions['data-validation'] = 'time';
//							$htmlOptions['data-validation-format'] = 'HH:mm:ss';
//							$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid time value! Please re-enter.', array('{title}'=>$title));
						break;
					case 'int':
					case 'integer':
						$htmlOptions['data-validation'] = 'number';
						$htmlOptions['data-validation-allowing'] = 'negative';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid integer value! Please re-enter.', array('{title}' => $title));
						break;
					case 'positiveinteger':
						$htmlOptions['data-validation'] = 'number';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid positive integer value! Please re-enter.', array('{title}' => $title));
						break;
					case 'percent':
						$htmlOptions['data-validation'] = 'number';
						$htmlOptions['data-validation-allowing'] = 'range[0;100],float';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The value of field {title} must be between {min} and {max}! Please re-enter.', array('{title}' => $title, '{min}' => '0', '{max}' => '100'));
						break;
					case 'htmlsize':
						$htmlOptions['data-validation-regexp'] = '^([0-9]{1,4}[\.]{0,1}[0-9]{0,1}(px|em|pt|%){0,1})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid HTML element size value (ex.: 100px, pt, em or %)! Please re-enter.', array('{title}' => $title));
						break;
					case 'float':
						$format_sample = ($validationFormat == 'european') ? '1234,00' : '1234.00';
						$htmlOptions['data-validation'] = 'number';
						$htmlOptions['data-validation-allowing'] = 'float,negative';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid float value in format: {format}! Please re-enter.', array('{title}' => $title, '{format}' => $format_sample));
						break;
					case 'url':
						//$htmlOptions['data-validation-regexp'] = '^(http:\/\/|https:\/\/|ftp:\/\/)$';
						$htmlOptions['data-validation'] = 'url';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid URL string value! Please re-enter.', array('{title}' => $title));
						break;
					case 'ip':
						$htmlOptions['data-validation-regexp'] = '^([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid IP address! Please re-enter.', array('{title}' => $title));
						break;
					case 'hexcolor':
						$htmlOptions['data-validation-regexp'] = '^#([a-f0-9]{6}|[a-f0-9]{3})$';
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} must be a valid a hexadecimal color value, ex.: {sample}! Please re-enter.', array('{title}' => $title, '{sample}' => '#3369FE'));
						break;
					case 'regex':
						$htmlOptions['data-validation-regexp'] = $pattern;
						$htmlOptions['data-validation-error-msg'] = A::t('core', 'The field {title} has incorrect value! It must match pattern {pattern}. Please re-enter.', array('{title}' => $title, '{pattern}' => $pattern));
						break;
					case 'range':
						if (!empty($minValue) && !empty($maxValue)) {
							$htmlOptions['data-validation'] = 'number';
							$htmlOptions['data-validation-allowing'] = 'range[' . (int)$minValue . ';' . (int)$maxValue . ']';
							$htmlOptions['data-validation-error-msg'] = A::t('core', 'The value of field {title} must be between {min} and {max}! Please re-enter.', array('{title}' => $title, '{min}' => $minValue, '{max}' => $maxValue));
						}
						break;
					//if(!empty($minLength) || !empty($maxLength)){
					//	$htmlOptions['data-validation'] = 'length';
					//	if(!empty($minLength) && empty($maxLength)){
					//		$htmlOptions['data-validation-length'] = 'min'.(int)$minLength;
					//	}elseif(empty($minLength) && !!empty($maxLength)){
					//		$htmlOptions['data-validation-length'] = 'min'.(int)$maxLength;
					//	}else{
					//		$htmlOptions['data-validation-length'] = (int)$minLength.'-'.(int)$maxLength;
					//	}
					//}
					case 'set':
						if ($viewType == 'checkboxes') {
							$htmlOptions['data-validation'] = 'checkbox_group';
							$htmlOptions['data-validation-qty'] = 'min1';
						}
						break;
					//case 'confirm':
					//	$htmlOptions['data-validation'] = 'confirmation';
					//	$htmlOptions['data-validation-confirm'] = $confirmField;
					//	$htmlOptions['data-validation-error-msg'] = A::t('core', 'The {confirm_field} and {title} fields do not match! Please re-enter.', array('{confirm_field}'=>CString::humanize($confirmField), '{title}'=>$title));
					//	break;
					case 'captcha':
					case 'text':
					case 'any':
					default:
						break;
				}
				
				if (isset($htmlOptions['data-validation-regexp'])) {
					$htmlOptions['data-validation'] = 'custom';
				}
			}
		}
	}
}
