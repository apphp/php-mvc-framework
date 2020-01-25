<?php
/**
 * CFormValidation widget helper class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init                                               	_validateMaxLength
 * 
 */	  

class CFormValidation extends CWidgs
{
	
    const NL = "\n";
	
	/** @var string */
	private static $_errorMessage = '';
	private static $_output = array('error'=>false, 'uploadedFiles'=>array());		

    /**
     * Performs form validation
     * @param array $params
     * 
     * Usage: (in Controller class)
     * - possible validation types:
     *  	alpha, alphabetic, alphadash, alphanumeric, numeric, variable, mixed(alphanumericspaces), seoLink, phone, phoneString, username, timeZone, zipCode,
     *  	password, email, fileName, identity|identityCode, date, integer|int, positiveInteger|positiveInt, percent, htmlSize,
     *  	float, any, confirm, url, ip, range ('minValue'=>'' and 'maxValue'=>''), length ('minLength'=>'' and 'maxLength'=>''),
     *  	simplePassword, set, text, hexColor, captcha, regex
     * - attribute 'forbiddenChars' - 'validation'=>array(..., 'forbiddenChars'=>array('+', '$')) is used to define forbidden characters
	 * - attribute 'allowedChars' - 'validation'=>array(..., 'allowedChars'=>array('+', '$')) is used to define allowed characters (for alpha, alphanumeric or alphadash)
     * - attribute 'trim' - 'validation'=>array(..., 'trim'=>true) - removes spaces from field value before validation
     * - validated field in $_POST may also be an array
     * 
     *  HTML example:
     *  1. <input type="file" name="damage_type_1" value="" />
     *     <input type="file" name="damage_type_2" value="" />
     *     ...
     * 		$result = CWidget::create('CFormValidation', array(
     * 			'fields'=>array(
     * 				'damage_type_1'  	=> array('title'=>A::t('autoportal', 'Damage Type'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(0,1,2,3))),
     * 				'damage_type_2'  	=> array('title'=>A::t('autoportal', 'Damage Type'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(0,1,2,3))),
     *     		),
     *   	));
     *     
     *  2. <input type="file" name="damage_type[1]" value="" />
     *     <input type="file" name="damage_type[2]" value="" />
     *     ...
     * 		$result = CWidget::create('CFormValidation', array(
     * 			'fields'=>array(
     * 				'damage_type'  	=> array('title'=>A::t('autoportal', 'Damage Type'), 'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(0,1,2,3))),
     *     		),
     *   	));
     *
     *  3. <input type="file" name="listing_image[]" value="" />
     *     ...
     *      $fieldsImages = array();
     *		for($i = 1; $i <= 10; $i++){
     *			$fieldsImages['listing_image'][] = array('title'=>A::t('autoportal', 'Image').' #'.$i, 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'assets/modules/gallery/images/items/', 'maxSize'=>'990k', 'fileName'=>'l'.$listingId.'_'.CHash::getRandomString(10), 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif'));
     *		}
     * 		$result = CWidget::create('CFormValidation', array('fields'=>$fieldsImages));
     *  
     * 
     * $result = CWidget::create('CFormValidation', array(
     *     'fields'=>array(
     *         'field_1'=>array('title'=>'Username',        'validation'=>array('required'=>true, 'type'=>'username')),
     *         'field_2'=>array('title'=>'Password',        'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6, 'maxLength'=>25, 'simplePassword'=>false)),
     *         'field_3'=>array('title'=>'Repeat Password', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_2')),
     *         'field_4'=>array('title'=>'Email',           'validation'=>array('required'=>true, 'type'=>'email')),
     *         'field_5'=>array('title'=>'Confirm Email',   'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_4')),
     *         'field_6'=>array('title'=>'Mixed',           'validation'=>array('required'=>true, 'type'=>'mixed')),
     *         'field_7'=>array('title'=>'Field',           'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255)),
     *         'field_8'=>array('title'=>'Image',           'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'maxWidth'=>'120px', 'maxHeight'=>'90px', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'')),
     *         'field_9'=>array('title'=>'File',            'validation'=>array('required'=>true, 'type'=>'file', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml', 'fileName'=>'')),
     *        'field_10'=>array('title'=>'Price',           'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'', 'maxValue'=>'', 'format'=>'american|european'),
     *        'field_11'=>array('title'=>'Format',          'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5))),
     *        'field_12'=>array('title'=>'Captcha', 		'validation'=>array('required'=>true, 'type'=>'captcha')),
	 *        'field_13'=>array('title'=>'Regex', 			'validation'=>array('required'=>true, 'type'=>'regex', 'pattern'=>'^[a-zA-Z\-]+$')),
     *     ),
     *     'multiArray'		=> false,
     *     'messagesSource'	=> 'core',
     *     'showAllErrors'	=> false,
     *     'method'			=> 'POST',
     * ));
     *
     *   
     * if($result['error']){
     *     $msg = $result['errorMessage'];
     *     $this->_view->errorField = $result['errorField'];
     *     $msgType = 'validation';                
     * }else{
     *     // your code here to handle a successful submission...
     * }
     */
	public static function init($params = array())
	{
		parent::init($params);
		
		$fields = self::params('fields', array());
		$isMultiArray = (bool)self::params('multiArray', false);
		$ind = 0;
		
		if ($isMultiArray) {
			foreach ($fields as $field => $fieldInfo) {
				$fieldInfos = $fieldInfo;
				foreach ($fieldInfos as $key => $fieldInfo) {
					$result = self::_handleField($field, $fieldInfo, $isMultiArray, $ind++);
					if ($result == 'break') {
						break;
					}
				}
			}
		} else {
			foreach ($fields as $field => $fieldInfo) {
				$result = self::_handleField($field, $fieldInfo);
				if ($result == 'break') {
					break;
				}
			}
		}
		
		return self::$_output;
	}
	
