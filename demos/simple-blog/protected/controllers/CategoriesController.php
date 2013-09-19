<?php

/**
 * CategoriesController
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              
 * viewAction
 * indexAction
 * addAction
 * insertAction
 * editAction
 * updateAction
 * deleteAction
 *
 */
class CategoriesController extends CController
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
		
		$this->view->activeLink = 'edit_category';
		$this->view->viewRightMenu = false;
        $this->view->actionMessage = '';
        $this->view->errorField = '';
        
	}
            
    public function viewAction($categoryId = 0)
    {
    	$this->view->viewOnePost = false;
		$this->view->activeLink = '';
        $this->view->viewRightMenu = true;

        $categories = Categories::model()->findByPk($categoryId);
    	$catName = (!is_null($categories)) ? $categories->name : '';

        $settings = Settings::model()->findByPk(1);
        $this->view->postMaxChars = ($settings->post_max_chars != null) ? $settings->post_max_chars : -1;
        $this->view->setMetaTags('title', $catName.' | '.$settings->metatag_title);
        
    	//All posts from the selected category
    	$posts = Posts::model();
    	if(!$posts->count('category_id = :category_id', array(':category_id'=>$categoryId))){
            $errorType = 'warning';
    		$msg = (!empty($catName)) ? 'There are still no posts in category <b>'.$catName.'</b>.' : 'Wrong parameter passed, please try again later.';
    	}else{

            // prepare pagination vars
            $this->view->targetPage = 'categories/view/id/'.$categoryId;
            $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->view->pageSize = '5';
            $this->view->totalRecords = Posts::model()->count(array(
                    'condition'=>'category_id = :category_id'                                              
                ),
                array(':category_id'=>$categoryId)
            );

            $errorType = 'info';
	    	$msg = 'Category: '.$catName;

            $this->view->posts = $posts->findAll(array(
                    'condition'=>'category_id = :category_id',
                    'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                    'order'=>'post_datetime DESC'
                ),
                array(':category_id'=>$categoryId)
            );    		
    	}    	
    	$this->view->mainText = CWidget::create('CMessage', array($errorType, $msg, array('button'=>false)));
    	$this->view->render('posts/view');
    }
    
    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Categories | '.$this->view->blogName);        
    	$this->view->activeLink = 'edit_category';
        $msg_text = '';

    	if(!empty($msg)){
            if($msg == 'delete_success'){
                $msg_text = 'Category has been successfully deleted!';
                $errorType = 'success';
            }else if($msg == 'delete_error'){
                $msg_text = 'An error occurred while deleting the category!';
                $errorType = 'error';
            }else if($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $errorType = 'warning';
            }else if($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check category ID.';
                $errorType = 'error';                
            }
            if(!empty($msg_text)) $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->view->targetPage = 'categories/index';
        $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->view->pageSize = '15';
        $this->view->totalRecords = Categories::model()->count();
        
        if(!$this->view->currentPage){
            $this->view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->view->categories = Categories::model()->findAll(array(
                'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                'order'=>'id ASC'
            ));        
        }
        
    	$this->view->render('categories/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Add Category | '.$this->view->blogName);
		$this->view->activeLink = 'add_category';
    	$this->view->render('categories/add');
    }

    public function insertAction()
    {
         // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Add Category | '.$this->view->blogName);
		$this->view->activeLink = 'add_category';
        $cRequest = A::app()->getRequest();

    	if($cRequest->getPost('act') == 'send'){
            
    		$this->view->categoryName = $cRequest->getPost('categoryName');
    		
    	    // perform category add form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'categoryName' =>array('title'=>'Category name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
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
                    $categories = new Categories();
                    if(!is_null($categories)){                    
                        $categories->name = $this->view->categoryName;
                        $categories->posts_count = 0;
                        if($categories->exists('name = :name', array(':name'=>$this->view->categoryName))){
                            $msg = 'Category "'.$this->view->categoryName.'" already exists! Please re-enter.';
                            $errorType = 'error';
                            $this->view->errorField = 'categoryName';
                        }else if($categories->save()){
                            $msg = 'New category "'.$this->view->categoryName.'" has been successfully added!';
                            $errorType = 'success';
                        }else{
                            $msg = 'An error occurred while insertion new category! Please re-enter.';
                            $this->view->errorField = 'categoryName';
                            $errorType = 'error';
                        }
                    }else{
                        $this->redirect('categories/index/msg/wrong-id');
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('categories/add');
    		}
    	}else{
            $this->redirect('categories/add');    
        }            	
    }
    
    
    public function editAction($categoryId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Edit Category | '.$this->view->blogName);
		$this->view->activeLink = 'edit_category';

    	$categories = Categories::model()->findByPk($categoryId);
        if(!$categories){
    		$this->redirect('categories/index/msg/wrong-id');
        }
        
        $this->view->categoryId = $categories->id;
        $this->view->categoryName = $categories->name;                    
    	$this->view->render('categories/edit');
    }

    public function updateAction($categoryId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Edit Category | '.$this->view->blogName);
		$this->view->activeLink = 'edit_category';
        $cRequest = A::app()->getRequest();
    	
        if($cRequest->getPost('act') == 'send'){

    		$this->view->categoryId = $cRequest->getPost('categoryId');
    		$this->view->categoryName = $cRequest->getPost('categoryName');
    		
    	    // perform category edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'categoryName' =>array('title'=>'Category name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
                )
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
                    $categories = Categories::model()->findByPk($this->view->categoryId);
                    if(!is_null($categories)){
                        $categories->name = $this->view->categoryName;    
                        if($categories->exists('name = :name AND id != :id', array(':name'=>$this->view->categoryName, ':id'=>$this->view->categoryId))){
                            $msg = 'Category "'.$this->view->categoryName.'" already exists! Please re-enter.';
                            $errorType = 'error';
                            $this->view->errorField = 'categoryName';
                        }else if($categories->save()){
                            $msg = 'Category has been successfully updated!';
                            $errorType = 'success';
                        }else{
                            $msg = 'An error occurred while updating the category! Please re-enter.';
                            $this->view->errorField = 'categoryName';
                            $errorType = 'error';
                        }
                    }else{
                        $this->redirect('categories/index/msg/wrong-id');
                    }
                }
    		}
            
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('categories/edit');
            }
    	}else{
            $this->redirect('categories/index');    
        }        
    }
    

    public function deleteAction($categoryId)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        if(APPHP_MODE == 'demo'){
            $this->redirect('categories/index/msg/delete_demo');
        }else{                    
            if(Categories::model()->deleteByPk($categoryId)){
                //Posts::model()->deleteAll('category_id = :category_id', array(':category_id' => $categoryId));
                //Posts::model()->deleteAll('category_id = '.(int)$categoryId);
                $this->redirect('categories/index/msg/delete_success');
            }else{
                $this->redirect('categories/index/msg/delete_error');
            }
        }
    }
    
}