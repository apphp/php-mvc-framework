<?php
/**
* AuthorsController
*
* PUBLIC:                  PRIVATE
* -----------              ------------------
* __construct              
* indexAction
* editAction
* updateAction
*
*/
class AuthorsController extends CController
{
	public function __construct()
	{
        parent::__construct();
        
        // block access to this controller for not-logged users
		CAuth::handleLogin();	
			
        $this->_loggedId = CAuth::getLoggedId();

        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', 'Account | '.$settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->blogName = $settings->blog_name;
        $this->view->blogSlogan = $settings->slogan;
        $this->view->blogFooter = $settings->footer;

		$this->view->activeLink = 'home';
		$this->view->viewRightMenu = false;
		$this->view->errorField = '';
		$this->view->actionMessage = '';
	}

   	public function indexAction()
   	{
        $this->view->setMetaTags('title', 'Dashboard | '.$this->view->blogName);
		$this->view->mainHeader = 'Welcome to Admin Panel!';
		$this->view->mainText = 'The administrator panel is an integrated place to manage your site. 
					Use navigation menu links from the left to access required page.';
   		$this->view->render('authors/index');
   	}
	
		
	public function editAction()
	{
        $this->view->activeLink = 'author';
        
    	$author = Authors::model()->findByPk($this->_loggedId);
        if(empty($this->_loggedId) || !$author){
    		$this->redirect('authors/index');
        }
        
    	$this->view->login = $author->login;
    	$this->view->password = '';
    	$this->view->passwordRetype = '';
    	$this->view->email = $author->email;
    	$this->view->aboutText = $author->about_text;
    	$this->view->avatarFile = $author->avatar_file;

		$this->view->render('authors/edit');		
    }

	public function updateAction()
	{
        $this->view->activeLink = 'author';
        $cRequest = A::app()->getRequest();
		$msg = '';
		$errorType = '';
		
		if($cRequest->getPost('act') == 'send'){

            $author = Authors::model()->findByPk($this->_loggedId);

			$this->view->login = $cRequest->getPost('login');
			$this->view->password = $cRequest->getPost('password');
			$this->view->passwordRetype = $cRequest->getPost('passwordRetype');
			$this->view->email = $cRequest->getPost('email');
			$this->view->aboutText = $cRequest->getPost('aboutText');
            $this->view->avatarFile = !empty($_FILES['avatar']['name']) ? $_FILES['avatar']['name'] : $author->avatar_file;
				
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'password'	=>array('title'=>'Password', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxlength'=>20)),
                    'passwordRetype' =>array('title'=>'Repeat Password', 'validation'=>array('required'=>false, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxlength'=>20)),
                	'email' 	=>array('title'=>'Email', 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100)),
                	'aboutText' =>array('title'=>'About Me', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>300)),
    				'avatar'	=>array('title'=>'Avatar', 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'templates/default/images/authors/', 'maxSize'=>'100k', 'mimeType'=>'image/jpeg, image/png, image/gif, image/jpg')),
                ),
            ));
            if($result['error']){
				$msg = $result['errorMessage'];
				$this->view->errorField = $result['errorField'];
				$errorType = 'validation';
                
                if($this->view->errorField == 'avatar'){
                    $this->view->avatarFile = $author->avatar_file;
                }
			}else{
                $author->email = $this->view->email;
                $author->about_text = $this->view->aboutText;
                unset($author->password);
                unset($author->avatar_file);
                if($this->view->password != ''){
                    $author->password = ((CConfig::get('password.encryption')) ? CHash::create(CConfig::get('password.encryptAlgorithm'), $this->view->password, CConfig::get('password.hashKey')) : $this->view->password);
                }
                if($this->view->avatarFile != ''){
                    $author->avatar_file = $this->view->avatarFile;
                }
                
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $errorType = 'warning';
                }else{                    
                    if($author->save()){                    
                        $msg = 'Author settings have been successfully saved!';
                        $errorType = 'success';
                        $this->view->password = '';
                        $this->view->passwordRetype = '';
                    }else{
                        $msg = 'An error occurred while saving the settings! Please re-enter.';
                        $this->view->errorField = '';
                        $errorType = 'error';
                    }
                }
			}
		}else{
            $this->redirect('authors/edit');		
        }
		if(!empty($msg)){
			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
		}
		
		$this->view->render('authors/edit');		
    }
    
}