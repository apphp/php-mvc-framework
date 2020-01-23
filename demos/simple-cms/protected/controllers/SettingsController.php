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
		$this->_view->setMetaTags('title', 'Settings | ' . $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		$this->_view->cmsName = $settings->site_name;
		$this->_view->cmsSlogan = $settings->slogan;
		$this->_view->cmsFooter = $settings->footer;
		
		$this->_view->activeLink = 'settings';
		$this->_view->viewRightMenu = false;
		$this->_view->actionMessage = '';
		$this->_view->errorField = '';
	}
	
	public function editAction()
	{
		$settings = Settings::model()->findByPk(1);
		
		if (!$settings) {
			$this->redirect('admins/index');
		} else {
			$this->_view->cmsName = $settings->site_name;
			$this->_view->slogan = $settings->slogan;
			$this->_view->footer = $settings->footer;
			$this->_view->metaTagTitle = $settings->metatag_title;
			$this->_view->metaTagKeywords = $settings->metatag_keywords;
			$this->_view->metaTagDescription = $settings->metatag_description;
			
			$this->_view->render('settings/edit');
		}
	}
	
	public function updateAction()
	{
		$cRequest = A::app()->getRequest();
		$msg = '';
		$msgType = '';
		
		//General settings form post 
		if ($cRequest->getPost('act') == 'send') {
			
			$this->_view->cmsName = $cRequest->getPost('cmsName');
			$this->_view->slogan = $cRequest->getPost('slogan');
			$this->_view->footer = $cRequest->getPost('footer');
			$this->_view->postMaxChars = $cRequest->getPost('postMaxChars');
			$this->_view->metaTagTitle = $cRequest->getPost('metaTagTitle');
			$this->_view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
			$this->_view->metaTagDescription = $cRequest->getPost('metaTagDescription');
			
			// perform settings form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'cmsName' => array('title' => 'Site Name', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 100)),
					'slogan' => array('title' => 'Slogan', 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'footer' => array('title' => 'Footer', 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagTitle' => array('title' => CHtml::encode('Tag <TITLE>'), 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 250)),
					'metaTagKeywords' => array('title' => CHtml::encode('Meta tag <KEYWORDS>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagDescription' => array('title' => CHtml::encode('Meta tag <DESCRIPTION>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
				),
			));
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$this->_view->errorField = $result['errorField'];
			} else {
				$settings = Settings::model()->findByPk(1);
				$settings->site_name = $this->_view->cmsName;
				$settings->slogan = $this->_view->slogan;
				$settings->footer = $this->_view->footer;
				$settings->metatag_title = $this->_view->metaTagTitle;
				$settings->metatag_keywords = $this->_view->metaTagKeywords;
				$settings->metatag_description = $this->_view->metaTagDescription;
				
				if (APPHP_MODE == 'demo') {
					$msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
					$msgType = 'warning';
				} else {
					if ($settings->save()) {
						$msg = 'Global settings have been successfully saved!';
						$msgType = 'success';
					} else {
						$msg = 'An error occurred while saving the settings! Please re-enter.';
						$msgType = 'error';
						$this->_view->errorField = 'cmsName';
					}
				}
			}
			
			if (!empty($msg)) {
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
			}
			$this->_view->render('settings/edit');
		} else {
			$this->redirect('settings/edit');
		}
	}
	
}