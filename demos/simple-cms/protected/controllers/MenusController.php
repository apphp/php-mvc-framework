<?php

/**
 * MenusController
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
class MenusController extends CController
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
		
		$this->view->activeLink = 'edit_menu';
		$this->view->viewRightMenu = false;
        $this->view->actionMessage = '';
        $this->view->errorField = '';
        
	}
            
    public function viewAction($menuId = 0)
    {
    	$this->view->viewOnePost = false;
		$this->view->activeLink = '';
        $this->view->viewRightMenu = true;

        $menus = Menus::model()->findByPk($menuId);
    	$catName = (!is_null($menus)) ? $menus->name : '';

        $settings = Settings::model()->findByPk(1);
        $this->view->setMetaTags('title', $catName.' | '.$settings->metatag_title);
        
    	//All posts from the selected menu
    	$posts = Pages::model();
    	if(!$posts->count('menu_id = :menu_id', array(':menu_id'=>$menuId))){
            $errorType = 'warning';
    		$msg = (!empty($catName)) ? 'There are still no pages in menu <b>'.$catName.'</b>.' : 'Wrong parameter passed, please try again later.';
    	}else{

            // prepare pagination vars
            $this->view->targetPage = 'menus/view/id/'.$menuId;
            $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->view->pageSize = '5';
            $this->view->totalRecords = Pages::model()->count(array(
                    'condition'=>'menu_id = :menu_id'                                              
                ),
                array(':menu_id'=>$menuId)
            );

            $errorType = 'info';
	    	$msg = 'Menu: '.$catName;

            $this->view->posts = $posts->findAll(array(
                    'condition'=>'menu_id = :menu_id',
                    'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                    'order'=>'created_at DESC'
                ),
                array(':menu_id'=>$menuId)
            );    		
    	}    	
    	$this->view->mainText = CWidget::create('CMessage', array($errorType, $msg, array('button'=>false)));
    	$this->view->render('posts/view');
    }
    
    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Menus | '.$this->view->cmsName);        
    	$this->view->activeLink = 'edit_menu';
        $msg_text = '';

    	if(!empty($msg)){
            if($msg == 'delete_success'){
                $msg_text = 'Menu has been successfully deleted!';
                $errorType = 'success';
            }else if($msg == 'delete_error'){
                $msg_text = 'An error occurred while deleting the menu!';
                $errorType = 'error';
            }else if($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $errorType = 'warning';
            }else if($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check menu ID.';
                $errorType = 'error';                
            }
            if(!empty($msg_text)) $this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->view->targetPage = 'menus/index';
        $this->view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->view->pageSize = '15';
        $this->view->totalRecords = Menus::model()->count();
        
        if(!$this->view->currentPage){
            $this->view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->view->menus = Menus::model()->findAll(array(
                'limit'=>(($this->view->currentPage - 1) * $this->view->pageSize).', '.$this->view->pageSize,
                'order'=>'id ASC'
            ));        
        }
        
    	$this->view->render('menus/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Add Menu | '.$this->view->cmsName);
		$this->view->activeLink = 'add_menu';
    	$this->view->render('menus/add');
    }

    public function insertAction()
    {
         // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Add Menu | '.$this->view->cmsName);
		$this->view->activeLink = 'add_menu';
        $cRequest = A::app()->getRequest();

    	if($cRequest->getPost('act') == 'send'){
            
    		$this->view->menuName = $cRequest->getPost('menuName');
			$this->view->sortOrder = $cRequest->getPost('sortOrder');
    		
    	    // perform menu add form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'menuName' =>array('title'=>'Menu name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
                    'sortOrder' =>array('title'=>'Sort Order', 'validation'=>array('required'=>true, 'type'=>'integer', 'maxLength'=>3)),
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
                    $menus = new Menus();
                    if(!is_null($menus)){                    
                        $menus->name = $this->view->menuName;
                        if($menus->exists('name = :name', array(':name'=>$this->view->menuName))){
                            $msg = 'Menu "'.$this->view->menuName.'" already exists! Please re-enter.';
                            $errorType = 'error';
                            $this->view->errorField = 'menuName';
                        }else if($menus->save()){
                            $msg = 'New menu "'.$this->view->menuName.'" has been successfully added!';
                            $errorType = 'success';
                        }else{
                            $msg = 'An error occurred while insertion new menu! Please re-enter.';
                            $this->view->errorField = 'menuName';
                            $errorType = 'error';
                        }
                    }else{
                        $this->redirect('menus/index/msg/wrong-id');
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('menus/add');
    		}
    	}else{
            $this->redirect('menus/add');    
        }            	
    }
    
    
    public function editAction($menuId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->view->setMetaTags('title', 'Edit Menu | '.$this->view->cmsName);
		$this->view->activeLink = 'edit_menu';

    	$menus = Menus::model()->findByPk($menuId);
        if(!$menus){
    		$this->redirect('menus/index/msg/wrong-id');
        }
        
        $this->view->menuId = $menus->id;
        $this->view->menuName = $menus->name;
		$this->view->sortOrder = $menus->sort_order;                    
    	$this->view->render('menus/edit');
    }

    public function updateAction($menuId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->view->setMetaTags('title', 'Edit Menu | '.$this->view->cmsName);
		$this->view->activeLink = 'edit_menu';
        $cRequest = A::app()->getRequest();
    	
        if($cRequest->getPost('act') == 'send'){

    		$this->view->menuId = $cRequest->getPost('menuId');
    		$this->view->menuName = $cRequest->getPost('menuName');
			$this->view->sortOrder = $cRequest->getPost('sortOrder');
    		
    	    // perform menu edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'menuName'  =>array('title'=>'Menu name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
                    'sortOrder' =>array('title'=>'Sort Order', 'validation'=>array('required'=>true, 'type'=>'integer', 'maxLength'=>3)),
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
                    $menus = Menus::model()->findByPk($this->view->menuId);
                    if(!is_null($menus)){
                        $menus->name = $this->view->menuName;
						$menus->sort_order = $this->view->sortOrder;    
                        if($menus->exists('name = :name AND id != :id', array(':name'=>$this->view->menuName, ':id'=>$this->view->menuId))){
                            $msg = 'Menu "'.$this->view->menuName.'" already exists! Please re-enter.';
                            $errorType = 'error';
                            $this->view->errorField = 'menuName';
                        }else if($menus->save()){
                            $msg = 'Menu has been successfully updated!';
                            $errorType = 'success';
                        }else{
                            $msg = 'An error occurred while updating the menu! Please re-enter.';
                            $this->view->errorField = 'menuName';
                            $errorType = 'error';
                        }
                    }else{
                        $this->redirect('menus/index/msg/wrong-id');
                    }
                }
    		}
            
    		if(!empty($msg)){
    			$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
                $this->view->render('menus/edit');
            }
    	}else{
            $this->redirect('menus/index');    
        }        
    }
    

    public function deleteAction($menuId)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        if(APPHP_MODE == 'demo'){
            $this->redirect('menus/index/msg/delete_demo');
        }else{                    
            if(Menus::model()->deleteByPk($menuId)){
                //Pages::model()->deleteAll('menu_id = :menu_id', array(':menu_id' => $menuId));
                //Pages::model()->deleteAll('menu_id = '.(int)$menuId);
                $this->redirect('menus/index/msg/delete_success');
            }else{
                $this->redirect('menus/index/msg/delete_error');
            }
        }
    }
    
}