<?php

class AccountController extends CController
{

	public function __construct()
	{
        parent::__construct();

        // block access to this controller for not-logged users
		CAuth::handleLogin();		
        
		$this->view->setMetaTags('title', 'Sample application - Simple Login System : My Account');
		$this->view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
    }

   	public function editAction()
	{
        $cRequest = A::app()->getRequest();
		$this->view->errorField = '';
		$this->view->actionMessage = '';
		$this->view->username = $cRequest->getPost('username');
        $this->view->password = $cRequest->getPost('password');
		$msg = '';
		$errorType = '';

        $model = new Accounts();
        $info = $model->getInfo(CAuth::getLoggedId());
        
		if($cRequest->getPost('act') == 'send'){

            // perform account edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    //'username'=>array('title'=>'Username' 'validation'=>array('required'=>true, 'type'=>'username', 'minLength'=>6)),
                    'password'=>array('title'=>'Password', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>6)),
                ),            
            ));

            if($result['error']){
				$msg = $result['errorMessage'];
				$this->view->errorField = $result['errorField'];
				$errorType = 'validation';                
            }else{
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $errorType = 'warning';
                }else{                    
                    if($model->save($this->view->username, $this->view->password)){
                        $msg = 'Username and password have been successfully saved!';
                        $errorType = 'success';
                    }else{
                        $msg = 'An error occurred while saving username and password! Please re-enter.';
                        $this->view->errorField = 'password';
                        $errorType = 'error';
                    }
                }
            }

			if(!empty($msg)){				
				$this->view->username = $cRequest->getPost('username', 'string');				
				$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));				
			}else{
                $this->view->username = $info['username'];
            }
        }else{
            $this->view->username = $info['username'];
        }
        
       
        $this->view->render('account/edit');		
    }

}