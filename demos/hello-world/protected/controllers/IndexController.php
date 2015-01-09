<?php

class IndexController extends CController
{
    
	public function __construct()
	{
        parent::__construct();
    }
	
	public function indexAction()
	{
        $this->_view->text = 'Hello World!';
        $this->_view->render('index/index');
	}
	
}