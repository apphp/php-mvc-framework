<?php

class IndexController extends CController
{
    
	public function __construct()
	{
        parent::__construct();
    }
	
	public function indexAction()
	{
        $this->view->text = 'Hello World!';
        $this->view->render('index/index');
	}
	
}