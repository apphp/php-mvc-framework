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
		$this->_view->setMetaTags('title', $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		$this->_view->blogName = $settings->blog_name;
		$this->_view->blogSlogan = $settings->slogan;
		$this->_view->blogFooter = $settings->footer;
	}
	
	public function indexAction()
	{
		$this->redirect('posts/view');
	}
}