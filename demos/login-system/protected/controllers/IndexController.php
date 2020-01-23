<?php

class IndexController extends CController
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_view->setMetaTags('title', 'Sample application - Simple Login System : Index');
		$this->_view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->_view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
	}
	
	public function indexAction()
	{
		$this->_view->header = 'Simple Login System';
		$this->_view->text = '
			This is a template for a simple login system website. It includes a few pages and simple structure<br>
			consists from header, footer and a central part. It also includes a protected area, that may be <br>
			accessed by access after login. Use it as a starting point to create something more unique.
			<br><br>
			Click links from the top menu to see the site in work.
		';
		$this->_view->render('index/index');
	}
	
}