<?php

class LoginController extends CController
{
    
	public function __construct()
	{
        parent::__construct();
		
        $this->_view->errorField = '';
		$this->_view->actionMessage = '';
		$this->_view->username = '';
		$this->_view->password = '';

		$this->_view->setMetaTags('title', 'Sample application - Simple Login System : Login');
		$this->_view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->_view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
    }

	public function indexAction()
	{
		CAuth::handleLoggedIn('page/dashboard');

		$this->_view->render('login/index');	
	}

	public function logoutAction()
	{
        A::app()->getSession()->endSession();
        $this->redirect('login/index');
	}
	
	public function runAction()
	{
		$cRequest = A::app()->getRequest();
		$this->_view->username = $cRequest->getPost('username');
		$this->_view->password = $cRequest->getPost('password');
		$msg = '';
		$msgType = '';        		
        
		if($cRequest->getPost('act') == 'send'){

            // perform login form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'username'=>array('title'=>'Username', 'validation'=>array('required'=>true, 'type'=>'any')),
                    'password'=>array('title'=>'Password', 'validation'=>array('required'=>true, 'type'=>'any')),
                ),            
            ));
            
            if($result['error']){
				$msg = $result['errorMessage'];
				$msgType = 'validation';                
				$this->_view->errorField = $result['errorField'];
            }else{
				$model = new Login();				
				if($model->login($this->_view->username, $this->_view->password)){
					$this->redirect('page/dashboard');	
				}else{
					$msg = 'Wrong username or password! Please re-enter.';
					$msgType = 'error';
					$this->_view->errorField = 'username';
				}                
            }
        
			if(!empty($msg)){				
				$this->_view->username = $cRequest->getPost('username', 'string');
				$this->_view->password = $cRequest->getPost('password', 'string');				
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg));
			}			
        }
		$this->_view->render('login/index');	
	}
    
}