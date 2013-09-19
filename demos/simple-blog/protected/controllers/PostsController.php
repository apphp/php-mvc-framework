<?php

/**
 * PostsController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct             
 * viewAction 
 * indexAction
 * addAction
 * editAction          
 * deleteAction
 *
 */
class PostsController extends CController
{

	public function __construct()
	{
        parent::__construct();
        
        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', $settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->blogName = $settings->blog_name;
        $this->view->blogSlogan = $settings->slogan;
        $this->view->blogFooter = $settings->footer;
        
		$this->view->activeLink = 'edit_post';
		$this->view->viewRightMenu = false;
        
        $this->view->actionMessage = '';
        $this->view->errorField = '';

    	$this->view->header = '';
    	$this->view->categoryId = '';
    	$this->view->postText = '';
	}

    public function viewAction($postId = null)
    {    	 	
    	$posts = Posts::model();
        $settings = Settings::model()->findByPk(1);
        $this->view->postMaxChars = ($settings->post_max_chars != null) ? $settings->post_max_chars : -1;
        $this->view->activeLink = '';
    	$this->view->viewRightMenu = true; 
		
    	//The posts list
    	if(empty($postId)){
    		$viewOnePost = false;
            $result = null;

            // prepare pagination vars
            $this->view->targetPage = 'posts/view/';
            $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->view->pageSize = '5';
            $this->view->totalRecords = Posts::model()->count();
            
            if(!$this->view->currentPage){
                $msg = 'Wrong parameter passed! Please try again later.';
                $errorType = 'error';
            }else{
                $result = $posts->findAll(array(
                    'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                    'order'=>'post_datetime DESC'
                ));    		
            }
    	}else{
    		$viewOnePost = true;
    		$result = $posts->find(CConfig::get('db.prefix').'posts.id = :id', array(':id'=>$postId));
    	}
    	$this->view->viewOnePost = $viewOnePost;
    	
    	if(!$result){
    		$msg = (!empty($msg)) ? $msg : 'There are still no posts.';
    		$errorType = (!empty($errorType)) ? $errorType : 'info';
    		$this->view->mainText = CWidget::create('CMessage', array($errorType, $msg, array('button'=>false)));
    	}else{
    		$this->view->mainText = '';
    		if($viewOnePost){
		    	// meta tags specific for the post
		    	if(!empty($result[0]['metatag_title'])){
		    		$this->view->setMetaTags('title', $result[0]['header'].' | '.$result[0]['metatag_title']);
		    	}		
		    	if(!empty($result[0]['metatag_keywords'])){
		    		$this->view->setMetaTags('keywords', $result[0]['metatag_keywords']);
		    	}
		    	if(!empty($result[0]['metatag_description'])){
		    		$this->view->setMetaTags('description', $result[0]['metatag_description']);    			
		    	}
            }
			$this->view->posts = $result;
    	}
    
    	$this->view->render('posts/view');
    }

    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Posts | '.$this->view->blogName);
    	$this->view->activeLink = 'edit_post';

