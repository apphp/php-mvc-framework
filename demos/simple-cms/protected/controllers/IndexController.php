<?php
/**
 * IndexController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct              
 * indexAction
 *
 */
class IndexController extends CController
{
    private $_session;
    
	public function __construct()
	{
        parent::__construct();		

        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', $settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->cmsName = $settings->site_name;
        $this->view->cmsSlogan = $settings->slogan;
        $this->view->cmsFooter = $settings->footer;
    }
    
	public function indexAction()
	{
		$this->redirect('pages/view');
	}     
}