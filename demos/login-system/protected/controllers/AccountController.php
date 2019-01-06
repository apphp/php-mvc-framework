<?php

class AccountController extends CController
{

	public function __construct()
	{
        parent::__construct();

        // block access to this controller for not-logged users
		if(!CAuth::isLoggedIn()){
			$this->redirect('index/index');
		}	
        
		$this->_view->setMetaTags('title', 'Sample application - Simple Login System : My Account');
		$this->_view->setMetaTags('keywords', 'apphp framework, simple login system, apphp');
		$this->_view->setMetaTags('description', 'This is a simple login system, consists from the few pages and protected area.');
    }

   	public function editAction()
	{
        $cRequest = A::app()->getRequest();
		$this->_view->errorField = '';
		$this->_view->actionMessage = '';
		$this->_view->username = $cRequest->getPost('username');
        $this->_view->password = $cRequest->getPost('password');
		$msg = '';
		$msgType = '';

        $model = new Accounts();
        $info = $model->getInfo(CAuth::getLoggedId());
        
		if($cRequest->getPost('act') == 'send'){

            // Perform account edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    //'username'=>array('title'=>'Username' 'validation'=>array('required'=>true, 'type'=>'username', 'minLength'=>4)),
                    'password'=>array('title'=>'Password', 'validation'=>array('required'=>true, 'type'=>'password', 'minLength'=>4)),
                ),            
            ));

            if($result['error']){
				$msg = $result['errorMessage'];
				$this->_view->errorField = $result['errorField'];
				$msgType = 'validation';                
            }else{
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $msgType = 'warning';
                }else{                    
                    if($model->save($this->_view->username, $this->_view->password)){
                        $msg = 'Username and password have been successfully saved!';
                        $msgType = 'success';
                    }else{
                        $msg = 'An error occurred while saving username and password! Please re-enter.';
                        $this->_view->errorField = 'password';
                        $msgType = 'error';
                    }
                }
            }

			if(!empty($msg)){				
				$this->_view->username = $cRequest->getPost('username', 'string');				
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));				
			}else{
                $this->_view->username = $info['username'];
            }
        }else{
            $this->_view->username = $info['username'];
        }
        
       
        $this->_view->render('account/edit');		
    }

}