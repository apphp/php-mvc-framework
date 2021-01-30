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
        $this->_view->setMetaTags('title', 'Account | '.$settings->metatag_title);
        $this->_view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->_view->setMetaTags('description', $settings->metatag_description);
        $this->_view->cmsName   = $settings->site_name;
        $this->_view->cmsSlogan = $settings->slogan;
        $this->_view->cmsFooter = $settings->footer;

        $this->_view->activeLink    = 'home';
        $this->_view->viewRightMenu = false;
        $this->_view->errorField    = '';
        $this->_view->actionMessage = '';

        // prepare list of roles that the logged admin can deal with
        $allRolesList = [];
        $rolesList    = [];

        if (CAuth::isLoggedInAs('owner')) {
            $rolesList = ['mainadmin' => 'mainadmin', 'admin' => 'admin'];
        } elseif (CAuth::isLoggedInAs('mainadmin')) {
            $rolesList = ['admin' => 'admin'];
        }
        $roles = [
            ['code' => 'owner', 'name' => 'Owner'],
            ['code' => 'mainadmin', 'name' => 'Main Admin'],
            ['code' => 'admin', 'name' => 'Admin'],
        ];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                $allRolesList[$role['code']] = $role['name'];
                if (in_array($role['code'], $rolesList)) {
                    $rolesList[$role['code']] = $role['name'];
                }
            }
        }
        $this->_view->rolesListStr = "'".implode("','", array_keys($rolesList))."'";
        $this->_view->rolesList    = $rolesList;
        $this->_view->allRolesList = $allRolesList;

        $this->_view->dateTimeFormat = 'm F, Y H:i:s';
    }

    public function indexAction()
    {
        $this->redirect('admins/view');
    }

    /**
     * View admins action handler
     *
     * @param  string  $msg
     */
    public function viewAction($msg = '')
    {
        $this->_view->activeLink = 'admins';
        switch ($msg) {
            case 'added':
                $message = A::t('core', 'The adding operation has been successfully completed!');
                break;
            case 'updated':
                $message = A::t('core', 'The updating operation has been successfully completed!');
                break;
            default:
                $message = '';
        }
        if ( ! empty($message)) {
            $this->_view->actionMessage = CWidget::create('CMessage', ['success', $message, ['button' => true]]);
        }
        $this->_view->render('admins/view');
    }

    /**
     * Edit admin action handler
     *
     * @param  int  $id  The admin id
     */
    public function editAction($id = 0)
    {
        $this->_view->activeLink = 'admins';

        //$this->_view->activeLink = 'admins';
        $admin = Admins::model()->findByPk((int)$id);
        if ( ! $admin) {
            $this->redirect('backend/index');
        }
        $this->_view->isMyAccount = ($admin->id == $this->_loggedId ? true : false);
        if ($this->_view->isMyAccount == true) {
            $this->_view->activeLink = 'myAccount';
        }

        // allow access to edit other admins only to site owner or main admin
        if ( ! $this->_view->isMyAccount
            &&
            ! CAuth::isLoggedInAs('owner', 'mainadmin')
            &&
            ! in_array($admin->role, array_keys($this->_view->rolesList))
        ) {
            $this->redirect('backend/index');
        }
        $this->_view->admin          = $admin;
        $this->_view->password       = '';
        $this->_view->passwordRetype = '';

        $this->_view->render('admins/edit');
    }


    /**
     * My Account action handler
     * Calls the editAction with id of logged admin.
     */
    public function myAccountAction()
    {
        $this->_view->activeLink = 'myAccount';
        $this->editAction($this->_loggedId);
    }

    /*
     * Add new admin action handler
     */
    public function addAction()
    {
        // allow access only to site owner or main admin
        if ( ! CAuth::isLoggedInAs('owner', 'mainadmin')) {
            $this->redirect('backend/index');
        }
        $this->_view->render('admins/add');
    }

    /**
     * Delete admin action handler
     *
     * @param  int  $id  The admin id
     */
    public function deleteAction($id = 0)
    {
        // allow access only to site owner or main admin
        if ( ! CAuth::isLoggedInAs('owner', 'mainadmin')) {
            $this->redirect('backend/index');
        }

        $msg     = '';
        $msgType = '';

        $admin = Admins::model()->findByPk((int)$id);
        if ( ! $admin) {
            $this->redirect('admins/view');
        }

        // check if this delete operation is allowed
        if ( ! in_array($admin->role, array_keys($this->_view->rolesList))) {
            $msg     = A::t('core', 'Operation Blocked Error Message');
            $msgType = 'error';
            // delete the admin
        } elseif ($admin->delete()) {
            $msg     = A::t('core', 'Deleting operation has been successfully completed!');
            $msgType = 'success';
        } else {
            if (APPHP_MODE == 'demo') {
                $msg = CDatabase::init()->getErrorMessage();
            } else {
                $msg = A::t('core', 'An error occurred while deleting the record!');
            }
            $msgType = 'error';
        }
        if ( ! empty($msg)) {
            $this->_view->actionMessage = CWidget::create('CMessage', [$msgType, $msg, ['button' => true]]);
        }
        $this->_view->render('admins/view');
    }

}