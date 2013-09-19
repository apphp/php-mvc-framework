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
        $this->view->blogName = $settings->blog_name;
        $this->view->blogSlogan = $settings->slogan;
        $this->view->blogFooter = $settings->footer;
    }
    
	public function indexAction()
	{
		$this->redirect('posts/view');
	}     
}