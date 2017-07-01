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
        $this->_view->setMetaTags('title', $settings->metatag_title);
        $this->_view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->_view->setMetaTags('description', $settings->metatag_description);
        $this->_view->cmsName = $settings->site_name;
        $this->_view->cmsSlogan = $settings->slogan;
        $this->_view->cmsFooter = $settings->footer;
		
		$this->_view->activeLink = 'edit_menu';
		$this->_view->viewRightMenu = false;
        $this->_view->actionMessage = '';
        $this->_view->errorField = '';
        
	}
            
    public function viewAction($menuId = 0)
    {
    	$this->_view->viewOnePost = false;
		$this->_view->activeLink = '';
        $this->_view->viewRightMenu = true;

        $menus = Menus::model()->findByPk($menuId);
    	$catName = (!is_null($menus)) ? $menus->name : '';

        $settings = Settings::model()->findByPk(1);
        $this->_view->setMetaTags('title', $catName.' | '.$settings->metatag_title);
        
    	//All posts from the selected menu
    	$posts = Pages::model();
    	if(!$posts->count('menu_id = :menu_id', array(':menu_id'=>$menuId))){
            $msgType = 'warning';
    		$msg = (!empty($catName)) ? 'There are still no pages in menu <b>'.$catName.'</b>.' : 'Wrong parameter passed, please try again later.';
    	}else{

            // prepare pagination vars
            $this->_view->targetPage = 'menus/view/id/'.$menuId;
            $this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
            $this->_view->pageSize = '5';
            $this->_view->totalRecords = Pages::model()->count(array(
                    'condition'=>'menu_id = :menu_id'                                              
                ),
                array(':menu_id'=>$menuId)
            );

            $msgType = 'info';
	    	$msg = 'Menu: '.$catName;

            $this->_view->posts = $posts->findAll(array(
                    'condition'=>'menu_id = :menu_id',
                    'limit'=>(($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
                    'order'=>'created_at DESC'
                ),
                array(':menu_id'=>$menuId)
            );    		
    	}    	
    	$this->_view->mainText = CWidget::create('CMessage', array($msgType, $msg, array('button'=>false)));
    	$this->_view->render('posts/view');
    }
    
    public function indexAction($msg = '')
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->_view->setMetaTags('title', 'Menus | '.$this->_view->cmsName);        
    	$this->_view->activeLink = 'edit_menu';
        $msg_text = '';

    	if(!empty($msg)){
            if($msg == 'delete_success'){
                $msg_text = 'Menu has been successfully deleted!';
                $msgType = 'success';
            }elseif($msg == 'delete_error'){
                $msg_text = 'An error occurred while deleting the menu!';
                $msgType = 'error';
            }elseif($msg == 'delete_demo'){
                $msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
                $msgType = 'warning';
            }elseif($msg == 'wrong-id'){
                $msg_text = 'Wrong parameter passed! Check menu ID.';
                $msgType = 'error';                
            }
            if(!empty($msg_text)) $this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg_text, array('button'=>true)));
    	}

        // prepare pagination vars
        $this->_view->targetPage = 'menus/index';
        $this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
        $this->_view->pageSize = '15';
        $this->_view->totalRecords = Menus::model()->count();
        
        if(!$this->_view->currentPage){
            $this->_view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button'=>true)));
        }else{
            $this->_view->menus = Menus::model()->findAll(array(
                'limit'=>(($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
                'order'=>'id ASC'
            ));        
        }
        
    	$this->_view->render('menus/index');
    }

    public function addAction()
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->_view->setMetaTags('title', 'Add Menu | '.$this->_view->cmsName);
		$this->_view->activeLink = 'add_menu';
    	$this->_view->render('menus/add');
    }

    public function insertAction()
    {
         // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->_view->setMetaTags('title', 'Add Menu | '.$this->_view->cmsName);
		$this->_view->activeLink = 'add_menu';
        $cRequest = A::app()->getRequest();

    	if($cRequest->getPost('act') == 'send'){
            
    		$this->_view->menuName = $cRequest->getPost('menuName');
			$this->_view->sortOrder = $cRequest->getPost('sortOrder');
    		
    	    // perform menu add form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'menuName' =>array('title'=>'Menu name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
                    'sortOrder' =>array('title'=>'Sort Order', 'validation'=>array('required'=>true, 'type'=>'integer', 'maxLength'=>3)),
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
                    $menus = new Menus();
                    if(!is_null($menus)){                    
                        $menus->name = $this->_view->menuName;
                        if($menus->exists('name = :name', array(':name'=>$this->_view->menuName))){
                            $msg = 'Menu "'.$this->_view->menuName.'" already exists! Please re-enter.';
                            $msgType = 'error';
                            $this->_view->errorField = 'menuName';
                        }elseif($menus->save()){
                            $msg = 'New menu "'.$this->_view->menuName.'" has been successfully added!';
                            $msgType = 'success';
                        }else{
                            $msg = 'An error occurred while insertion new menu! Please re-enter.';
                            $msgType = 'error';
                            $this->_view->errorField = 'menuName';
                        }
                    }else{
                        $this->redirect('menus/index/msg/wrong-id');
                    }
                }
    		}
    		if(!empty($msg)){
    			$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
                $this->_view->render('menus/add');
    		}
    	}else{
            $this->redirect('menus/add');    
        }            	
    }
    
    
    public function editAction($menuId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();	
    	
        $this->_view->setMetaTags('title', 'Edit Menu | '.$this->_view->cmsName);
		$this->_view->activeLink = 'edit_menu';

    	$menus = Menus::model()->findByPk($menuId);
        if(!$menus){
    		$this->redirect('menus/index/msg/wrong-id');
        }
        
        $this->_view->menuId = $menus->id;
        $this->_view->menuName = $menus->name;
		$this->_view->sortOrder = $menus->sort_order;                    
    	$this->_view->render('menus/edit');
    }

    public function updateAction($menuId = null)
    {
        // block access to this action for not-logged users
		CAuth::handleLogin();
        
        $this->_view->setMetaTags('title', 'Edit Menu | '.$this->_view->cmsName);
		$this->_view->activeLink = 'edit_menu';
        $cRequest = A::app()->getRequest();
    	
        if($cRequest->getPost('act') == 'send'){

    		$this->_view->menuId = $cRequest->getPost('menuId');
    		$this->_view->menuName = $cRequest->getPost('menuName');
			$this->_view->sortOrder = $cRequest->getPost('sortOrder');
    		
    	    // perform menu edit form validation
            $result = CWidget::create('CFormValidation', array(
                'fields'=>array(
                    'menuName'  =>array('title'=>'Menu name', 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>50)),
                    'sortOrder' =>array('title'=>'Sort Order', 'validation'=>array('required'=>true, 'type'=>'integer', 'maxLength'=>3)),
                )
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
                    $menus = Menus::model()->findByPk($this->_view->menuId);
                    if(!is_null($menus)){
                        $menus->name = $this->_view->menuName;
						$menus->sort_order = $this->_view->sortOrder;    
                        if($menus->exists('name = :name AND id != :id', array(':name'=>$this->_view->menuName, ':id'=>$this->_view->menuId))){
                            $msg = 'Menu "'.$this->_view->menuName.'" already exists! Please re-enter.';
                            $msgType = 'error';
                            $this->_view->errorField = 'menuName';
                        }elseif($menus->save()){
                            $msg = 'Menu has been successfully updated!';
                            $msgType = 'success';
                        }else{
                            $msg = 'An error occurred while updating the menu! Please re-enter.';
                            $msgType = 'error';
                            $this->_view->errorField = 'menuName';
                        }
                    }else{
                        $this->redirect('menus/index/msg/wrong-id');
                    }
                }
    		}
            
    		if(!empty($msg)){
    			$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button'=>true)));
                $this->_view->render('menus/edit');
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