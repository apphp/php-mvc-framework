<?php
/**
* AdminsController
*
* PUBLIC:                  PRIVATE
* -----------              ------------------
* __construct              
* indexAction
* editAction
* updateAction
* addAction
* deleteAction
*/
class AdminsController extends CController
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
        $this->view->cmsName = $settings->site_name;
        $this->view->cmsSlogan = $settings->slogan;
        $this->view->cmsFooter = $settings->footer;

		$this->view->activeLink = 'home';
		$this->view->viewRightMenu = false;
		$this->view->errorField = '';
		$this->view->actionMessage = '';

        // prepare list of roles that the logged admin can deal with
        $allRolesList = array(); 
        $rolesList = array();
		
        if(CAuth::isLoggedInAs('owner')){
        	$rolesList = array('mainadmin'=>'mainadmin', 'admin'=>'admin');
        }else if(CAuth::isLoggedInAs('mainadmin')){
        	$rolesList = array('admin'=>'admin');
        }        	        	
        $roles = array(
			array('code'=>'owner', 'name'=>'Owner'),
			array('code'=>'mainadmin', 'name'=>'Main Admin'),
			array('code'=>'admin', 'name'=>'Admin')
		);
		if(is_array($roles)){
        	foreach($roles as $role){
	        	$allRolesList[$role['code']] = $role['name'];
	        	if(in_array($role['code'], $rolesList)){
	        		$rolesList[$role['code']] = $role['name'];
	        	}
	        }
		}
        $this->view->rolesListStr = "'".implode("','", array_keys($rolesList))."'";
        $this->view->rolesList = $rolesList;
        $this->view->allRolesList = $allRolesList;
		
		$this->view->dateTimeFormat = 'm F, Y H:i:s';
	}

   	public function indexAction()
   	{
		$this->redirect('admins/view');
   	}
	
    /**
     * View admins action handler
     * @param string $msg 
     */
    public function viewAction($msg = '')
    {
		$this->view->activeLink = 'admins';
        switch($msg){
        	case 'add': 
				$message = A::t('core', 'The adding operation has been successfully completed!');
				break;						
        	case 'update': 
				$message = A::t('core', 'The updating operation has been successfully completed!');
				break;						
			default:
				$message = '';						
        }
        if(!empty($message)){
    		$this->view->actionMessage = CWidget::create('CMessage', array('success', $message, array('button'=>true)));
    	}
        $this->view->render('admins/view');
    }

    /**
     * Edit admin action handler
     * @param int $id The admin id 
     */
    public function editAction($id = 0)
    {
		$this->view->activeLink = 'admins';
		
		//$this->view->activeLink = 'admins';
		$admin = Admins::model()->findByPk((int)$id);
    	if(!$admin){
	  		$this->redirect('backend/index');
    	}
    	$this->view->isMyAccount = ($admin->id == $this->_loggedId ? true : false);    	

    	// allow access to edit other admins only to site owner or main admin
        if(!$this->view->isMyAccount && 
        		!CAuth::isLoggedInAs('owner', 'mainadmin') && 
        		!in_array($admin->role, array_keys($this->view->rolesList))){
        	$this->redirect('backend/index');
        }
        $this->view->admin = $admin;
    	$this->view->password = '';
    	$this->view->passwordRetype = '';
       
        $this->view->render('admins/edit');
    }


    /**
     * My Account action handler
     * Calls the editAction with id of logged admin.
     */
	public function myAccountAction()
	{
		$this->view->activeLink = 'myAccount';
		$this->editAction($this->_loggedId);		
    }
 
    /*
     * Add new admin action handler
     */
    public function addAction()
    {
        // allow access only to site owner or main admin
        if(!CAuth::isLoggedInAs('owner', 'mainadmin')){
        	$this->redirect('backend/index');
        }
        $this->view->render('admins/add');
    }

    /**
     * Delete admin action handler
     * @param int $id The admin id 
     */
    public function deleteAction($id = 0)
    {
        // allow access only to site owner or main admin
        if(!CAuth::isLoggedInAs('owner', 'mainadmin')){
        	$this->redirect('backend/index');
        }
    	    		
    	$msg = '';
    	$errorType = '';
    
    	$admin = Admins::model()->findByPk((int)$id);
    	if(!$admin){
    		$this->redirect('admins/view');
    	}
    	
    	// check if this delete operation is allowed
    	if(!in_array($admin->role, array_keys($this->view->rolesList))){
    		$msg = A::t('core', 'Operation Blocked Error Message');
    		$errorType = 'error';
    	// delete the admin
    	}else if($admin->delete()){
        	$msg = A::t('core', 'Deleting operation has been successfully completed!');
			$errorType = 'success';
    	}else{
			if(APPHP_MODE == 'demo'){
				$msg = CDatabase::init()->getErrorMessage();
		   	}else{
				$msg = A::t('core', 'An error occurred while deleting the record!');	
		   	}
    		$errorType = 'error';
    	}
    	if(!empty($msg)){
    		$this->view->actionMessage = CWidget::create('CMessage', array($errorType, $msg, array('button'=>true)));
    	}
    	$this->view->render('admins/view');
    }
	
}