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
        $this->_view->setMetaTags('title', $settings->metatag_title);
        $this->_view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->_view->setMetaTags('description', $settings->metatag_description);
        $this->_view->blogName = $settings->blog_name;
        $this->_view->blogSlogan = $settings->slogan;
        $this->_view->blogFooter = $settings->footer;
        
		$this->_view->activeLink = 'edit_post';
		$this->_view->viewRightMenu = false;
        
        $this->_view->actionMessage = '';
        $this->_view->errorField = '';

    	$this->_view->header = '';
    	$this->_view->categoryId = '';
    	$this->_view->postText = '';
	}

    public function viewAction($postId = null)
    {    	 	
    	$postsModel = Posts::model();
        $settings = Settings::model()->findByPk(1);
        $this->_view->postMaxChars = ($settings->post_max_chars != null) ? $settings->post_max_chars : -1;
        $this->_view->activeLink = '';
    	$this->_view->viewRightMenu = true; 
		
    	// the posts list
    	if(empty($postId)){
    		$viewOnePost = false;
            $result = null;

            // prepare pagination vars
            $this->_view->targetPage = 'posts/view/';
            $this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->_view->pageSize = '5';
            $this->_view->totalRecords = $postsModel->count();
            
            if(!$this->_view->currentPage){
                $msg = 'Wrong parameter passed! Please try again later.';
                $msgType = 'error';
            }else{
                $result = $postsModel->findAll(array(
                    'limit'=>(($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
                    'order'=>'post_datetime DESC'
                ));    		
            }
    	}else{
    		$viewOnePost = true;
    		$result = $postsModel->find(CConfig::get('db.prefix').'posts.id = :id', array(':id'=>$postId));
    	}
    	$this->_view->viewOnePost = $viewOnePost;
    	
    	if(!$result){
    		$msg = (!empty($msg)) ? $msg : 'There are still no posts.';
    		$msgType = (!empty($msgType)) ? $msgType : 'info';
    		$this->_view->mainText = CWidget::create('CMessage', array($msgType, $msg, array('button'=>false)));
    	}else{
    		$this->_view->mainText = '';
    		if($viewOnePost){
		    	// meta tags specific for the post
		    	if(!empty($result->metatag_title)){
		    		$this->_view->setMetaTags('title', $result->header.' | '.$result->metatag_title);
		    	}		
		    	if(!empty($result->metatag_keywords)){
		    		$this->_view->setMetaTags('keywords', $result->metatag_keywords);
		    	}
		    	if(!empty($result->metatag_description)){
		    		$this->_view->setMetaTags('description', $result->metatag_description);    			
		    	}
            }
			$this->_view->posts = $result;
    	}
    
    	$this->_view->render('posts/view');
    }

    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->_view->setMetaTags('title', 'Posts | '.$this->_view->blogName);
    	$this->_view->activeLink = 'edit_post';

    	if(!empty($msg)){
            if($msg == 'added'){
                $msg_text = 'New post has been successfully added!';
                $msgType = 'success';
            }else if($msg == 'deleted'){
                $msg_text = 'Post has been successfully deleted!';
                $msgType = 'success';                
            }else if($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $msgType = 'warning';
            }else if($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check post ID.';
                $msgType = 'error';                
            }
            if(!empty($msg_text)) $this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->_view->targetPage = 'posts/index';
        $this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->_view->pageSize = '15';
        $this->_view->totalRecords = Posts::model()->count();
        
        if(!$this->_view->currentPage){
            $this->_view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->_view->posts = Posts::model()->findAll(array(
                'limit'=>(($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
                'order'=>'post_datetime DESC'
            ));        
        }

        $this->_view->render('posts/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->_view->setMetaTags('title', 'Add Post | '.$this->_view->blogName);
        $this->_view->activeLink = 'add_post';        
        $this->_view->categories = Categories::model()->findAll();

        $settings = Settings::model()->findByPk(1);
    	$this->_view->metaTagTitle = $settings->metatag_title;
    	$this->_view->metaTagKeywords = $settings->metatag_keywords;
    	$this->_view->metaTagDescription = $settings->metatag_description;
        
        $this->_view->render('posts/add');
    }

    public function insertAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
        $this->_view->setMetaTags('title', 'Add Post | '.$this->_view->blogName);
		$this->_view->activeLink = 'add_post'; 
        $this->_view->categories = Categories::model()->findAll();
    	$msg = '';
    	$msgType = '';

    	if($cRequest->getPost('act') == 'send'){
            
	    	$this->_view->header = $cRequest->getPost('header');
	    	$this->_view->categoryId = (int)$cRequest->getPost('categoryId');
	    	$this->_view->postText = $cRequest->getPost('postText');
	    	$this->_view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->_view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->_view->metaTagDescription = $cRequest->getPost('metaTagDescription');

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
				$msgType = 'validation';                
				$this->_view->errorField = $result['errorField'];
            }else{
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but insert operation is blocked in DEMO version!';
                    $msgType = 'warning';
                }else{                    
                    $posts = new Posts();
                    $posts->header = $this->_view->header;
                    $posts->category_id = (int)$this->_view->categoryId;
                    $posts->post_text = $this->_view->postText;
                    $posts->author_id = CAuth::getLoggedId();	
                    $posts->metatag_title = $this->_view->metaTagTitle;
                    $posts->metatag_keywords = $this->_view->metaTagKeywords;
                    $posts->metatag_description = $this->_view->metaTagDescription;
                    unset($posts->post_datetime);
                    
                    if($posts->save()){
                        $this->redirect('posts/index/msg/added');    
                    }else{
                        $msg = 'An error occurred while adding new post! Please re-enter.';
                        $msgType = 'error';
                        $this->_view->errorField = 'header';
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
                $this->_view->render('posts/add');
    		}               
    	}else{
            $this->redirect('posts/add');    
        }
    }

    public function editAction($postId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->_view->setMetaTags('title', 'Edit Post | '.$this->_view->blogName);        
		$this->_view->activeLink = 'edit_post';
        $this->_view->categories = Categories::model()->findAll();
        
    	$posts = Posts::model()->findByPk($postId);
        if(!$posts){
    		$this->redirect('posts/index/msg/wrong-id');
        }
        
        $this->_view->postId = $posts->id;
        $this->_view->header = $posts->header;
        $this->_view->categoryId = $posts->category_id;
        $this->_view->postText = $posts->post_text;
        $this->_view->metaTagTitle = $posts->metatag_title;
        $this->_view->metaTagKeywords = $posts->metatag_keywords;
        $this->_view->metaTagDescription = $posts->metatag_description;

    	$this->_view->render('posts/edit');
    }
    
    public function updateAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
		$this->_view->setMetaTags('title', 'Edit Post | '.$this->_view->blogName);        
        $this->_view->activeLink = 'edit_post';
        $this->_view->categories = Categories::model()->findAll();
    	$msg = '';
    	$msgType = '';
    
    	$posts = Posts::model();
    	 
        if($cRequest->getPost('act') == 'send'){

	    	$this->_view->postId = (int)$cRequest->getPost('postId');
    		$this->_view->header = $cRequest->getPost('header');
    		$this->_view->categoryId = (int)$cRequest->getPost('categoryId');
	    	$this->_view->postText = $cRequest->getPost('postText');
	    	$this->_view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->_view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->_view->metaTagDescription = $cRequest->getPost('metaTagDescription');
	    	
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
				$msgType = 'validation';                
				$this->_view->errorField = $result['errorField'];
            }else{
                if(APPHP_MODE == 'demo'){
                    $msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
                    $msgType = 'warning';
                }else{                    
                    $posts = Posts::model()->findByPk($this->_view->postId);
                    $posts->categoryOldId = $posts->category_id;
    
                    $posts->header = $this->_view->header;
                    $posts->category_id = $this->_view->categoryId;
                    $posts->post_text = $this->_view->postText;
                    $posts->metatag_title = $this->_view->metaTagTitle;
                    $posts->metatag_keywords = $this->_view->metaTagKeywords;
                    $posts->metatag_description = $this->_view->metaTagDescription;
                    unset($posts->post_datetime);
    
                    if($posts->save()){
                        $msg = 'Post has been successfully updated!';
                        $msgType = 'success';
                    }else{
                        $msg = 'An error occurred while updating new post! Please re-enter.';
                        $msgType = 'error';
                        $this->_view->errorField = 'header';
                    }
                }
    		}
            if(!empty($msg)){
    			$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
    		}
            $this->_view->render('posts/edit');
    	}else{
    		$this->redirect('posts/index');
    	}    	
    }

    public function deleteAction($postId)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
		$this->_view->activeLink = 'edit_post';
    	$msg = '';
    	$msgType = '';    

        if(APPHP_MODE == 'demo'){
            $this->redirect('posts/index/msg/delete_demo');
        }else{                    
            $posts = Posts::model()->findByPk($postId);        
            if($posts && $posts->delete()){
                $msg = 'Post has been successfully deleted!';
                $msgType = 'success';
            }else{
                $msg = 'An error occurred while deleting the post!';
                $msgType = 'error';
            }
            if(!empty($msg)){
                $this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
            }
             
            $this->redirect('posts/index/msg/deleted');
        }
    }   

}