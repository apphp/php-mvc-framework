<?php
/**
 * CDataForm widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init                                                 prepareFieldInfo
 *                                                      additionalParams
 * 
 */	  

class CDataForm
{
    const NL = "\n";
    /** @var string */
    public static $count = 0;
	/** @var array */
    public static $allowedActs = array('send', 'change');
    
    /**
     * Draws HTML form control
     * @param array $params
     * 
     * TODO:
     *   - to prevent double quotes issue use 'encode'=>true in htmlOptions
     *   - insert code (for all fields): 'prependCode=>'', 'appendCode'=>''
     *   
     * Notes:
     *   - for INSERT operations don't define 'primaryKey' option at all
     *   - attribute 'default'=>'' is used for Add mode only
     *   - to disable any field or button use: 'disabled'=>true
     *   - attribute 'validation'=>array('unique'=>true) is used for Add/Edit modes for standard fields (not for translation fields)
     * 	 - validation types: 
     *  	alpha, numeric, alphanumeric, variable, mixed, phone, username, timeZone
     *  	password, email, date, integer, float, any, confirm
     *   
     * Usage: (in view file)
     *  echo CWidget::create('CDataForm', array(
     *       'model'=>'tableName',
     *       'primaryKey'=>1,
     *       'operationType'=>'add|edit',
     *       'action'=>'locations/edit/id/1',     
     *       'successUrl'=>'locations/view/msg/1',
     *       'cancelUrl'=>'locations/view',
     *       'passParameters'=>false,
     *       'method'=>'post',
     *       'htmlOptions'=>array(
     *           'name'=>'frmContact',
     *           'enctype'=>'multipart/form-data',
     *           'autoGenerateId'=>true
     *       ),
     *       'requiredFieldsAlert'=>true,
     *       'fieldSetType'=>'frameset|tabs',
     *       'fields'=>array(
	 *         	 'separatorName' =>array(
	 *               'separatorInfo' => array('legend'=>A::t('app', A::t('app', 'Headers & Footers'))),
	 *               'field_1'=>array('type'=>'textbox', 'title'=>'Field 1', 'tooltip'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'htmlOptions'=>array()),
	 *               ...
	 *           ),
     *           'field_1'=>array('type'=>'textbox',        'title'=>'Username',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'username'), 'htmlOptions'=>array()),
     *           'field_2'=>array('type'=>'password',       'title'=>'Password',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6), 'encryption'=>array('enabled'=>true, 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'hashKey'=>CConfig::get('password.hashKey')), 'htmlOptions'=>array()),
     *           'field_3'=>array('type'=>'textbox',        'title'=>'Confirm P',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_2'), 'htmlOptions'=>array()),
     *           'field_4'=>array('type'=>'textbox',        'title'=>'Email',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'email'), 'htmlOptions'=>array()),
     *           'field_5'=>array('type'=>'textbox',        'title'=>'Confirm E',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_4'), 'htmlOptions'=>array()),
     *           'field_6'=>array('type'=>'textbox',        'title'=>'Mixed',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'mixed'), 'htmlOptions'=>array()),
     *           'field_7'=>array('type'=>'textbox',        'title'=>'Field',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array()),
     *           'field_8'=>array('type'=>'image',          'title'=>'Image',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'image/jpeg, image/png', 'fileName'=>''), 'htmlOptions'=>array()),
     *           'field_9'=>array('type'=>'file',           'title'=>'File',       'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'file', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml', 'fileName'=>''), 'htmlOptions'=>array()),
     *          'field_10'=>array('type'=>'textbox',        'title'=>'Format',     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5)), 'htmlOptions'=>array()),
     *          'field_11'=>array('type'=>'textarea',       'title'=>'Text',       'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>255), 'htmlOptions'=>array('maxLength'=>'250')),
     *          'field_12'=>array('type'=>'checkbox',       'title'=>'Checkbox',   'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'htmlOptions'=>array()),
     *          'field_13'=>array('type'=>'select',         'title'=>'Select',     'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'data'=>array(), 'htmlOptions'=>array()),
     *          'field_14'=>array('type'=>'radioButton',    'title'=>'Radio',      'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'htmlOptions'=>array()),
     *          'field_15'=>array('type'=>'radioButtonList','title'=>'RadioList',  'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>''), 'data'=>array(), 'htmlOptions'=>array()),
     *          'field_16'=>array('type'=>'imageUpload',    'title'=>'ImageUpload','tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'templates/backend/images/accounts/', 'maxSize'=>'100k', 'mimeType'=>'image/jpeg, image/png', 'fileName'=>CHash::getRandomString(10))), 'htmlOptions'=>array(),
	 *          	'imageOptions'  => array('showImage'=>true, 'showImageName'=>true, 'showImageSize'=>true, 'imagePath'=>'templates/backend/images/accounts/', 'imageClass'=>'avatar'),
	 *          	'deleteOptions' => array('showLink'=>true, 'linkPath'=>'admins/edit/avatar/delete', 'linkText'=>'Delete'),
	 *          	'fileOptions'   => array('showAlways'=>false, 'class'=>'file', 'size'=>'25')
     *          ),
     *          'field_17'=>array('type'=>'label',  'default'=>'', 'title'=>'Label 17', 'tooltip'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>''),
     *          'field_18'=>array('type'=>'datetime', 'default'=>'', 'title'=>'Field 18', 'tooltip'=>'', 'definedValues'=>array(), 'htmlOptions'=>array(), 'format'=>''),
     *          'field_19'=>array('type'=>'hidden', 'default'=>'', 'htmlOptions'=>array()),
     *       ),
     *       'translationInfo' => array('relation'=>array('field_from', 'field_to'), 'languages'=>Languages::model()->findAll('is_active = 1')),
     *       'translationFields'=>array(
     *           'fields_1_1' => array('type'=>'textbox', 'title'=>'Field 1-1', 'tooltip'=>'', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any'), 'htmlOptions'=>array()),
     *       ),
     *       'buttons'=>array(
     *          'submit'=>array('type'=>'submit', 'value'=>'Send', 'htmlOptions'=>array('name'=>''))
	 *          'reset' =>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array()),
     *          'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'')),
	 *          'custom' =>array('type'=>'button', 'value'=>'Custom', 'htmlOptions'=>array('onclick'=>"$(location).attr('href','categories/index');")),
     *       ),
     *       'messagesSource'=>'core',
     *       'showAllErrors'=>false,
     *       'return'=>true,
     *  ));
     */
    public static function init($params = array())
    {
        $output = '';
        $model = isset($params['model']) ? $params['model'] : '';
        $primaryKey = isset($params['primaryKey']) ? (int)$params['primaryKey'] : '';
		$operationType = (isset($params['operationType']) && $params['operationType'] == 'edit') ? 'edit' : 'add';
		$action = isset($params['action']) ? $params['action'] : '';
		$successUrl = isset($params['successUrl']) ? $params['successUrl'] : '';
		$cancelUrl = isset($params['cancelUrl']) ? $params['cancelUrl'] : '';
		$method = isset($params['method']) ? $params['method'] : 'post';
        $htmlOptions = (isset($params['htmlOptions']) && is_array($params['htmlOptions'])) ? $params['htmlOptions'] : array();
		$requiredFieldsAlert = isset($params['requiredFieldsAlert']) ? $params['requiredFieldsAlert'] : false;
		$fieldSetType = (isset($params['fieldSetType']) && $params['fieldSetType'] == 'tabs') ? 'tabs' : 'frameset';                
		//$autoGenerateId = isset($htmlOptions['autoGenerateId']) ? (bool)$htmlOptions['autoGenerateId'] : false;
        $formName = isset($htmlOptions['name']) ? $htmlOptions['name'] : '';
        $return = isset($params['return']) ? (bool)$params['return'] : true;

		$fields = isset($params['fields']) ? $params['fields'] : array();
		$translationInfo = isset($params['translationInfo']) ? $params['translationInfo'] : array();
		$languages = isset($translationInfo['languages']) ? $translationInfo['languages'] : array();
		$keyFrom = isset($translationInfo['relation'][0]) ? $translationInfo['relation'][0] : '';
		$keyTo = isset($translationInfo['relation'][1]) ? $translationInfo['relation'][1] : '';
		
		$translationFields = isset($params['translationFields']) ? $params['translationFields'] : array();
		$msgSource = isset($params['messagesSource']) ? $params['messagesSource'] : 'core';
		$showAllErrors = isset($params['showAllErrors']) ? (bool)$params['showAllErrors'] : false;
        $buttons = isset($params['buttons']) ? $params['buttons'] : array();
		if(isset($buttons['cancel']) && !empty($cancelUrl)) $buttons['cancel']['htmlOptions']['onclick'] = '$(location).attr(\'href\',\''.$cancelUrl.'\');';

		$objModel = call_user_func_array($model.'::model', array());
		$tableName = CConfig::get('db.prefix').$objModel->getTableName();
		$records = ($operationType == 'edit') ? $objModel->findByPk($primaryKey) : call_user_func(array($model, 'model')); 
		$recordsAssoc = !empty($records) ? $records->getFieldsAsArray() : array();
		$cRequest = A::app()->getRequest();
		
		$passParameters = isset($params['passParameters']) ? (bool)$params['passParameters'] : false;
		// add additional parameters if allowed
		if($passParameters){
			$additionalParams = self::additionalParams(true);
			$successUrl .= $additionalParams;
			$action .= $additionalParams;
			if(isset($buttons['cancel']) && !empty($cancelUrl)) $buttons['cancel']['htmlOptions']['onclick'] = '$(location).attr(\'href\',\''.$cancelUrl.$additionalParams.'\');';
		} 		

		// -----------------------------------------------------------
		// HANDLE FORM SUBMISSION
		// -----------------------------------------------------------
		if($cRequest->getPost('APPHP_FORM_ACT') == 'send'){
			$msg = $errorField = $errorType = '';			
			
			// prepare fields without framesets
			$fieldsMainTable = array();
			foreach($fields as $field => $fieldInfo){
				if(preg_match('/separator/i', $field) && is_array($fieldInfo)){
					unset($fieldInfo['separatorInfo']);
					foreach($fieldInfo as $iField => $iFieldInfo) $fieldsMainTable[$iField] = $iFieldInfo;
				}else{
					$fieldsMainTable[$field] = $fieldInfo;
				}
			}

			// merge fields from main table with translation fields for validation			
			$transFieldsByLangs = array();
			foreach($translationFields as $transFieldKey => $transFieldVal){
				foreach($languages as $lang){
					$transField = $translationFields[$transFieldKey];
					$transField['title'] .= ' ('.$lang['name_native'].')';
					$transFieldsByLangs[$transFieldKey.'_'.$lang['code']] = $transField;
					//$transFieldsByLangs[$lang['code']][$transFieldKey] = $cRequest->getPost($transFieldKey.'_'.$lang['code']);
				}
			}
			$mergedFieldsForValidation = array_merge($fieldsMainTable, $transFieldsByLangs);
			
			// validate the form with all fields
			$result = CWidget::create('CFormValidation', array('fields'=>$mergedFieldsForValidation, 'messagesSource'=>$msgSource, 'showAllErrors'=>$showAllErrors));
			if($result['error']){
				$msg = $result['errorMessage'];
				$errorField = $result['errorField'];
				$errorType = 'validation';
			}else{				
				// check fields for unique values
				foreach($fieldsMainTable as $field => $fieldInfo){
					if(isset($fieldInfo['validation']['unique']) && (bool)$fieldInfo['validation']['unique']){
						$fieldTitle = isset($fieldInfo['title']) ? $fieldInfo['title'] : '';
						$sqlCount = $tableName.'.'.$field.' = :code'.(($operationType == 'edit') ? ' AND '.$tableName.'.id != :id' : '');
						$sqlParams = ($operationType == 'edit') ? array(':code'=>$cRequest->getPost($field), ':id'=>$primaryKey) : array(':code'=>$cRequest->getPost($field));							
						if($objModel->count($sqlCount, $sqlParams) > 0){
							$errorField = $field;
							$msg = A::t($msgSource, 'The field {title} allows only unique values, please re-enter!', array('{title}'=>'<b>'.$fieldTitle.'</b>'));
							$errorType = 'error';
							break;
						}
					}
				}
				
				if(!$errorType){
					// update fields values (according to definition in CDataForm)
					foreach($fieldsMainTable as $field => $fieldInfo){
						$fieldType = isset($fieldInfo['type']) ? $fieldInfo['type'] : '';
						if($fieldType != 'label'){
							$fieldValue = $cRequest->getPost($field);
							if(isset($fieldInfo['htmlOptions']['disabled'])){
								unset($recordsAssoc[$field]);							
							}else if($fieldType == 'checkbox' && $fieldValue == ''){
								// set default value to spesific fields: checkbox (if it's empty), encrypted fields and image
								$fieldValue = 0;
							}else if($fieldType == 'password'){
								$fieldEncryption = isset($fieldInfo['encryption']['enabled']) ? (bool)$fieldInfo['encryption']['enabled'] : false;
								if($fieldEncryption){
									$encryptAlgorithm = isset($fieldInfo['encryption']['encryptAlgorithm']) ? $fieldInfo['encryption']['encryptAlgorithm'] : '';					
									$hashKey = isset($fieldInfo['encryption']['hashKey']) ? $fieldInfo['encryption']['hashKey'] : '';
									if(empty($fieldValue)){
										unset($recordsAssoc[$field]);
									}else{
										$fieldValue = CHash::create($encryptAlgorithm, $fieldValue, $hashKey);									
									}							
								}
							}else if($fieldType == 'image'){
								unset($recordsAssoc[$field]);
							}else if($fieldType == 'imageupload'){
								if(!empty($_FILES[$field]['name'])){
									$fileName = isset($fieldInfo['validation']['fileName']) ? $fieldInfo['validation']['fileName'] : '';
									if(!empty($fileName)){
										$fieldValue = $fileName.'.'.pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
									}else{
										$fieldValue = $_FILES[$field]['name'];
									}
								}else{
									$fieldValue = '';
									unset($recordsAssoc[$field]);
								}
							}else if($fieldValue == '' && $operationType == 'add'){
								if(isset($fieldInfo['default'])) $fieldValue = $fieldInfo['default'];
							}
							
							// update fields values (according to defined in database table)
							if(isset($recordsAssoc[$field])){
								$records->set($field, $fieldValue);
							}
						}						
					}
					
					// save main table
					if($operationType == 'add') $objModel->clearPkValue();
					if($records->save()){
						// save data into translation table					
						if(count($translationFields) > 0){
							$translationParams = array();
							foreach($translationFields as $transFieldKey => $transFieldVal){
								foreach($languages as $lang){
									$translationParams[$lang['code']][$transFieldKey] = $cRequest->getPost($transFieldKey.'_'.$lang['code']);
								}
							}
							if($cRequest->getPost('APPHP_FORM_ACT') == 'send'){
								$keyFromValue = ($records->primaryKey() == $keyFrom) ? $records->getPrimaryKey() : $records->get($keyFrom);
							}else{
								$keyFromValue = $recordsAssoc[$keyFrom];
							}
							$records->saveTranslations(array('key'=>$keyTo, 'value'=>$keyFromValue, 'fields'=>$translationParams));
						}
						
						$errorType = 'success';
						$msg = A::t($msgSource, ($operationType == 'add') ? 'The adding operation has been successfully completed!' : 'The updating operation has been successfully completed!');						
						
						if(!empty($successUrl)){
							header('location: '.A::app()->getRequest()->getBaseUrl().$successUrl);
							exit;
						}
						
						// get last inserted ID
						if($operationType == 'add') $primaryKey = $objModel->getPrimaryKey();
						// refresh data
						$records = $objModel->findByPk($primaryKey);
						$recordsAssoc = $records->getFieldsAsArray();
					}else{
						$msg = (APPHP_MODE == 'demo') ? CDatabase::init()->getErrorMessage() : A::t($msgSource, ($operationType == 'add') ? 'The error occurred while adding new record! To get more information please turn on debug mode.' : 'The error occurred while updating this record! To get more information please turn on debug mode.');	
						$errorType = 'warning';					
					}					
				}				
			}
			
			// remove uploaded images if error is detected
			if($errorType == 'error' || $errorType == 'validation'){
				$uploadedFiles = (array)$result['uploadedFiles'];
				foreach($uploadedFiles as $file){
					CFile::deleteFile($file);
				}					
			}

			if(!empty($msg)){
				$output .= CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
			}			
		}

		// -----------------------------------------------------------
		// DRAW FORM ON THE SCREEN
		// -----------------------------------------------------------
        if($operationType == 'edit' && empty($records)){
			$output .= CWidget::create('CMessage', array('error', 'Could not complete the operation! One or more parameter values are not valid.'));
        }else{
			$formViewParams = array();
			$formViewParams['action'] = $action;
			$formViewParams['method'] = $method;
			$formViewParams['htmlOptions'] = $htmlOptions;
			$formViewParams['requiredFieldsAlert'] = $requiredFieldsAlert;
			$formViewParams['fieldSetType'] = $fieldSetType;			
			
			$formViewParams['fields'] = array();
			$formViewParams['fields']['APPHP_FORM_ACT'] = array('type'=>'hidden', 'value'=>'send');
	
			// remove disabled fields
			foreach($fields as $field => $fieldInfo){
				if(preg_match('/separator/i', $field) && is_array($fieldInfo)){
					foreach($fieldInfo as $iField => $iFieldInfo){
						if(isset($iFieldInfo['disabled']) && (bool)$iFieldInfo['disabled'] === true) unset($fields[$field][$iField]);
					}
				}else{
					if(isset($fieldInfo['disabled']) && (bool)$fieldInfo['disabled'] === true) unset($fields[$field]);
				}				
			}

			// draw fields
			foreach($fields as $field => $fieldInfo){
				if(preg_match('/separator/i', $field) && is_array($fieldInfo)){
					foreach($fieldInfo as $iField => $iFieldInfo){
						self::prepareFieldInfo($cRequest, $operationType, $formViewParams, $iFieldInfo, $recordsAssoc, $field, $iField);
					}
				}else{
					self::prepareFieldInfo($cRequest, $operationType, $formViewParams, $fieldInfo, $recordsAssoc, $field, '');
				}
			}
			
			// draw translation fields			
			if(count($translationFields) > 0){
				$translationFieldsArray = array();
				foreach($translationFields as $transFieldKey => $transFieldVal){
					$translationFieldsArray[] = $transFieldKey;
				}
				$translationsArray = $objModel->getTranslations(array('key'=>$keyTo, 'value'=>(isset($recordsAssoc[$keyFrom]) ? $recordsAssoc[$keyFrom] : ''), 'fields'=>$translationFieldsArray));
				
				foreach($languages as $lang){
					$formViewParams['fields']['separator_'.$lang['code']] = array(
						'separatorInfo' => array('legend'=>'<img width="16px" src="images/flags/'.$lang['icon'].'"> &nbsp;'.$lang['name_native']),
					);			
					foreach($translationFields as $transFieldKey => $transFieldVal){
						$tfTitle = isset($transFieldVal['title']) ? $transFieldVal['title'] : '';
						$tfType = isset($transFieldVal['type']) ? $transFieldVal['type'] : '';
						$tfTooltip = isset($transFieldVal['tooltip']) ? $transFieldVal['tooltip'] : '';
						$tfRequired = isset($transFieldVal['validation']['required']) ? $transFieldVal['validation']['required'] : false;
						$tfHtmlOptions = (isset($transFieldVal['htmlOptions']) && is_array($transFieldVal['htmlOptions'])) ? $transFieldVal['htmlOptions'] : array();
						// retrive translation field value from POST or from database
						if(in_array($cRequest->getPost('APPHP_FORM_ACT'), self::$allowedActs) && $cRequest->isPostExists($transFieldKey.'_'.$lang['code'])){
							$tfValue = $cRequest->getPost($transFieldKey.'_'.$lang['code']);
						}else{
							if($operationType == 'add'){
								$tfValue = isset($transFieldVal['default']) ? $transFieldVal['default'] : '';
							}else{
								$tfValue = $translationsArray[$lang['code']][$transFieldKey];
							}							
						}
						$formViewParams['fields']['separator_'.$lang['code']][$transFieldKey.'_'.$lang['code']] = array(
							'type'=>$tfType,
							'tooltip'=>$tfTooltip,
							'value'=>$tfValue,
							'title'=>$tfTitle,
							'mandatoryStar'=>$tfRequired,
							'htmlOptions'=>$tfHtmlOptions
						);
					}
				}			
			}
			
            $formViewParams['events'] = array('focus'=>array('field'=>$errorField));
			$formViewParams['buttons'] = $buttons;			
			$output .= CWidget::create('CFormView', $formViewParams);			
		}			

		if($return) return $output;
        else echo $output; 
    }
	
