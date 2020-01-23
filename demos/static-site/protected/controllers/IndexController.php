<?php

class IndexController extends CController
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_view->setMetaTags('title', 'Sample application - Static Site : Index');
		$this->_view->setMetaTags('keywords', 'apphp framework, static site, apphp');
		$this->_view->setMetaTags('description', 'This is a static web site, consists from the few pages.');
	}
	
	public function indexAction()
	{
		$this->_view->header = 'Static Site';
		$this->_view->text = '
			This is a template for a simple static website. It includes a few pages and simple structure<br>
			consists from header, footer and a central part. Use it as a starting point to create something more unique.
			<br><br>
			Click links from the top menu to see the site in work.
		';
		$this->_view->render('index/index');
	}
	
}