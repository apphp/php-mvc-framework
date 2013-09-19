<?php

/**
 * SettingsController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct             
 * editAction
 * updateAction
 *
 */
class SettingsController extends CController
{
	
	public function __construct()
	{
        parent::__construct();

        // block access to this controller for not-logged users
		CAuth::handleLogin();				
	
        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', 'Settings | '.$settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->blogName = $settings->blog_name;
        $this->view->blogSlogan = $settings->slogan;
        $this->view->blogFooter = $settings->footer;

		$this->view->activeLink = 'settings';
		$this->view->viewRightMenu = false;
		$this->view->actionMessage = '';
        $this->view->errorField = '';
	}

   	public function editAction()
	{
        $settings = Settings::model()->findByPk(1);

        if(!$settings){
            $this->redirect('authors/index');
        }else{
            $this->view->blogName = $settings->blog_name;
            $this->view->slogan = $settings->slogan;
            $this->view->footer = $settings->footer;
            $this->view->postMaxChars = $settings->post_max_chars;
            $this->view->metaTagTitle = $settings->metatag_title;
            $this->view->metaTagKeywords = $settings->metatag_keywords;
            $this->view->metaTagDescription = $settings->metatag_description;
            
            $this->view->render('settings/edit');                        
        }
    }
    
   	public function updateAction()
	{
        $cRequest = A::app()->getRequest();
		$msg = '';
		$errorType = '';
		
		//General settings form post 
		if($cRequest->getPost('act') == 'send'){
			
            $this->view->blogName = $cRequest->getPost('blogName');
			$this->view->slogan = $cRequest->getPost('slogan');
			$this->view->footer = $cRequest->getPost('footer');
			$this->view->postMaxChars = $cRequest->getPost('postMaxChars');
			$this->view->metaTagTitle = $cRequest->getPost('metaTagTitle');
			$this->view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
			$this->view->metaTagDescription = $cRequest->getPost('metaTagDescription');
				
    	    // perform settings form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'blogName'		=>array('title'=>'Blog name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>100)),
                    'slogan'		=>array('title'=>'Slogan', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                	'footer'		=>array('title'=>'Footer', 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                	'postMaxChars'	=>array('title'=>'Maximum post length', 'validation'=>array('required'=>true, 'type'=>'numeric')),
             	    'metaTagTitle'		=>array('title'=>CHtml::encode('Tag <TITLE>'), 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>250)),
             	    'metaTagKeywords'	=>array('title'=>CHtml::encode('Meta tag <KEYWORDS>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                	'metaTagDescription'=>array('title'=>CHtml::encode('Meta tag <DESCRIPTION>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                ),            
            ));
           	if($result['error']){
				$msg = $result['errorMessage'];
				$this->view->errorField = $result['errorField'];
				$errorType = 'validation';                
            }else{
                
                $settings = Settings::model()->findByPk(1);
                $settings->blog_name = $this->view->blogName;
                $settings->slogan = $this->view->slogan;
                $settings->footer = $this->view->footer;
                $settings->post_max_chars = $this->view->postMaxChars;
                $settings->metatag_title = $this->view->metaTagTitle;
                $settings->metatag_keywords = $this->view->metaTagKeywords;
                $settings->metatag_description = $this->view->metaTagDescription;
                
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $errorType = 'warning';
                }else{                    
                    if($settings->save()){
                        $msg = 'Global settings have been successfully saved!';
                        $errorType = 'success';
                    }else{
                        $msg = 'An error occurred while saving the settings! Please re-enter.';
                        $this->view->errorField = 'blogName';
                        $errorType = 'error';
                    }
                }
			}

            if(!empty($msg)){
                $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
            }
            $this->view->render('settings/edit');		    
		}else{
            $this->redirect('settings/edit');
        }        
    }
    
}