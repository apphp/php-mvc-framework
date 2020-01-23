<?php

class PageController extends CController
{
	public $id;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_view->setMetaTags('title', 'Sample application - Simple Login System :: Index');
		$this->_view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->_view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
	}
	
	public function indexAction()
	{
		$this->redirect('index/index');
	}
	
	public function errorAction()
	{
		$this->_view->header = 'Error 404';
		$this->_view->text = CDebug::getMessage('errors', 'action') . '<br>Please check carefully the URL you\'ve typed.';
		$this->_view->render('error/index');
	}
	
	public function publicAction($id = 0)
	{
		$id = (CValidator::isDigit($id) && CValidator::validateMaxLength($id, 1)) ? $id : 0;
		$this->_view->id = $id;
		$this->_view->header = 'Page' . $id;
		if ($id === '1') {
			$this->_view->setMetaTags('title', 'Page 1 :: Simple Login System :: Index');
			$this->_view->text = '
				Mauris quis nisl sit amet augue adipiscing consectetur. Praesent lacus augue,
				vulputate ullamcorper pharetra in, ultricies et justo. Nulla id sem tortor.
				Ut hendrerit sagittis erat nec tristique. Aenean placerat massa a magna condimentum scelerisque.
				<br><br>
				Ut varius bibendum urna ac convallis. Sed non porta elit. Cum sociis natoque penatibus et magnis
				dis parturient montes, nascetur ridiculus mus. Vestibulum a nunc tincidunt velit egestas pulvinar
				ut a tellus. Cras porta suscipit lorem eget mattis. Donec fringilla, leo in vulputate interdum,
				dolor justo dapibus justo, eget vehicula massa velit ornare justo. Sed et mi in nunc interdum
				malesuada ac eu magna. Mauris ornare vestibulum congue. In scelerisque orci vel velit condimentum
				hendrerit.
			';
		} else {
			$this->_view->setMetaTags('title', 'Page 2 :: Simple Login System :: Index');
			$this->_view->text = '
				Ut varius bibendum urna ac convallis. Sed non porta elit. Cum sociis natoque penatibus et magnis
				dis parturient montes, nascetur ridiculus mus. Vestibulum a nunc tincidunt velit egestas pulvinar
				ut a tellus. Cras porta suscipit lorem eget mattis.
				<br><br>
				Donec fringilla, leo in vulputate interdum, dolor justo dapibus justo, eget vehicula massa velit
				ornare justo. Sed et mi in nunc interdum malesuada ac eu magna. Mauris ornare vestibulum congue.
				In scelerisque orci vel velit condimentum hendrerit.
				<br><br>
				Mauris quis nisl sit amet augue adipiscing consectetur. Praesent lacus augue, vulputate ullamcorper
				pharetra in, ultricies et justo. Nulla id sem tortor. Ut hendrerit sagittis erat nec tristique.
				Aenean placerat massa a magna condimentum scelerisque. Cras porta suscipit lorem eget mattis. Donec
				fringilla, leo in vulputate interdum, dolor justo dapibus justo, eget vehicula massa velit ornare
				justo. Sed et mi in nunc interdum malesuada ac eu magna. Mauris ornare vestibulum congue. In
				scelerisque orci vel velit condimentum hendrerit.
			';
		}
		
		$this->_view->render('page/index');
	}
	
	public function dashboardAction()
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->render('page/dashboard');
	}
	
}