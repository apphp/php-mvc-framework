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
		$this->_view->setMetaTags('title', $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		$this->_view->cmsName = $settings->site_name;
		$this->_view->cmsSlogan = $settings->slogan;
		$this->_view->cmsFooter = $settings->footer;
		
		$this->_view->activeLink = 'edit_page';
		$this->_view->viewRightMenu = false;
		
		$this->_view->actionMessage = '';
		$this->_view->errorField = '';
		
		$this->_view->isHomePage = 0;
		$this->_view->headerText = '';
		$this->_view->linkText = '';
		$this->_view->menuId = '';
		$this->_view->pageText = '';
	}
	
	public function viewAction($pageId = null)
	{
		$pages = Pages::model();
		$settings = Settings::model()->findByPk(1);
		$this->_view->activeLink = '';
		$this->_view->viewRightMenu = true;
		$this->_view->isHomePage = false;
		
		//The pages list
		if (empty($pageId)) {
			$viewOnePage = false;
			$result = null;
			$this->_view->isHomePage = true;
			
			// prepare pagination vars
			$this->_view->targetPage = 'pages/view/';
			$this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$this->_view->pageSize = '5';
			
			if (!$this->_view->currentPage) {
				$msg = 'Wrong parameter passed! Please try again later.';
				$msgType = 'error';
			} else {
				$result = $pages->findAll('is_homepage = 1');
			}
		} else {
			$viewOnePage = true;
			$result = $pages->findAll(CConfig::get('db.prefix') . 'pages.id = :id', array(':id' => $pageId));
		}
		$this->_view->viewOnePage = $viewOnePage;
		
		if (!$result) {
			$msg = (!empty($msg)) ? $msg : 'There are still no pages.';
			$msgType = (!empty($msgType)) ? $msgType : 'info';
			$this->_view->mainText = CWidget::create('CMessage', array($msgType, $msg, array('button' => false)));
		} else {
			$this->_view->mainText = '';
			if ($viewOnePage) {
				// meta tags specific for the page
				if (!empty($result[0]['metatag_title'])) {
					$this->_view->setMetaTags('title', $result[0]['header_text'] . ' | ' . $result[0]['metatag_title']);
				}
				if (!empty($result[0]['metatag_keywords'])) {
					$this->_view->setMetaTags('keywords', $result[0]['metatag_keywords']);
				}
				if (!empty($result[0]['metatag_description'])) {
					$this->_view->setMetaTags('description', $result[0]['metatag_description']);
				}
			}
			$this->_view->pages = $result;
		}
		
		$this->_view->render('pages/view');
	}
	
	public function indexAction($msg = '')
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$this->_view->setMetaTags('title', 'Pages | ' . $this->_view->cmsName);
		$this->_view->activeLink = 'edit_page';
		
		if (!empty($msg)) {
			if ($msg == 'added') {
				$msg_text = 'New page has been successfully added!';
				$msgType = 'success';
			} elseif ($msg == 'deleted') {
				$msg_text = 'Page has been successfully deleted!';
				$msgType = 'success';
			} elseif ($msg == 'delete_error') {
				$msg_text = 'An error occurred while deleting the page!';
				$msgType = 'error';
			} elseif ($msg == 'delete_homepage_error') {
				$msg_text = 'You cannot delete Homepage!';
				$msgType = 'error';
			} elseif ($msg == 'delete_demo') {
				$msg_text = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
				$msgType = 'warning';
			} elseif ($msg == 'wrong-id') {
				$msg_text = 'Wrong parameter passed! Check page ID.';
				$msgType = 'error';
			}
			if (!empty($msg_text)) $this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg_text, array('button' => true)));
		}
		
		// prepare pagination vars
		$this->_view->targetPage = 'pages/index';
		$this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
		$this->_view->pageSize = '15';
		$this->_view->totalRecords = Pages::model()->count();
		
		if (!$this->_view->currentPage) {
			$this->_view->actionMessage = CWidget::create('CMessage', array('error', 'Wrong parameter passed! Please try again later.', array('button' => true)));
		} else {
			$this->_view->pages = Pages::model()->findAll(array(
				'limit' => (($this->_view->currentPage - 1) * $this->_view->pageSize) . ', ' . $this->_view->pageSize,
				'order' => 'created_at DESC',
			));
		}
		
		$this->_view->render('pages/index');
	}
	
	public function addAction()
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$this->_view->setMetaTags('title', 'Add Page | ' . $this->_view->cmsName);
		$this->_view->activeLink = 'add_page';
		$this->_view->menus = Menus::model()->findAll();
		
		$settings = Settings::model()->findByPk(1);
		$this->_view->metaTagTitle = $settings->metatag_title;
		$this->_view->metaTagKeywords = $settings->metatag_keywords;
		$this->_view->metaTagDescription = $settings->metatag_description;
		$this->_view->isHomePage = 0;
		
		$this->_view->render('pages/add');
	}
	
	public function insertAction()
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$cRequest = A::app()->getRequest();
		$this->_view->setMetaTags('title', 'Add Page | ' . $this->_view->cmsName);
		$this->_view->activeLink = 'add_page';
		$this->_view->menus = Menus::model()->findAll();
		$msg = '';
		$msgType = '';
		
		if ($cRequest->getPost('act') == 'send') {
			
			$this->_view->linkText = $cRequest->getPost('link_text');
			$this->_view->headerText = $cRequest->getPost('header_text');
			$this->_view->menuId = (int)$cRequest->getPost('menuId');
			$this->_view->pageText = $cRequest->getPost('page_text');
			$this->_view->metaTagTitle = $cRequest->getPost('metaTagTitle');
			$this->_view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
			$this->_view->metaTagDescription = $cRequest->getPost('metaTagDescription');
			$this->_view->isHomePage = $cRequest->getPost('is_homepage');
			
			// perform page add form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'link_text' => array('title' => 'Link Text', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 100)),
					'header_text' => array('title' => 'Header', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 255)),
					'page_text' => array('title' => 'Page Text', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 4000)),
					'metaTagTitle' => array('title' => CHtml::encode('Tag <TITLE>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagKeywords' => array('title' => CHtml::encode('Meta tag <KEYWORDS>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagDescription' => array('title' => CHtml::encode('Meta tag <DESCRIPTION>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
				),
			));
			
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$this->_view->errorField = $result['errorField'];
			} else {
				if (APPHP_MODE == 'demo') {
					$msg = '<b>:(</b> Sorry, but insert operation is blocked in DEMO version!';
					$msgType = 'warning';
				} else {
					$pages = new Pages();
					$pages->link_text = $this->_view->linkText;
					$pages->header_text = $this->_view->headerText;
					$pages->menu_id = (int)$this->_view->menuId;
					$pages->is_homepage = (int)$this->_view->isHomePage;
					$pages->page_text = $this->_view->pageText;
					$pages->metatag_title = $this->_view->metaTagTitle;
					$pages->metatag_keywords = $this->_view->metaTagKeywords;
					$pages->metatag_description = $this->_view->metaTagDescription;
					unset($pages->created_at);
					
					if ($pages->save()) {
						$this->redirect('pages/index/msg/added');
					} else {
						$msg = 'An error occurred while adding new page! Please re-enter.';
						$msgType = 'error';
						$this->_view->errorField = 'header_text';
					}
				}
			}
			if (!empty($msg)) {
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
				$this->_view->render('pages/add');
			}
		} else {
			$this->redirect('pages/add');
		}
	}
	
	public function editAction($pageId = null)
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$this->_view->setMetaTags('title', 'Edit Page | ' . $this->_view->cmsName);
		$this->_view->activeLink = 'edit_page';
		$this->_view->menus = Menus::model()->findAll();
		
		$pages = Pages::model()->findByPk($pageId);
		if (!$pages) {
			$this->redirect('pages/index/msg/wrong-id');
		}
		
		$this->_view->pageId = $pages->id;
		$this->_view->linkText = $pages->link_text;
		$this->_view->headerText = $pages->header_text;
		$this->_view->menuId = $pages->menu_id;
		$this->_view->pageText = $pages->page_text;
		$this->_view->metaTagTitle = $pages->metatag_title;
		$this->_view->metaTagKeywords = $pages->metatag_keywords;
		$this->_view->metaTagDescription = $pages->metatag_description;
		$this->_view->isHomePage = $pages->is_homepage;
		
		$this->_view->render('pages/edit');
	}
	
	public function updateAction()
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$cRequest = A::app()->getRequest();
		$this->_view->setMetaTags('title', 'Edit Page | ' . $this->_view->cmsName);
		$this->_view->activeLink = 'edit_page';
		$this->_view->menus = Menus::model()->findAll();
		$msg = '';
		$msgType = '';
		
		$pages = Pages::model();
		
		if ($cRequest->getPost('act') == 'send') {
			
			$this->_view->pageId = (int)$cRequest->getPost('pageId');
			$this->_view->linkText = $cRequest->getPost('link_text');
			$this->_view->headerText = $cRequest->getPost('header_text');
			$this->_view->menuId = (int)$cRequest->getPost('menuId');
			$this->_view->pageText = $cRequest->getPost('page_text');
			$this->_view->metaTagTitle = $cRequest->getPost('metaTagTitle');
			$this->_view->metaTagKeywords = $cRequest->getPost('metaTagKeywords');
			$this->_view->metaTagDescription = $cRequest->getPost('metaTagDescription');
			$this->_view->isHomePage = $cRequest->getPost('is_homepage');
			
			// perform page edit form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'link_text' => array('title' => 'Link Text', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 100)),
					'header_text' => array('title' => 'Header', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 255)),
					'page_text' => array('title' => 'Page Text', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 4000)),
					'metaTagTitle' => array('title' => CHtml::encode('Tag <TITLE>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagKeywords' => array('title' => CHtml::encode('Meta tag <KEYWORDS>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
					'metaTagDescription' => array('title' => CHtml::encode('Meta tag <DESCRIPTION>'), 'validation' => array('required' => false, 'type' => 'any', 'maxLength' => 250)),
				),
			));
			
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$msgType = 'validation';
				$this->_view->errorField = $result['errorField'];
			} else {
				if (APPHP_MODE == 'demo') {
					$msg = '<b>:(</b> Sorry, but update operation is blocked in DEMO version!';
					$msgType = 'warning';
				} else {
					$pages = Pages::model()->findByPk($this->_view->pageId);
					$pages->menu_id = $this->_view->menuId;
					$pages->link_text = $this->_view->linkText;
					$pages->header_text = $this->_view->headerText;
					$pages->page_text = $this->_view->pageText;
					$pages->metatag_title = $this->_view->metaTagTitle;
					$pages->metatag_keywords = $this->_view->metaTagKeywords;
					$pages->metatag_description = $this->_view->metaTagDescription;
					$pages->is_homepage = (int)$this->_view->isHomePage;
					unset($pages->created_at);
					
					if ($pages->save()) {
						$msg = 'Page has been successfully updated!';
						$msgType = 'success';
					} else {
						$msg = 'An error occurred while updating new page! Please re-enter.';
						$msgType = 'error';
						$this->_view->errorField = 'header';
					}
				}
			}
			if (!empty($msg)) {
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
			}
			$this->_view->render('pages/edit');
		} else {
			$this->redirect('pages/index');
		}
	}
	
	public function deleteAction($pageId)
	{
		// block access to this action for not-logged users
		CAuth::handleLogin();
		
		$this->_view->activeLink = 'edit_page';
		$msg = '';
		$msgType = '';
		
		if (APPHP_MODE == 'demo') {
			$this->redirect('pages/index/msg/delete_demo');
		} else {
			$pages = Pages::model()->findByPk($pageId);
			if ($pages->is_homepage == 1) {
				$msg = 'delete_homepage_error';
				$msgType = 'error';
			} elseif ($pages && $pages->delete()) {
				$msg = 'deleted';
				$msgType = 'success';
			} else {
				$msg = 'delete_error';
				$msgType = 'error';
			}
			
			$this->redirect('pages/index/msg/' . $msg);
		}
	}
	
}