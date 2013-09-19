<?php

/**
 * PagesController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
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
class PagesController extends CController
{

	public function __construct()
	{
        parent::__construct();
        
        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', $settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->cmsName = $settings->site_name;
        $this->view->cmsSlogan = $settings->slogan;
        $this->view->cmsFooter = $settings->footer;
        
		$this->view->activeLink = 'edit_page';
		$this->view->viewRightMenu = false;
        
        $this->view->actionMessage = '';
        $this->view->errorField = '';

		$this->view->isHomepage = 0;
    	$this->view->headerText = '';
		$this->view->linkText = '';
    	$this->view->menuId = '';
    	$this->view->pageText = '';
	}

    public function viewAction($pageId = null)
    {    	 	
    	$pages = Pages::model();
        $settings = Settings::model()->findByPk(1);
        $this->view->activeLink = '';
    	$this->view->viewRightMenu = true;
		$this->view->isHomePage = false;
		
    	//The pages list
    	if(empty($pageId)){
    		$viewOnePage = false;
            $result = null;

            // prepare pagination vars
            $this->view->targetPage = 'pages/view/';
            $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->view->pageSize = '5';
            
            if(!$this->view->currentPage){
                $msg = 'Wrong parameter passed! Please try again later.';
                $errorType = 'error';
            }else{
                $result = $pages->find('is_homepage = 1');    		
            }
    	}else{
    		$viewOnePage = true;
			$this->view->isHomePage = true;
    		$result = $pages->find(CConfig::get('db.prefix').'pages.id = :id', array(':id'=>$pageId));
    	}
    	$this->view->viewOnePage = $viewOnePage;
    	
    	if(!$result){
    		$msg = (!empty($msg)) ? $msg : 'There are still no pages.';
    		$errorType = (!empty($errorType)) ? $errorType : 'info';
    		$this->view->mainText = CWidget::create('CMessage', array($errorType, $msg, array('button'=>false)));
    	}else{
    		$this->view->mainText = '';
    		if($viewOnePage){
		    	// meta tags specific for the page
		    	if(!empty($result[0]['metatag_title'])){
		    		$this->view->setMetaTags('title', $result[0]['header_text'].' | '.$result[0]['metatag_title']);
		    	}		
		    	if(!empty($result[0]['metatag_keywords'])){
		    		$this->view->setMetaTags('keywords', $result[0]['metatag_keywords']);
		    	}
		    	if(!empty($result[0]['metatag_description'])){
		    		$this->view->setMetaTags('description', $result[0]['metatag_description']);    			
		    	}
            }
			$this->view->pages = $result;
    	}
    
    	$this->view->render('pages/view');
    }

    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Pages | '.$this->view->cmsName);
    	$this->view->activeLink = 'edit_page';

    	if(!empty($msg)){
            if($msg == 'inserted'){
                $msg_text = 'New page has been successfully added!';
                $errorType = 'success';
            }else if($msg == 'deleted'){
                $msg_text = 'Page has been successfully deleted!';
                $errorType = 'success';                
            }else if($msg == 'delete_error'){
                $msg_text = 'An error occurred while deleting the page!';
                $errorType = 'error';                
            }else if($msg == 'delete_homepage_error'){
                $msg_text = 'You cannot delete Homepage!';
                $errorType = 'error';                
            }else if($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $errorType = 'warning';
            }else if($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check page ID.';
                $errorType = 'error';                
            }
            if(!empty($msg_text)) $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->view->targetPage = 'pages/index';
        $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->view->pageSize = '15';
        $this->view->totalRecords = Pages::model()->count();
        
        if(!$this->view->currentPage){
            $this->view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->view->pages = Pages::model()->findAll(array(
                'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                'order'=>'created_at DESC'
            ));        
        }

        $this->view->render('pages/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->view->setMetaTags('title', 'Add Page | '.$this->view->cmsName);
        $this->view->activeLink = 'add_page';        
        $this->view->menus = Menus::model()->findAll();

        $settings = Settings::model()->findByPk(1);
    	$this->view->metaTagTitle = $settings->metatag_title;
    	$this->view->metaTagKeywords = $settings->metatag_keywords;
    	$this->view->metaTagDescription = $settings->metatag_description;
		$this->view->isHomepage = 0;
        
        $this->view->render('pages/add');
    }

    public function insertAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
        $this->view->setMetaTags('title', 'Add Page | '.$this->view->cmsName);
		$this->view->activeLink = 'add_page'; 
        $this->view->menus = Menus::model()->findAll();
    	$msg = '';
    	$errorType = '';

    	if($cRequest->getPost('act') == 'send'){
            
	    	$this->view->linkText = $cRequest->getPost('link_text');
			$this->view->headerText = $cRequest->getPost('header_text');
	    	$this->view->menuId = (int)$cRequest->getPost('menuId');
	    	$this->view->pageText = $cRequest->getPost('page_text');
	    	$this->view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->view->metaTagDescription = $cRequest->getPost('metaTagDescription');
			$this->view->isHomepage = $cRequest->getPost('is_homepage');

    	    // perform page add form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'link_text'   	    =>array('title'=>'Link Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>100)),
                    'header_text'   	=>array('title'=>'Header', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>255)),
	                'page_text'			=>array('title'=>'Page Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>4000)),
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
                    $pages = new Pages();
					$pages->link_text = $this->view->linkText;
                    $pages->header_text = $this->view->headerText;
                    $pages->menu_id = (int)$this->view->menuId;
					$pages->is_homepage = (int)$this->view->isHomepage;
                    $pages->page_text = $this->view->pageText;
                    $pages->metatag_title = $this->view->metaTagTitle;
                    $pages->metatag_keywords = $this->view->metaTagKeywords;
                    $pages->metatag_description = $this->view->metaTagDescription;
                    unset($pages->created_at);
                    
                    if($pages->save()){
                        $this->redirect('pages/index/msg/inserted');    
                    }else{
                        $msg = 'An error occurred while adding new page! Please re-enter.';
                        $errorType = 'error';
                        $this->view->errorField = 'header_text';
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('pages/add');
    		}               
    	}else{
            $this->redirect('pages/add');    
        }
    }

    public function editAction($pageId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	

        $this->view->setMetaTags('title', 'Edit Page | '.$this->view->cmsName);        
		$this->view->activeLink = 'edit_page';
        $this->view->menus = Menus::model()->findAll();
        
    	$pages = Pages::model()->findByPk($pageId);
        if(!$pages){
    		$this->redirect('pages/index/msg/wrong-id');
        }
        
        $this->view->pageId = $pages->id;
		$this->view->linkText = $pages->link_text;
        $this->view->headerText = $pages->header_text;
        $this->view->menuId = $pages->menu_id;
        $this->view->pageText = $pages->page_text;
        $this->view->metaTagTitle = $pages->metatag_title;
        $this->view->metaTagKeywords = $pages->metatag_keywords;
        $this->view->metaTagDescription = $pages->metatag_description;
		$this->view->isHomepage = $pages->is_homepage;

    	$this->view->render('pages/edit');
    }
    
    public function updateAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
    	$cRequest = A::app()->getRequest();
		$this->view->setMetaTags('title', 'Edit Page | '.$this->view->cmsName);        
        $this->view->activeLink = 'edit_page';
        $this->view->menus = Menus::model()->findAll();
    	$msg = '';
    	$errorType = '';
    
    	$pages = Pages::model();
    	 
        if($cRequest->getPost('act') == 'send'){

	    	$this->view->pageId = (int)$cRequest->getPost('pageId');
    		$this->view->linkText = $cRequest->getPost('link_text');
			$this->view->headerText = $cRequest->getPost('header_text');
    		$this->view->menuId = (int)$cRequest->getPost('menuId');
	    	$this->view->pageText = $cRequest->getPost('page_text');
	    	$this->view->metaTagTitle = $cRequest->getPost('metaTagTitle');
	    	$this->view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
	    	$this->view->metaTagDescription = $cRequest->getPost('metaTagDescription');
			$this->view->isHomepage = $cRequest->getPost('is_homepage');
	    	
    	    // perform page edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'link_text'   	    =>array('title'=>'Link Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>100)),
                    'header_text'   	=>array('title'=>'Header', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>255)),
	                'page_text'			=>array('title'=>'Page Text', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>4000)),
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
                    $pages = Pages::model()->findByPk($this->view->pageId);
                    $pages->menu_id = $this->view->menuId;
					$pages->link_text = $this->view->linkText;
					$pages->header_text = $this->view->headerText;
                    $pages->page_text = $this->view->pageText;
                    $pages->metatag_title = $this->view->metaTagTitle;
                    $pages->metatag_keywords = $this->view->metaTagKeywords;
                    $pages->metatag_description = $this->view->metaTagDescription;
					$pages->is_homepage = (int)$this->view->isHomepage;
                    unset($pages->created_at);
    
                    if($pages->save()){
                        $msg = 'Page has been successfully updated!';
                        $errorType = 'success';
                    }else{
                        $msg = 'An error occurred while updating new page! Please re-enter.';
                        $this->view->errorField = 'header';
                        $errorType = 'error';
                    }
                }
    		}
            if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
    		}
            $this->view->render('pages/edit');
    	}else{
    		$this->redirect('pages/index');
    	}    	
    }

    public function deleteAction($pageId)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
		$this->view->activeLink = 'edit_page';
    	$msg = '';
    	$errorType = '';    

        if(APPHP_MODE == 'demo'){
            $this->redirect('pages/index/msg/delete_demo');
        }else{                    
            $pages = Pages::model()->findByPk($pageId);
			if($pages->is_homepage == 1){
                $msg = 'delete_homepage_error';
                $errorType = 'error';				
			}else if($pages && $pages->delete()){
                $msg = 'deleted';
                $errorType = 'success';
            }else{
                $msg = 'delete_error';
                $errorType = 'error';
            }

			$this->redirect('pages/index/msg/'.$msg);				
        }
    }   

}