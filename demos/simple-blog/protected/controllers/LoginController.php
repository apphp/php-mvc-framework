<?php

/**
 * LoginController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct
 * indexAction
 * logoutAction
 * runAction
 *
 */

class LoginController extends CController
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_view->errorField = '';
		$this->_view->actionMessage = '';
		$this->_view->username = '';
		$this->_view->password = '';
		
		$settings = Settings::model()->findByPk(1);
		$this->_view->setMetaTags('title', 'Login | ' . $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		$this->_view->blogName = $settings->blog_name;
		$this->_view->blogSlogan = $settings->slogan;
		$this->_view->blogFooter = $settings->footer;
		
		$this->_view->viewRightMenu = false;
	}
	
	public function indexAction()
	{
		CAuth::handleLoggedIn('authors/index');
		
		$this->_view->render('login/index');
	}
	
	public function logoutAction()
	{
		A::app()->getSession()->endSession();
		$this->redirect('login/index');
	}
	
	public function runAction()
	{
		$cRequest = A::app()->getRequest();
		$this->_view->username = $cRequest->getPost('username');
		$this->_view->password = $cRequest->getPost('password');
		$msg = '';
		$msgType = '';
		
		if ($cRequest->getPost('act') == 'send') {
			
			// perform login form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'username' => array('title' => 'Username', 'validation' => array('required' => true, 'type' => 'text', 'minLength' => 4, 'maxLength' => 20)),
					'password' => array('title' => 'Password', 'validation' => array('required' => true, 'type' => 'text', 'minLength' => 4, 'maxLength' => 20)),
				),
			));
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$this->_view->errorField = $result['errorField'];
			} else {
				$model = new Login();
				if ($model->login($this->_view->username, $this->_view->password)) {
					$this->redirect('authors/index');
				} else {
					$msg = 'Wrong username or password! Please re-enter.';
					$msgType = 'error';
					$this->_view->errorField = 'username';
				}
			}
			
			if (!empty($msg)) {
				$this->_view->username = $cRequest->getPost('username', 'string');
				$this->_view->password = $cRequest->getPost('password', 'string');
				
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg));
			}
		}
		$this->_view->render('login/index');
	}
	
}