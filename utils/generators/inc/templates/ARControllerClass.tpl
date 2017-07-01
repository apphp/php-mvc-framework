<?php
/**
 * [CONTROLLER_NAME] controller
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _checkActionAccess
 * indexAction              
 * manageAction
 * addAction
 * editAction
 * deleteAction
 * viewAllAction
 *
 */

class [CONTROLLER_NAME]Controller extends CController
{
    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();

        // block access if the module is not installed
        if(!Modules::model()->exists("code = '[MODULE_CODE]' AND is_installed = 1")){
            if(CAuth::isLoggedInAsAdmin()){
                $this->redirect('modules/index');
            }else{
                $this->redirect('index/index');
            }
        }

        // set backend mode
        Website::setBackend();

        if(CAuth::isLoggedInAsAdmin()){
            // set meta tags according to active [CONTROLLER_NAME_LC]
            Website::setMetaTags(array('title'=>A::t('app', '[CONTROLLER_NAME] Management')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';
            
            $this->_view->tabs = [CONTROLLER_NAME]Component::prepareTab('[TAB_CODE]');
        }
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('[CONTROLLER_NAME_LC]/manage');
    }
    
    /**
     * Manage action handler
     * @param string $msg 
     */
    public function manageAction($msg = '')
    {
        Website::prepareBackendAction('manage', '[PRIVILEGE_CODE]', '[CONTROLLER_NAME_LC]/manage');

        switch($msg){
            case 'added':
                $message = A::t('app', 'Adding operation has been successfully completed!');
                break;
            case 'updated':
                $message = A::t('app', 'Updating operation has been successfully completed!');
                break;
            default:
                $message = '';
        }
		
        if(!empty($message)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array('success', $message, array('button'=>true))
            );
        }

        $this->_view->render('[CONTROLLER_NAME_LC]/manage');        
    }

    /**
     * Add new action handler
     */
    public function addAction()
    {
        Website::prepareBackendAction('add', '[PRIVILEGE_CODE]', '[CONTROLLER_NAME_LC]/manage');

        $this->_view->render('[CONTROLLER_NAME_LC]/add');
    }

    /**
     * Edit [CONTROLLER_NAME_LC] action handler
     * @param int $id 
     */
    public function editAction($id = 0)
    {
        Website::prepareBackendAction('edit', '[PRIVILEGE_CODE]', '[CONTROLLER_NAME_LC]/manage');
        $model = $this->_checkActionAccess($id);
        
        $this->_view->model = $model;
        $this->_view->render('[CONTROLLER_NAME_LC]/edit');
    }

    /**
     * Delete action handler
     * @param int $id  
     */
    public function deleteAction($id = 0)
    {
        Website::prepareBackendAction('delete', '[PRIVILEGE_CODE]', '[CONTROLLER_NAME_LC]/manage');
        $model = $this->_checkActionAccess($id);

        $msg = '';
        $msgType = '';
    
        // check if default
        if($model->is_default){
            $msg = A::t('app', 'Delete Default Alert');
            $msgType = 'error';
        }elseif($model->delete()){
            if($model->getError()){
                $msg = A::t('app', 'Delete Warning Message');
                $msgType = 'warning';
            }else{
                $msg = A::t('app', 'Delete Success Message');
                $msgType = 'success';
            }
        }else{
            if(APPHP_MODE == 'demo'){
                $msg = CDatabase::init()->getErrorMessage();
                $msgType = 'warning';
            }else{
                $msg = A::t('app', 'Delete Error Message');
                $msgType = 'error';
            }
        }
        if(!empty($msg)){
            $this->_view->actionMessage = CWidget::create(
                'CMessage', array($msgType, $msg, array('button'=>true))
            );
        }
        $this->_view->render('[CONTROLLER_NAME_LC]/manage');
    }

    /**
     * View the module on Frontend
     */
    public function viewAllAction()
    {
        // set frontend mode
        Website::setFrontend();
        
        // your code here...
    }

    /**
     * Check if passed record ID is valid
     * @param int $id
     */
    private function _checkActionAccess($id = 0)
    {        
        $model = [MODEL_NAME]::model()->findByPk($id);
        if(!$model){
            $this->redirect('[CONTROLLER_NAME_LC]/manage');
        }
        return $model;
    }    
  
}