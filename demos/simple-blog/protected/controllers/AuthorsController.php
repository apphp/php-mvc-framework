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
		if(!CAuth::isLoggedIn()){
			$this->redirect('index/index');
		}	
			
        $this->_loggedId = CAuth::getLoggedId();

        $settings = Settings::model()->findByPk(1);
        $this->_view->setMetaTags('title', 'Account | '.$settings->metatag_title);
        $this->_view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->_view->setMetaTags('description', $settings->metatag_description);
        $this->_view->blogName = $settings->blog_name;
        $this->_view->blogSlogan = $settings->slogan;
        $this->_view->blogFooter = $settings->footer;

		$this->_view->activeLink = 'home';
		$this->_view->viewRightMenu = false;
		$this->_view->errorField = '';
		$this->_view->actionMessage = '';
	}

   	public function indexAction()
   	{
        $this->_view->setMetaTags('title', 'Dashboard | '.$this->_view->blogName);
		$this->_view->mainHeader = 'Welcome to Admin Panel!';
		$this->_view->mainText = 'The administrator panel is an integrated place to manage your site. 
					Use navigation menu links from the left to access required page.';
   		$this->_view->render('authors/index');
   	}
	
		
	public function editAction()
	{
        $this->_view->activeLink = 'author';
        
    	$author = Authors::model()->findByPk($this->_loggedId);
        if(empty($this->_loggedId) || !$author){
    		$this->redirect('authors/index');
        }
        
    	$this->_view->login = $author->login;
    	$this->_view->password = '';
    	$this->_view->passwordRetype = '';
    	$this->_view->email = $author->email;
    	$this->_view->aboutText = $author->about_text;
    	$this->_view->avatarFile = $author->avatar_file;

		$this->_view->render('authors/edit');		
    }

	public function updateAction()
	{
        $this->_view->activeLink = 'author';
        $cRequest = A::app()->getRequest();
		$msg = '';
		$msgType = '';
		
		if($cRequest->getPost('act') == 'send'){

            $author = Authors::model()->findByPk($this->_loggedId);

			$this->_view->login = $cRequest->getPost('login');
			$this->_view->password = $cRequest->getPost('password');
            $this->_view->passwordRetype = $cRequest->getPost('passwordRetype');
			$this->_view->email = $cRequest->getPost('email');
			$this->_view->aboutText = $cRequest->getPost('aboutText');
            $this->_view->avatarFile = !empty($_FILES['avatar']['name']) ? $_FILES['avatar']['name'] : $author->avatar_file;
				
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'password'	=>array('title'=>'Password', 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>4, 'maxlength'=>20)),
                    'passwordRetype' =>array('title'=>'Repeat Password', 'validation'=>array('required'=>false, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>4, 'maxlength'=>20)),
                	'email' 	=>array('title'=>'Email', 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>100)),
                	'aboutText' =>array('title'=>'About Me', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>300)),
    				'avatar'	=>array('title'=>'Avatar', 'validation'=>array('required'=>false, 'type'=>'image', 'targetPath'=>'templates/default/images/authors/', 'maxSize'=>'100k', 'mimeType'=>'image/jpeg, image/png, image/gif, image/jpg')),
                ),
            ));
            if($result['error']){
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$this->_view->errorField = $result['errorField'];
                
                if($this->_view->errorField == 'avatar'){
                    $this->_view->avatarFile = $author->avatar_file;
                }
			}else{
                $author->email = $this->_view->email;
                $author->about_text = $this->_view->aboutText;
                unset($author->password);
                unset($author->avatar_file);
                if($this->_view->password != ''){
                    $salt = CConfig::get('password.encryption') && (CConfig::get('password.encryptSalt')) ? CHash::salt() : '';
                    $author->password = (CConfig::get('password.encryption') ? CHash::create(CConfig::get('password.encryptAlgorithm'), $this->_view->password, $salt) : $this->_view->password);
                }
                if($this->_view->avatarFile != ''){
                    $author->avatar_file = $this->_view->avatarFile;
                }
                
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $msgType = 'warning';
                }else{                    
                    if($author->save()){                    
                        $msg = 'Author settings have been successfully saved!';
                        $msgType = 'success';
                        $this->_view->password = '';
                        $this->_view->passwordRetype = '';
                    }else{
                        $msg = 'An error occurred while saving the settings! Please re-enter.';
                        $msgType = 'error';
                        $this->_view->errorField = '';
                    }
                }
			}
		}else{
            $this->redirect('authors/edit');		
        }
		if(!empty($msg)){
			$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
		}
		
		$this->_view->render('authors/edit');		
    }
    
}