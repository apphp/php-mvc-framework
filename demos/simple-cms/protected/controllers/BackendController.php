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
        $this->view->setMetaTags('title', 'Account | '.$settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);

		$this->view->cmsName = $settings->site_name;
		$this->view->cmsSlogan = $settings->slogan;
		$this->view->cmsFooter = $settings->footer;
		
		$this->view->activeLink = 'home';
		$this->view->viewRightMenu = false;
		$this->view->errorField = '';
		$this->view->actionMessage = '';
	}

   	public function indexAction()
   	{
        $this->view->setMetaTags('title', 'Dashboard | '.$this->view->cmsName);
		$this->view->mainHeader = 'Welcome to Admin Panel!';
		$this->view->mainText = 'The administrator panel is an integrated place to manage your site. 
					Use navigation menu links from the left to access required page.';
   		$this->view->render('backend/index');
   	}
}