<?php

/**
 * BackendController
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct
 * indexAction
 *
 */
class BackendController extends CController
{
	public function __construct()
	{
		parent::__construct();
		
		// block access to this controller for not-logged users
		CAuth::handleLogin();
		
		$this->_loggedId = CAuth::getLoggedId();
		
		$settings = Settings::model()->findByPk(1);
		$this->_view->setMetaTags('title', 'Account | ' . $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		
		$this->_view->cmsName = $settings->site_name;
		$this->_view->cmsSlogan = $settings->slogan;
		$this->_view->cmsFooter = $settings->footer;
		
		$this->_view->activeLink = 'home';
		$this->_view->viewRightMenu = false;
		$this->_view->errorField = '';
		$this->_view->actionMessage = '';
	}
	
	public function indexAction()
	{
		$this->_view->setMetaTags('title', 'Dashboard | ' . $this->_view->cmsName);
		$this->_view->mainHeader = 'Welcome to Admin Panel!';
		$this->_view->mainText = 'The administrator panel is an integrated place to manage your site. 
					Use navigation menu links from the left to access required page.';
		$this->_view->render('backend/index');
	}
}