	/**
	 * Prepares field info for CFormView
	 * @param object $cRequest
	 * @param array &$formViewParams
	 * @param array &$fieldInfo
	 * @param string $field
	 * @param string $iField
	 */
	private function prepareFieldInfo($cRequest, $operationType, &$formViewParams, &$fieldInfo, &$recordsAssoc, $field, $iField = '')
	{
		$fieldInd = (!empty($iField)) ? $iField : $field;
		$mandatoryStar = isset($fieldInfo['validation']['required']) ? (bool)$fieldInfo['validation']['required'] : false;
		if(isset($fieldInfo['validation'])) unset($fieldInfo['validation']);
		// retrive field value from POST or from database
		if(in_array($cRequest->getPost('APPHP_FORM_ACT'), self::$allowedActs)){
			if(preg_match('/imageupload|label/i', $fieldInfo['type'])){
				$fieldInfo['value'] = isset($recordsAssoc[$fieldInd]) ? $recordsAssoc[$fieldInd] : '';
			}else if(isset($fieldInfo['htmlOptions']['disabled'])){
				$fieldInfo['value'] = isset($recordsAssoc[$fieldInd]) ? $recordsAssoc[$fieldInd] : '';
			}else if($fieldInfo['type'] == 'hidden'){
				$fieldInfo['value'] = isset($fieldInfo['default']) ? $fieldInfo['default'] : '';
			}else{
				$fieldInfo['value'] = $cRequest->getPost($fieldInd);
			}
		}else{
			if($operationType == 'add'){
				$fieldInfo['value'] = isset($fieldInfo['default']) ? $fieldInfo['default'] : '';
			}else{
				// edit
				if($fieldInfo['type'] == 'password'){
					$fieldInfo['value'] = '';
				}else{
					$fieldInfo['value'] = isset($recordsAssoc[$fieldInd]) ? $recordsAssoc[$fieldInd] : '';		
				}								
			}
		}
		$fieldInfo['mandatoryStar'] = $mandatoryStar;				
		if(in_array($fieldInfo['type'], array('checkbox', 'radio', 'radiobutton'))){
			$fieldInfo['checked'] = ($fieldInfo['value']) ? true : false;
		}
		if(!empty($iField)) $formViewParams['fields'][$field][$iField] = $fieldInfo;
		else $formViewParams['fields'][$field] = $fieldInfo;
	}
 
    /**
	 * Prepare additional parameters that will be passed
	 * @param bool $allow
     */
	private static function additionalParams($allow = false)
    {
		$output = '';
		if($allow){
			$page = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$sortBy = A::app()->getRequest()->getQuery('sort_by', 'string');
			$sortDir = A::app()->getRequest()->getQuery('sort_dir', 'string');				
			$output .= ($sortBy) ? '/sort_by/'.$sortBy : '';
			$output .= ($sortDir) ? '/sort_dir/'.$sortDir : '';
			$output .= ($page) ? '/page/'.$page : '';
		}
		return $output;
	}
}