<?php

class BackendController extends CController
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function indexAction()
	{
		$this->redirect('login/index');
	}
	
	public function loginAction()
	{
		$this->redirect('login/index');
	}
}