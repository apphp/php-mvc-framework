<?php
/**
 * CFormValidation widget helper class file
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

class CFormValidation
{
    const NL = "\n";

    /**
     * Performs form validation
     * @param array $params
     * 
     * Usage: (in Controller class)
     * - possible validation types:
     *  	alpha, numeric, alphanumeric, variable, mixed, phone, phoneString, username, timeZone
     *  	password, email, fileName, identity|identityCode, date, integer, positiveInteger,
     *  	float, any, confirm, url, range ('minValue'=>'' and 'maxValue'=>''), set, text
     * - attribute 'validation'=>array(..., 'forbiddenChars'=>array('+', '$')) is used to define forbidden characters
     * - attribute 'validation'=>array(..., 'trim'=>true) - removes spaces from field value before validation
     * 
     * $result = CWidget::create('CFormValidation', array(
     *     'fields'=>array(
     *         'field_1'=>array('title'=>'Username',        'validation'=>array('required'=>true, 'type'=>'username')),
     *         'field_2'=>array('title'=>'Password',        'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6)),
     *         'field_3'=>array('title'=>'Repeat Password', 'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_2')),
     *         'field_4'=>array('title'=>'Email',           'validation'=>array('required'=>true, 'type'=>'email')),
     *         'field_5'=>array('title'=>'Confirm Email',   'validation'=>array('required'=>true, 'type'=>'confirm', 'confirmField'=>'field_4')),
     *         'field_6'=>array('title'=>'Mixed',           'validation'=>array('required'=>true, 'type'=>'mixed')),
     *         'field_7'=>array('title'=>'Field',           'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>255)),
     *         'field_8'=>array('title'=>'Image',           'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'maxWidth'=>'120px', 'maxHeight'=>'90px', 'mimeType'=>'image/jpeg, image/jpg, image/png, image/gif', 'fileName'=>'')),
     *         'field_9'=>array('title'=>'File',            'validation'=>array('required'=>true, 'type'=>'file', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml', 'fileName'=>'')),
     *        'field_10'=>array('title'=>'Price',           'validation'=>array('required'=>true, 'type'=>'float', 'minValue'=>'', 'maxValue'=>'', 'format'=>'american|european'),
     *        'field_11'=>array('title'=>'Format',          'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5))),
     *     ),
     *     'messagesSource'=>'core',
     *     'showAllErrors'=>false,
     * ));
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
        $output = array('error'=>false, 'uploadedFiles'=>array());
        $cRequest = A::app()->getRequest();
		
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $msgSource = isset($params['messagesSource']) ? $params['messagesSource'] : 'core';
		$showAllErrors = isset($params['showAllErrors']) ? (bool)$params['showAllErrors'] : false;
		
        foreach($fields as $field => $fieldInfo){
            $title          = isset($fieldInfo['title']) ? $fieldInfo['title'] : '';
            $required       = isset($fieldInfo['validation']['required']) ? $fieldInfo['validation']['required'] : false;
            $type           = isset($fieldInfo['validation']['type']) ? $fieldInfo['validation']['type'] : 'any';
            $forbiddenChars = isset($fieldInfo['validation']['forbiddenChars']) ? $fieldInfo['validation']['forbiddenChars'] : array();
            $minLength      = isset($fieldInfo['validation']['minLength']) ? $fieldInfo['validation']['minLength'] : '';
            $maxLength      = isset($fieldInfo['validation']['maxLength']) ? (int)$fieldInfo['validation']['maxLength'] : '';			
            $minValue       = isset($fieldInfo['validation']['minValue']) ? $fieldInfo['validation']['minValue'] : '';
            $maxValue       = isset($fieldInfo['validation']['maxValue']) ? $fieldInfo['validation']['maxValue'] : '';			
            $maxSize        = isset($fieldInfo['validation']['maxSize']) ? CHtml::convertFileSize($fieldInfo['validation']['maxSize']) : '';
            $maxWidth       = isset($fieldInfo['validation']['maxWidth']) ? CHtml::convertImageDimensions($fieldInfo['validation']['maxWidth']) : '';
            $maxHeight      = isset($fieldInfo['validation']['maxHeight']) ? CHtml::convertImageDimensions($fieldInfo['validation']['maxHeight']) : '';            
            $targetPath     = isset($fieldInfo['validation']['targetPath']) ? $fieldInfo['validation']['targetPath'] : '';
            $fileMimeType   = isset($fieldInfo['validation']['mimeType']) ? $fieldInfo['validation']['mimeType'] : '';
            $fileMimeTypes  = (!empty($fileMimeType)) ? explode(',', str_replace(' ', '', $fileMimeType)) : array();
			$fileDefinedName = isset($fieldInfo['validation']['fileName']) ? $fieldInfo['validation']['fileName'] : '';
            $trim           = isset($fieldInfo['validation']['trim']) ? (bool)$fieldInfo['validation']['trim'] : false;
            $format         = isset($fieldInfo['validation']['format']) ? $fieldInfo['validation']['format'] : '';
            $fieldValue     = ($trim) ? trim($cRequest->getPost($field)) : $cRequest->getPost($field);
            $errorMessage   = '';
			$valid = true;
					
			if($type == 'file' || $type == 'image'){
                $fileName     = (isset($_FILES[$field]['name'])) ? $_FILES[$field]['name'] : '';
                $fileSize     = (isset($_FILES[$field]['size'])) ? $_FILES[$field]['size'] : 0;
                $fileTempName = (isset($_FILES[$field]['tmp_name'])) ? $_FILES[$field]['tmp_name'] : '';
                $fileError    = (isset($_FILES[$field]['error'])) ? $_FILES[$field]['error'] : '';
                $fileType     = (isset($_FILES[$field]['type'])) ? $_FILES[$field]['type'] : '';
                $fileWidth    = '';
                $fileHeight   = '';
                if($type == 'image'){
                    if($required && !isset($_FILES[$field]['tmp_name'])){
                        $required = false;
                    }else{
                        if(function_exists('image_type_to_mime_type') && function_exists('exif_imagetype')){
                            $fileType = image_type_to_mime_type(exif_imagetype($fileTempName));
                        }else{
                            if(strrpos($fileTempName, '.') > 0){
                                $fileType = substr($fileTempName, strrpos($fileTempName, '.')+1);                            
                            }else{
                                $fileType = 'allowed';
                                $fileMimeTypes[] = 'allowed';
                            }
                            CDebug::addMessage('warnings', 'fileUploadingImageType', A::t($msgSource, 'Check if this function exists and usable: {function}', array('{function}', 'exif_imagetype')));
                        }
                        $fileWidth = CImage::getImageSize($fileTempName, 'width');
                        $fileHeight = CImage::getImageSize($fileTempName, 'height');
                    }
                } 
	
                if($required && empty($fileSize)){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The field {title} cannot be empty! Please re-enter.', array('{title}'=>$title));
                }else if(!empty($fileSize)){
                    if($maxSize !== '' && $fileSize > $maxSize){
                        $valid = false;
                        $sFileSize = number_format(($fileSize / 1024), 2, '.', ',').' Kb';
                        $sMaxAllowed = number_format(($maxSize / 1024), 2, '.', ',').' Kb';
                        $errorMessage = A::t($msgSource, 'Invalid file size for field {title}: {file_size} (max. allowed: {max_allowed})', array('{title}'=>$title, '{file_size}'=>$sFileSize, '{max_allowed}'=>$sMaxAllowed));
                    }else if(!empty($fileMimeTypes) && !in_array($fileType, $fileMimeTypes)){
						$valid = false;
                        $errorMessage = A::t($msgSource, 'Invalid file type for field {title}: you may only upload {mime_type} files.', array('{title}'=>$title, '{mime_type}'=>$fileMimeType));
                    }else if($maxWidth !== '' && $fileWidth > $maxWidth){
						$valid = false;
                        $errorMessage = A::t($msgSource, 'Invalid image width for field {title}: {image_width}px (max. allowed: {max_allowed}px)', array('{title}'=>$title, '{image_width}'=>$fileWidth, '{max_allowed}'=>$maxWidth));
                    }else if($maxHeight !== '' && $fileHeight > $maxHeight){
						$valid = false;
                        $errorMessage = A::t($msgSource, 'Invalid image height for field {title}: {image_height}px (max. allowed: {max_allowed}px)', array('{title}'=>$title, '{image_height}'=>$fileHeight, '{max_allowed}'=>$maxHeight));
                    }else{
						// set predefined file name
						$targetFileName = (!empty($fileDefinedName)) ? $fileDefinedName.'.'.pathinfo($fileName, PATHINFO_EXTENSION) : basename($fileName);
                        $targetFullName = $targetPath.$targetFileName;
                        if(APPHP_MODE == 'demo'){
                            $valid = false;
                            $errorMessage = A::t($msgSource, 'This operation is blocked in Demo Mode!');                        
                        }else if(@move_uploaded_file($fileTempName, $targetFullName)){							
                            // uploaded - ok, save info in return array
							$output['uploadedFiles'][] = $targetFullName;
                        }else{
                            $valid = false;
                            $errorMessage = A::t($msgSource, 'An error occurred while uploading your file for field {title}. Please try again.', array('{title}'=>$title));
                            if(version_compare(PHP_VERSION, '5.2.0', '>=')){	
                                $err = error_get_last();
                                if(isset($err['message']) && $err['message'] != ''){
                                    $lastError = $err['message'].' | file: '.$err['file'].' | line: '.$err['line'];
                                    CDebug::addMessage('errors', 'fileUploading', $lastError);
                                    @trigger_error('');
                                }
                            }else{
                                CDebug::addMessage('errors', 'fileUploading', $fileError);
                            }
                        }
                    }                    
                }
            }else if($required && trim($fieldValue) == ''){
                $valid = false;
                $errorMessage = A::t($msgSource, 'The field {title} cannot be empty! Please re-enter.', array('{title}'=>$title));
            }else if($type == 'confirm'){                
                $confirmField = isset($fieldInfo['validation']['confirmField']) ? $fieldInfo['validation']['confirmField'] : '';
                $confirmFieldValue = $cRequest->getPost($confirmField);
                $confirmFieldName = isset($fields[$confirmField]['title']) ? $fields[$confirmField]['title'] : '';
                if($confirmFieldValue != $fieldValue){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The {confirm_field} and {title} fields do not match! Please re-enter.', array('{confirm_field}'=>$confirmFieldName, '{title}'=>$title));
                }
            }else if($fieldValue !== ''){
                if(!empty($minLength) && !CValidator::validateMinLength($fieldValue, $minLength)){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The {title} field length must be at least {min_length} characters! Please re-enter.', array('{title}'=>$title, '{min_length}'=>$minLength));                
                }else if(!empty($maxLength) && !CValidator::validateMaxLength($fieldValue, $maxLength)){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The {title} field length may be {max_length} characters maximum! Please re-enter.', array('{title}'=>$title, '{max_length}'=>$maxLength));
				}else if(is_array($forbiddenChars) && !empty($forbiddenChars)){
					foreach($forbiddenChars as $char){
						if(preg_match('/'.$char.'/i', $fieldValue)){
							$valid = false;
							$errorMessage = A::t($msgSource, 'The {title} field contains one or more forbidden characters from this list: {characters} ! Please re-enter.', array('{title}'=>$title, '{characters}'=>implode(' ', $forbiddenChars)));
							break;
						} 
					}
                }
                
				if($valid){				
				    switch($type){
                        case 'alpha':
                            $valid = CValidator::isAlpha($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid alphabetic value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'numeric':
                            $valid = CValidator::isNumeric($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid numeric value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'alphanumeric':
                            $valid = CValidator::isAlphaNumeric($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid alpha-numeric value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'variable':
                            $valid = CValidator::isVariable($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid label name (alphanumeric, starts with letter and can contain an underscore)! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'mixed':
                            $valid = CValidator::isMixed($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} should include only alpha, space and numeric characters! Please re-enter.', array('{title}'=>$title));
                            break;                        
                        case 'timeZone':
                            $valid = CValidator::isTimeZone($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} should include only alpha and slashes characters! Please re-enter.', array('{title}'=>$title));
                            break;                        
                        case 'phone':
                            $valid = CValidator::isPhone($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid phone number! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'phoneString':
                            $valid = CValidator::isPhoneString($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid phone number! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'username':
                            $valid = CValidator::isUsername($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must have a valid username value! Please re-enter.', array('{title}'=>$title));
                            break;
                        case 'password':
                            $valid = CValidator::isPassword($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must have a valid password value! Please re-enter.', array('{title}'=>$title));
                            break;
                        case 'email':
                            $valid = CValidator::isEmail($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid email address! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'identity':
                        case 'identityCode':
                            $valid = CValidator::isIdentityCode($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid identity code! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'fileName':
                            $valid = CValidator::isFileName($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid file name! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'date':
                            $valid = CValidator::isDate($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid date value! Please re-enter.', array('{title}'=>$title));
                            if($valid && $minValue != ''){
                                $valid = CValidator::validateMinDate($fieldValue, $minValue);
                                $errorMessage = A::t($msgSource, 'The field {title} must be greater than or equal to date {min}! Please re-enter.', array('{title}'=>$title, '{min}'=>$minValue));                                
                            }
                            if($valid && $maxValue != ''){
                                $valid = CValidator::validateMaxDate($fieldValue, $maxValue);
                                $errorMessage = A::t($msgSource, 'The field {title} must be less than or equal to date {max}! Please re-enter.', array('{title}'=>$title, '{max}'=>$maxValue));
                            }
                            break;                                                
                        case 'integer':
                            $valid = CValidator::isInteger($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid integer value! Please re-enter.', array('{title}'=>$title));
                            break;
                        case 'positiveInteger':
						case 'positiveInt':	
                            $valid = CValidator::isPositiveInteger($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid positive integer value! Please re-enter.', array('{title}'=>$title));
                            break;						
                        case 'float':
                            $valid = CValidator::isFloat($fieldValue, $format);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid float value! Please re-enter.', array('{title}'=>$title));
                            if($valid && $minValue != ''){
                                $valid = CValidator::validateMin($fieldValue, $minValue, $format);
                                $errorMessage = A::t($msgSource, 'The field {title} must be greater than or equal to {min}! Please re-enter.', array('{title}'=>$title, '{min}'=>$minValue));                                
                            }else if($valid && $maxValue != ''){
                                $valid = CValidator::validateMax($fieldValue, $maxValue, $format);
                                $errorMessage = A::t($msgSource, 'The field {title} must be less than or equal to {max}! Please re-enter.', array('{title}'=>$title, '{max}'=>$maxValue));
                            }
                            break;                                                
                        case 'set':
							$setArray  = isset($fieldInfo['validation']['source']) ? $fieldInfo['validation']['source'] : array();
							$valid = CValidator::inArray($fieldValue, $setArray);
                            $errorMessage = A::t($msgSource, 'The field {title} field has incorrect value! Please re-enter.', array('{title}'=>$title));							
							break;
						case 'range':
							if($minValue == '') $minValue = '?';
							if($maxValue == '') $maxValue = '?';
							$valid = CValidator::validateRange($fieldValue, $minValue, $maxValue);
							$errorMessage = A::t($msgSource, 'The field {title} must be between {min} and {max}! Please re-enter.', array('{title}'=>$title, '{min}'=>$minValue, '{max}'=>$maxValue));
							break;
                        case 'text':
                            $valid = CValidator::isText($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid textual value! Please re-enter.', array('{title}'=>$title));
                            break;
                        case 'url':
                            $valid = CValidator::isUrl($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid URL string value! Please re-enter.', array('{title}'=>$title));
                            break;
                        case 'any':
                        default:                        
                            break;
                    }                    
                }
            }
            
            if(!$valid){
                $output['error'] = true;				
				if($showAllErrors){
					if($output['errorField'] == '') $output['errorField'] = $field;
					$output['errorMessage'] .= $errorMessage.'<br />';
				}else{
					$output['errorField'] = $field;
					$output['errorMessage'] = $errorMessage;
					break;
				}                
			}
        } // foreach
        return $output;        
    }
    
}