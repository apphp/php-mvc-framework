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
 * 
 * STATIC:
 * ---------------------------------------------------------------
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
     * possible validation types:
     *  	alpha, numeric, alphanumeric, variable, mixed, phone, username, timeZone
     *  	password, email, date, integer, float, any, confirm
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
     *         'field_8'=>array('title'=>'Image',           'validation'=>array('required'=>true, 'type'=>'image', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'image/jpeg, image/png', 'fileName'=>'')),
     *         'field_9'=>array('title'=>'File',            'validation'=>array('required'=>true, 'type'=>'file', 'targetPath'=>'protected/uploaded/', 'maxSize'=>'100k', 'mimeType'=>'application/zip, application/xml', 'fileName'=>'')),
     *        'field_10'=>array('title'=>'Format',          'validation'=>array('required'=>true, 'type'=>'set', 'source'=>array(1, 2, 3, 4, 5))),
     *     ),
     *     'messagesSource'=>'core',
     *     'showAllErrors'=>false,
     * ));
     *   
     * if($result['error']){
     *     $msg = $result['errorMessage'];
     *     $this->view->errorField = $result['errorField'];
     *     $errorType = 'validation';                
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
            $fieldValue  = $cRequest->getPost($field);
            $title       = isset($fieldInfo['title']) ? $fieldInfo['title'] : '';
            $required    = isset($fieldInfo['validation']['required']) ? $fieldInfo['validation']['required'] : false;
            $type        = isset($fieldInfo['validation']['type']) ? $fieldInfo['validation']['type'] : 'any';
            $minLength   = isset($fieldInfo['validation']['minLength']) ? $fieldInfo['validation']['minLength'] : '';
            $maxLength   = isset($fieldInfo['validation']['maxLength']) ? (int)$fieldInfo['validation']['maxLength'] : '';
            $maxSize     = isset($fieldInfo['validation']['maxSize']) ? CHtml::convertFileSize($fieldInfo['validation']['maxSize']) : '';
            $targetPath  = isset($fieldInfo['validation']['targetPath']) ? $fieldInfo['validation']['targetPath'] : '';
            $fileMimeType  = isset($fieldInfo['validation']['mimeType']) ? $fieldInfo['validation']['mimeType'] : '';
            $fileMimeTypes = (!empty($fileMimeType)) ? explode(',', str_replace(' ', '', $fileMimeType)) : array();
			$fileDefinedName  = isset($fieldInfo['validation']['fileName']) ? $fieldInfo['validation']['fileName'] : '';
            $errorMessage = '';
			$valid = true;
					
			if($type == 'file' || $type == 'image'){
                $fileName     = (isset($_FILES[$field]['name'])) ? $_FILES[$field]['name'] : '';
                $fileSize     = (isset($_FILES[$field]['size'])) ? $_FILES[$field]['size'] : 0;
                $fileTempName = (isset($_FILES[$field]['tmp_name'])) ? $_FILES[$field]['tmp_name'] : '';
                $fileType     = (isset($_FILES[$field]['type'])) ? $_FILES[$field]['type'] : '';
				if($type == 'image' && !empty($fileTempName)) $fileType = image_type_to_mime_type(exif_imagetype($fileTempName));
                $fileError    = (isset($_FILES[$field]['error'])) ? $_FILES[$field]['error'] : '';
	
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
                    }else{
						// set pre-defined file name
						$targetFileName = (!empty($fileDefinedName)) ? $fileDefinedName.'.'.pathinfo($fileName, PATHINFO_EXTENSION) : basename($fileName);
                        $targetFullName = $targetPath.$targetFileName;
                        if(@move_uploaded_file($fileTempName, $targetFullName)){							
                            // uploaded - ok, save info in return array
							$output['uploadedFiles'][] = $targetFullName;
                        }else{
                            $valid = false;
                            $errorMessage = A::t($msgSource, 'An error occurred while uploading your file for field {title}. Please try again.', array('{title}'=>$title));
                            if(version_compare(PHP_VERSION, '5.2.0', '>=')){	
                                $err = error_get_last();
                                if(!empty($err)){
                                    CDebug::addMessage('errors', 'fileUploading', $err['message']);
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
            }else if(!empty($fieldValue)){
                if(!empty($minLength) && !CValidator::validateMinlength($fieldValue, $minLength)){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The {title} field length must be at least {min_length} characters! Please re-enter.', array('{title}'=>$title, '{min_length}'=>$minLength));                
                }else if(!empty($maxLength) && !CValidator::validateMaxlength($fieldValue, $maxLength)){
                    $valid = false;
                    $errorMessage = A::t($msgSource, 'The {title} field length may be {max_length} characters maximum! Please re-enter.', array('{title}'=>$title, '{max_length}'=>$maxLength));
                }else{                    
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
                        case 'date':
                            $valid = CValidator::isDate($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid date value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'integer':
                            $valid = CValidator::isInteger($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid integer value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'float':
                            $valid = CValidator::isFloat($fieldValue);
                            $errorMessage = A::t($msgSource, 'The field {title} must be a valid float value! Please re-enter.', array('{title}'=>$title));
                            break;                                                
                        case 'set':
							$setArray  = isset($fieldInfo['validation']['source']) ? $fieldInfo['validation']['source'] : array();
							$valid = CValidator::inArray($fieldValue, $setArray);
                            $errorMessage = A::t($msgSource, 'The field {title} field has incorrect value! Please re-enter.', array('{title}'=>$title));							
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