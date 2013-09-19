<?php

class LoginController extends CController
{
    
	public function __construct()
	{
        parent::__construct();
		
        $this->view->errorField = '';
		$this->view->actionMessage = '';
		$this->view->username = '';
		$this->view->password = '';
    }

	public function indexAction()
	{
		CAuth::handleLoggedIn('page/dashboard');

		$this->view->render('login/index');	
	}

	public function logoutAction()
	{
        A::app()->getSession()->endSession();
        $this->redirect('login/index');
	}
	
	public function runAction()
	{
		$cRequest = A::app()->getRequest();
		$this->view->username = $cRequest->getPost('username');
		$this->view->password = $cRequest->getPost('password');
		$msg = '';
		$errorType = '';        		
        
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
				$this->view->errorField = $result['errorField'];
				$errorType = 'validation';                
            }else{
				$model = new Login();				
				if($model->login($this->view->username, $this->view->password)){
					$this->redirect('page/dashboard');	
				}else{
					$msg = 'Wrong username or password! Please re-enter.';
					$this->view->errorField = 'username';
					$errorType = 'error';
				}                
            }
        
			if(!empty($msg)){				
				$this->view->username = $cRequest->getPost('username', 'string');
				$this->view->password = $cRequest->getPost('password', 'string');				
				$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg));
			}			
        }
		$this->view->render('login/index');	
	}
    
}