    	if(!empty($msg)){
            if($msg == 'inserted'){
                $msg_text = 'New post has been successfully added!';
                $errorType = 'success';
            }else if($msg == 'deleted'){
                $msg_text = 'Post has been successfully deleted!';
                $errorType = 'success';                
            }else if($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $errorType = 'warning';
            }else if($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check post ID.';
                $errorType = 'error';                
            }
            if(!empty($msg_text)) $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->view->targetPage = 'posts/index';
        $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->view->pageSize = '15';
        $this->view->totalRecords = Posts::model()->count();
        
        if(!$this->view->currentPage){
            $this->view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->view->posts = Posts::model()->findAll(array(
                'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                'order'=>'post_datetime DESC'
            ));        
        }

        $this->view->render('posts/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->view->setMetaTags('title', 'Add Post | '.$this->view->blogName);
        $this->view->activeLink = 'add_post';        
        $this->view->categories = Categories::model()->findAll();

        $settings = Settings::model()->findByPk(1);
    	$this->view->metaTagTitle = $settings->metatag_title;
    	$this->view->metaTagKeywords = $settings->metatag_keywords;
    	$this->view->metaTagDescription = $settings->metatag_description;
        
        $this->view->render('posts/add');
    }

    public function insertAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
        $this->view->setMetaTags('title', 'Add Post | '.$this->view->blogName);
		$this->view->activeLink = 'add_post'; 
        $this->view->categories = Categories::model()->findAll();
    	$msg = '';
    	$errorType = '';

    	if($cRequest->getPost('act') == 'send'){
            
	    	$this->view->header = $cRequest->getPost('header');
	    	$this->view->categoryId = (int)$cRequest->getPost('categoryId');
	    	$this->view->postText = $cRequest->getPost('postText');
	    	$this->view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->view->metaTagDescription = $cRequest->getPost('metaTagDescription');

    	    // perform post add form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'header'		=>array('title'=>'Header', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>100)),
	                'postText'		=>array('title'=>'Post Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>4000)),
             	    'metaTagTitle'		=>array('title'=>CHtml::encode('Tag <TITLE>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
             	    'metaTagKeywords'	=>array('title'=>CHtml::encode('Meta tag <KEYWORDS>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                	'metaTagDescription'=>array('title'=>CHtml::encode('Meta tag <DESCRIPTION>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                ),            
            ));

            if($result['error']){
				$msg = $result['errorMessage'];
				$this->view->errorField = $result['errorField'];
				$errorType = 'validation';                
            }else{
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but insert operation is blocked in DEMO version!';
                    $errorType = 'warning';
                }else{                    
                    $posts = new Posts();
                    $posts->header = $this->view->header;
                    $posts->category_id = (int)$this->view->categoryId;
                    $posts->post_text = $this->view->postText;
                    $posts->author_id = CAuth::getLoggedId();	
                    $posts->metatag_title = $this->view->metaTagTitle;
                    $posts->metatag_keywords = $this->view->metaTagKeywords;
                    $posts->metatag_description = $this->view->metaTagDescription;
                    unset($posts->post_datetime);
                    
                    if($posts->save()){
                        $this->redirect('posts/index/msg/inserted');    
                    }else{
                        $msg = 'An error occurred while adding new post! Please re-enter.';
                        $errorType = 'error';
                        $this->view->errorField = 'header';
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('posts/add');
    		}               
    	}else{
            $this->redirect('posts/add');    
        }
    }

    public function editAction($postId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->view->setMetaTags('title', 'Edit Post | '.$this->view->blogName);        
		$this->view->activeLink = 'edit_post';
        $this->view->categories = Categories::model()->findAll();
        
    	$posts = Posts::model()->findByPk($postId);
        if(!$posts){
    		$this->redirect('posts/index/msg/wrong-id');
        }
        
        $this->view->postId = $posts->id;
        $this->view->header = $posts->header;
        $this->view->categoryId = $posts->category_id;
        $this->view->postText = $posts->post_text;
        $this->view->metaTagTitle = $posts->metatag_title;
        $this->view->metaTagKeywords = $posts->metatag_keywords;
        $this->view->metaTagDescription = $posts->metatag_description;

    	$this->view->render('posts/edit');
    }
    
    public function updateAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
		$this->view->setMetaTags('title', 'Edit Post | '.$this->view->blogName);        
        $this->view->activeLink = 'edit_post';
        $this->view->categories = Categories::model()->findAll();
    	$msg = '';
    	$errorType = '';
    
    	$posts = Posts::model();
    	 
        if($cRequest->getPost('act') == 'send'){

	    	$this->view->postId = (int)$cRequest->getPost('postId');
    		$this->view->header = $cRequest->getPost('header');
    		$this->view->categoryId = (int)$cRequest->getPost('categoryId');
	    	$this->view->postText = $cRequest->getPost('postText');
	    	$this->view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->view->metaTagDescription = $cRequest->getPost('metaTagDescription');
	    	
    	    // perform post edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'header'		=>array('title'=>'Header', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>100)),
 	                'postText'		=>array('title'=>'Post Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>4000)),
             	    'metaTagTitle'		=>array('title'=>CHtml::encode('Tag <TITLE>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
             	    'metaTagKeywords'	=>array('title'=>CHtml::encode('Meta tag <KEYWORDS>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
                	'metaTagDescription'=>array('title'=>CHtml::encode('Meta tag <DESCRIPTION>'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>250)),
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
                    $posts = Posts::model()->findByPk($this->view->postId);
                    $posts->categoryOldId = $posts->category_id;
    
                    $posts->header = $this->view->header;
                    $posts->category_id = $this->view->categoryId;
                    $posts->post_text = $this->view->postText;
                    $posts->metatag_title = $this->view->metaTagTitle;
                    $posts->metatag_keywords = $this->view->metaTagKeywords;
                    $posts->metatag_description = $this->view->metaTagDescription;
                    unset($posts->post_datetime);
    
                    if($posts->save()){
                        $msg = 'Post has been successfully updated!';
                        $errorType = 'success';
                    }else{
                        $msg = 'An error occurred while updating new post! Please re-enter.';
                        $this->view->errorField = 'header';
                        $errorType = 'error';
                    }
                }
    		}
            if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
    		}
            $this->view->render('posts/edit');
    	}else{
    		$this->redirect('posts/index');
    	}    	
    }

    public function deleteAction($postId)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
		$this->view->activeLink = 'edit_post';
    	$msg = '';
    	$errorType = '';    

        if(APPHP_MODE == 'demo'){
            $this->redirect('posts/index/msg/delete_demo');
        }else{                    
            $posts = Posts::model()->findByPk($postId);        
            if($posts && $posts->delete()){
                $msg = 'Post has been successfully deleted!';
                $errorType = 'success';
            }else{
                $msg = 'An error occurred while deleting the post!';
                $errorType = 'error';
            }
            if(!empty($msg)){
                $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
            }
             
            $this->redirect('posts/index/msg/deleted');
        }
    }   

}