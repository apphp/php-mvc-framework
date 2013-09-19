<?php

class IndexController extends CController
{
    
	public function __construct()
	{
        parent::__construct();

		$this->view->setMetaTags('title', 'Sample application - Static Site : Index');
		$this->view->setMetaTags('keywords', 'apphp framework, static site, apphp');
		$this->view->setMetaTags('description', 'This is a static web site, consists from the few pages.');
    }
	
	public function indexAction()
	{
        $this->view->header = 'Static Site';
        $this->view->text = '
			This is a template for a simple static website. It includes a few pages and simple structure<br>
			consists from header, footer and a central part. Use it as a starting point to create something more unique.
			<br><br>
			Click links from the top menu to see the site in work.
		';
        $this->view->render('index/index');		
    }
	
}