	/**
	 *
	 */
	private static function _handleField($field, $fieldInfo, $isMultiArray = false, $ind = 0)
	{
		$cRequest = A::app()->getRequest();
		
		$fields = self::params('fields', array());
		$msgSource = self::params('messagesSource', 'core');
		$showAllErrors = self::params('showAllErrors', false);
		$requestMethod = strtolower(self::params('method', 'post')) == 'get' ? 'getQuery' : 'getPost';
		
		$title = self::keyAt('title', $fieldInfo, '');
		$required = self::keyAt('validation.required', $fieldInfo, false);
		$type = self::keyAt('validation.type', $fieldInfo, 'any');
		$viewType = self::keyAt('validation.viewType', $fieldInfo, '');
		$forbiddenChars = self::keyAt('validation.forbiddenChars', $fieldInfo, array());
		$allowedChars = self::keyAt('validation.allowedChars', $fieldInfo, array());
		$minLength = self::keyAt('validation.minLength', $fieldInfo, '');
		$maxLength = self::keyAt('validation.maxLength', $fieldInfo, '');
		$minValue = self::keyAt('validation.minValue', $fieldInfo, '');
		$maxValue = self::keyAt('validation.maxValue', $fieldInfo, '');
		$countryCode = self::keyAt('validation.countryCode', $fieldInfo, '');
		$simplePassword = self::keyAt('validation.simplePassword', $fieldInfo, true);
		$maxSize = self::keyAt('validation.maxSize', $fieldInfo, '');
		if (!empty($maxSize)) $maxSize = CHtml::convertFileSize($maxSize);
		$maxWidth = self::keyAt('validation.maxWidth', $fieldInfo, '');
		if (!empty($maxWidth)) $maxWidth = CHtml::convertImageDimensions($maxWidth);
		$maxHeight = self::keyAt('validation.maxHeight', $fieldInfo, '');
		if (!empty($maxHeight)) $maxHeight = CHtml::convertImageDimensions($maxHeight);
		
		$targetPath = self::keyAt('validation.targetPath', $fieldInfo, '');
		$fileMimeType = self::keyAt('validation.mimeType', $fieldInfo, '');
		$fileMimeTypes = (!empty($fileMimeType)) ? explode(',', str_replace(' ', '', $fileMimeType)) : array();
		$fileDefinedName = self::keyAt('validation.fileName', $fieldInfo, '');
		$trim = (bool)self::keyAt('validation.trim', $fieldInfo, false);
		$format = self::keyAt('validation.format', $fieldInfo, '');
		///$fieldValue     = @call_user_func_array(array($cRequest, $requestMethod), array($field));
		///$fieldValue     = $trim ? trim($fieldValue) : $fieldValue;
		$fieldValue = ($trim) ? trim($cRequest->$requestMethod($field)) : $cRequest->$requestMethod($field);
		$errorMessage = '';
		$valid = true;
		
		if ($type == 'file' || $type == 'image') {
			if ($isMultiArray) {
				$fileName = isset($_FILES[$field]['name'][$ind]) ? $_FILES[$field]['name'][$ind] : '';
				$fileSize = isset($_FILES[$field]['size'][$ind]) ? $_FILES[$field]['size'][$ind] : 0;
				$fileTempName = isset($_FILES[$field]['tmp_name'][$ind]) ? $_FILES[$field]['tmp_name'][$ind] : null;
				$fileError = isset($_FILES[$field]['error'][$ind]) ? $_FILES[$field]['error'][$ind] : '';
				$fileType = isset($_FILES[$field]['type'][$ind]) ? $_FILES[$field]['type'][$ind] : '';
			} else {
				$fileName = isset($_FILES[$field]['name']) ? $_FILES[$field]['name'] : '';
				$fileSize = isset($_FILES[$field]['size']) ? $_FILES[$field]['size'] : 0;
				$fileTempName = isset($_FILES[$field]['tmp_name']) ? $_FILES[$field]['tmp_name'] : '';
				$fileError = isset($_FILES[$field]['error']) ? $_FILES[$field]['error'] : '';
				$fileType = isset($_FILES[$field]['type']) ? $_FILES[$field]['type'] : '';
			}
			$fileWidth = '';
			$fileHeight = '';
			if ($type == 'image') {
				if ($required && !isset($_FILES[$field]['tmp_name'])) {
					$required = false;
				} else {
					// Check file type by file extension
					if (($fileTypeByExt = CFile::getMimeTypeByExtension($fileName)) !== '') {
						$fileType = $fileTypeByExt;
						// Check file type by function IMAGE_TYPE_TO_MIME_TYPE
					} elseif (function_exists('image_type_to_mime_type') && function_exists('exif_imagetype')) {
						$fileType = !empty($fileTempName) && is_file($fileTempName) ? image_type_to_mime_type(exif_imagetype($fileTempName)) : '';
					} else {
						if (strrpos($fileTempName, '.') > 0) {
							$fileType = substr($fileTempName, strrpos($fileTempName, '.') + 1);
						} else {
							$fileType = 'allowed';
							$fileMimeTypes[] = 'allowed';
						}
						CDebug::addMessage('warnings', 'fileUploadingImageType', A::t($msgSource, 'Check if this function exists and usable: {function}', array('{function}', 'exif_imagetype')));
					}
					$fileWidth = CImage::getImageSize($fileTempName, 'width');
					$fileHeight = CImage::getImageSize($fileTempName, 'height');
				}
			} elseif ($type == 'file') {
				if ($required && !isset($_FILES[$field]['tmp_name'])) {
					$required = false;
				} else {
					$fileType = CFile::getMimeType($fileTempName);
				}
			}
			
			if ($required && empty($fileSize)) {
				$valid = false;
				$errorMessage = A::t($msgSource, 'The field {title} cannot be empty! Please re-enter.', array('{title}' => $title));
			} elseif (!empty($fileSize)) {
				if ($maxSize !== '' && $fileSize > $maxSize) {
					$valid = false;
					$sFileSize = CConvert::fileSize($fileSize);
					$sMaxAllowed = CConvert::fileSize($maxSize);
					$errorMessage = A::t($msgSource, 'Invalid file size for field {title}: {file_size} (max. allowed: {max_allowed})', array('{title}' => $title, '{file_size}' => $sFileSize, '{max_allowed}' => $sMaxAllowed));
				} elseif (!empty($fileMimeTypes) && !in_array($fileType, $fileMimeTypes)) {
					$valid = false;
					$errorMessage = A::t($msgSource, 'Invalid file type for field {title}: you may only upload {mime_type} files.', array('{title}' => $title . ' (' . $fileType . ')', '{mime_type}' => $fileMimeType));
				} elseif ($maxWidth !== '' && $fileWidth > $maxWidth) {
					$valid = false;
					$errorMessage = A::t($msgSource, 'Invalid image width for field {title}: {image_width}px (max. allowed: {max_allowed}px)', array('{title}' => $title, '{image_width}' => $fileWidth, '{max_allowed}' => $maxWidth));
				} elseif ($maxHeight !== '' && $fileHeight > $maxHeight) {
					$valid = false;
					$errorMessage = A::t($msgSource, 'Invalid image height for field {title}: {image_height}px (max. allowed: {max_allowed}px)', array('{title}' => $title, '{image_height}' => $fileHeight, '{max_allowed}' => $maxHeight));
				} else {
					if (APPHP_MODE == 'demo') {
						$valid = false;
						$errorMessage = A::t($msgSource, 'This operation is blocked in Demo Mode!');
					} // Prevent malicious users and possible file upload attacks
					elseif (@is_uploaded_file($fileTempName)) {
						// Set predefined file name
						if (!empty($fileDefinedName)) {
							$targetFileName = strtolower($fileDefinedName . '.' . pathinfo($fileName, PATHINFO_EXTENSION));
						} else {
							$targetFileName = basename($fileName);
						}
						$targetFullName = $targetPath . $targetFileName;
						if (@move_uploaded_file($fileTempName, $targetFullName)) {
							// Uploaded - ok, save info in return array
							self::$_output['uploadedFiles'][] = $targetFullName;
						} else {
							$valid = false;
							$errorMessage = A::t($msgSource, 'An error occurred while uploading your file for field {title}. Please try again.', array('{title}' => $title));
							$err = error_get_last();
							if (!empty($err['message'])) {
								$lastError = $err['message'] . ' | file: ' . $err['file'] . ' | line: ' . $err['line'];
								CDebug::addMessage('errors', 'fileUploading', $lastError);
								@trigger_error('');
							}
						}
					} else {
						CDebug::addMessage('errors', 'fileUploading', A::t($msgSource, 'Possible file upload attack: file {filename}.', array('{filename}' => $fileTempName)));
					}
				}
			}
		} elseif ($required && (!is_array($fieldValue) && trim($fieldValue) === '' || is_array($fieldValue) && empty($fieldValue))) {
			$valid = false;
			$errorMessage = A::t($msgSource, 'The field {title} cannot be empty! Please re-enter.', array('{title}' => $title));
		} elseif ($type == 'confirm') {
			$confirmField = self::keyAt('validation.confirmField', $fieldInfo, '');
			$confirmFieldValue = $cRequest->getPost($confirmField);
			$confirmFieldName = self::keyAt($confirmField . '.title', $fields, '');
			if ($confirmFieldValue != $fieldValue) {
				$valid = false;
				$errorMessage = A::t($msgSource, 'The {confirm_field} and {title} fields do not match! Please re-enter.', array('{confirm_field}' => $confirmFieldName, '{title}' => $title));
			}
		} elseif ($fieldValue !== '') {
			if (!empty($minLength) && !CValidator::validateMinLength($fieldValue, $minLength)) {
				$valid = false;
				$errorMessage = A::t($msgSource, 'The {title} field length must be at least {min_length} characters! Please re-enter.', array('{title}' => $title, '{min_length}' => $minLength));
			} elseif (!empty($maxLength) && !self::_validateMaxLength($fieldValue, $maxLength, $title, $msgSource)) {
				$valid = false;
				$errorMessage = self::$_errorMessage;
			} elseif (is_array($forbiddenChars) && !empty($forbiddenChars)) {
				foreach ($forbiddenChars as $char) {
					if (preg_match('/' . $char . '/i', $fieldValue)) {
						$valid = false;
						$errorMessage = A::t($msgSource, 'The {title} field contains one or more forbidden characters from this list: {characters} ! Please re-enter.', array('{title}' => $title, '{characters}' => implode(' ', $forbiddenChars)));
						break;
					}
				}
			}
			
			if ($valid) {
				switch ($type) {
					case 'alpha':
					case 'alphabetic':
						$valid = CValidator::isAlpha($fieldValue, $allowedChars);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid alphabetic value! Please re-enter.', array('{title}' => $title));
						break;
					case 'alphanumeric':
						$valid = CValidator::isAlphaNumeric($fieldValue, $allowedChars);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid alpha-numeric value! Please re-enter.', array('{title}' => $title));
						break;
					case 'alphadash':
						$valid = CValidator::isAlphaDash($fieldValue, $allowedChars);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid alphabetic value or dash! Please re-enter.', array('{title}' => $title));
						break;
					case 'numeric':
						$valid = CValidator::isNumeric($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid numeric value! Please re-enter.', array('{title}' => $title));
						break;
					case 'variable':
						$valid = CValidator::isVariable($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid label name (alphanumeric, starts with letter and can contain an underscore)! Please re-enter.', array('{title}' => $title));
						break;
					case 'alphanumericspaces':
					case 'mixed':
						$valid = CValidator::isMixed($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} should include only alpha, space and numeric characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'seolink':
						$valid = CValidator::isSeoLink($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} should include only alpha, hyphen, underscore and numeric characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'timezone':
						$valid = CValidator::isTimeZone($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} should include only alpha and slashes characters! Please re-enter.', array('{title}' => $title));
						break;
					case 'phone':
						$valid = CValidator::isPhone($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid phone number! Please re-enter.', array('{title}' => $title));
						break;
					case 'phonestring':
						$valid = CValidator::isPhoneString($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid phone number! Please re-enter.', array('{title}' => $title));
						break;
					case 'zipcode':
						$valid = CValidator::isZipCode($fieldValue, $countryCode);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid zip/post code! Please re-enter.', array('{title}' => $title));
						break;
					case 'username':
						$valid = CValidator::isUsername($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must have a valid username value! Please re-enter.', array('{title}' => $title));
						break;
					case 'password':
						$valid = CValidator::isPassword($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must have a valid password value! Please re-enter.', array('{title}' => $title));
						if ($valid && !$simplePassword) {
							$valid = !CValidator::isSimplePassword($fieldValue);
							$errorMessage = A::t($msgSource, 'The password you entered is too easy, too simple, or too common! Please re-enter.');
						}
						break;
					case 'email':
						$valid = CValidator::isEmail($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid email address! Please re-enter.', array('{title}' => $title));
						break;
					case 'identity':
					case 'identitycode':
						$valid = CValidator::isIdentityCode($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid identity code! Please re-enter.', array('{title}' => $title));
						break;
					case 'filename':
						$valid = CValidator::isFileName($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid file name! Please re-enter.', array('{title}' => $title));
						break;
					case 'date':
						if ($viewType == 'datetime') {
							$valid = CValidator::isDateTime($fieldValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be a valid datetime value! Please re-enter.', array('{title}' => $title));
						} elseif ($viewType == 'time') {
							$valid = CValidator::isTime($fieldValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be a valid time value! Please re-enter.', array('{title}' => $title));
						} else {
							$valid = CValidator::isDate($fieldValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be a valid date value! Please re-enter.', array('{title}' => $title));
						}
						if ($valid && $minValue != '') {
							$valid = CValidator::validateMinDate($fieldValue, $minValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be greater than or equal to date {min}! Please re-enter.', array('{title}' => $title, '{min}' => $minValue));
						}
						if ($valid && $maxValue != '') {
							$valid = CValidator::validateMaxDate($fieldValue, $maxValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be less than or equal to date {max}! Please re-enter.', array('{title}' => $title, '{max}' => $maxValue));
						}
						break;
					case 'integer':
					case 'int':
						$valid = CValidator::isInteger($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid integer value! Please re-enter.', array('{title}' => $title));
						break;
					case 'positiveinteger':
					case 'positiveint':
						$valid = CValidator::isPositiveInteger($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid positive integer value! Please re-enter.', array('{title}' => $title));
						break;
					case 'percent':
						$valid = CValidator::validateRange($fieldValue, 0, 100);
						$errorMessage = A::t($msgSource, 'The value of field {title} must be between {min} and {max}! Please re-enter.', array('{title}' => $title, '{min}' => '0', '{max}' => '100'));
						break;
					case 'htmlsize':
						$valid = CValidator::isHtmlSize($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid HTML element size value (ex.: 100px, pt, em or %)! Please re-enter.', array('{title}' => $title));
						break;
					case 'float':
						$valid = CValidator::isFloat($fieldValue, $format);
						$format_sample = ($format == 'european') ? '1234,00' : '1234.00';
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid float value in format: {format}! Please re-enter.', array('{title}' => $title, '{format}' => $format_sample));
						if ($valid && $minValue != '') {
							$valid = CValidator::validateMin($fieldValue, $minValue, $format);
							$errorMessage = A::t($msgSource, 'The field {title} must be greater than or equal to {min}! Please re-enter.', array('{title}' => $title, '{min}' => $minValue));
						}
						if ($valid && $maxValue != '') {
							$valid = CValidator::validateMax($fieldValue, $maxValue, $format);
							$errorMessage = A::t($msgSource, 'The field {title} must be less than or equal to {max}! Please re-enter.', array('{title}' => $title, '{max}' => $maxValue));
						}
						break;
					case 'set':
						$sourceArray = self::keyAt('validation.source', $fieldInfo, array());
						if (is_array($fieldValue)) {
							foreach ($fieldValue as $key => $val) {
								$valid = CValidator::inArray($val, $sourceArray);
								if ($valid == false) {
									break;
								}
							}
							$errorMessage = A::t($msgSource, 'One of {title} fields has incorrect value! Please re-enter.', array('{title}' => $title));
						} else {
							$valid = CValidator::inArray($fieldValue, $sourceArray);
							$errorMessage = A::t($msgSource, 'The field {title} has incorrect value! Please re-enter.', array('{title}' => $title));
						}
						break;
					case 'range':
						if ($minValue == '') $minValue = '?';
						if ($maxValue == '') $maxValue = '?';
						$valid = CValidator::validateRange($fieldValue, $minValue, $maxValue);
						$errorMessage = A::t($msgSource, 'The value of field {title} must be between {min} and {max}! Please re-enter.', array('{title}' => $title, '{min}' => $minValue, '{max}' => $maxValue));
						break;
					case 'text':
						if (is_array($fieldValue)) {
							foreach ($fieldValue as $key => $val) {
								$valid = CValidator::isText($val);
								if ($valid == false) {
									break;
								}
							}
							$errorMessage = A::t($msgSource, 'One of {title} fields has incorrect value! Please re-enter.', array('{title}' => $title));
						} else {
							$valid = CValidator::isText($fieldValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be a valid textual value! Please re-enter.', array('{title}' => $title));
						}
						break;
					case 'url':
						$valid = CValidator::isUrl($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid URL string value! Please re-enter.', array('{title}' => $title));
						break;
					case 'ip':
						$valid = CValidator::isIpAddress($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid IP address! Please re-enter.', array('{title}' => $title));
						break;
					case 'hexcolor':
						$valid = CValidator::isHexColor($fieldValue);
						$errorMessage = A::t($msgSource, 'The field {title} must be a valid a hexadecimal color value, ex.: {sample}! Please re-enter.', array('{title}' => $title, '{sample}' => '#3369FE'));
						break;
					case 'captcha':
						$valid = ($fieldValue == A::app()->getSession()->get($field));
						$errorMessage = A::t($msgSource, 'Sorry, the code you have entered is invalid! Please try again.');
						break;
					case 'regex':
						$pattern = self::keyAt('validation.pattern', $fieldInfo);
						$valid = CValidator::validateRegex($fieldValue, $pattern);
						$errorMessage = A::t($msgSource, 'The field {title} has incorrect value! It must match pattern {pattern}. Please re-enter.', array('{title}' => $title, '{pattern}' => $pattern));
						break;
					case 'any':
					default:
						break;
				}
			}
		}
		
		if (!$valid) {
			self::$_output['error'] = true;
			if ($showAllErrors) {
				if (!self::keyAt('errorField', self::$_output)) self::$_output['errorField'] = $field;
				if (!self::keyAt('errorMessage', self::$_output)) {
					self::$_output['errorMessage'] = $errorMessage . '<br />';
				} else {
					self::$_output['errorMessage'] .= $errorMessage . '<br />';
				}
			} else {
				self::$_output['errorField'] = $field;
				self::$_output['errorMessage'] = $errorMessage;
				return 'break';
			}
		}
	}
	
	
	/**
	 * Validates max lenght
	 * @param mixed $fieldValue
	 * @param int $maxLength
	 * @param string $title
	 * @param string $msgSource
	 */
	private static function _validateMaxLength($fieldValue, $maxLength, $title, $msgSource)
	{
		$result = true;
		
		if (is_array($fieldValue)) {
			foreach ($fieldValue as $key => $val) {
				$valid = CValidator::validateMaxLength($val, $maxLength);
				if ($valid == false) {
					self::$_errorMessage = A::t($msgSource, 'One of {title} fields has incorrect value! Please re-enter.', array('{title}' => $title));
					$result = false;
					break;
				}
			}
		} elseif (!CValidator::validateMaxLength($fieldValue, $maxLength)) {
			self::$_errorMessage = A::t($msgSource, 'The {title} field length may be {max_length} characters maximum! Please re-enter.', array('{title}' => $title, '{max_length}' => $maxLength));
			$result = false;
		}
		
		return $result;
	}
	
}
