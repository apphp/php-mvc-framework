<?php

class PageController extends CController
{
	public $id;
	
	public function __construct()
	{
        parent::__construct();
		
		$this->view->setMetaTags('title', 'Sample application - Simple Login System : Index');
		$this->view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
    }
	
	public function indexAction()
	{
		$this->redirect('index/index');
    }

	public function errorAction()
	{
        $this->view->header = 'Error 404';
        $this->view->text = CDebug::getMessage('errors', 'action').'<br>Please check carefully the URL you\'ve typed.';		
        $this->view->render('error/index');        
    }
    
	public function publicAction($id = 0)
	{
		$id = (CValidator::isDigit($id) && CValidator::validateMaxlength($id, 1)) ? $id : 0;		
		$this->view->id = $id;
        $this->view->header = 'Page'.$id;
		if($id === '1'){
			$this->view->text = '
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
		}else{
			$this->view->text = '
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
		
        $this->view->render('page/index');		
    }
	
	public function dashboardAction()
	{
		CAuth::handleLogin();		
		$this->view->render('page/dashboard');		
	}	

}