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
		$this->_view->setMetaTags('title', $settings->metatag_title);
		$this->_view->setMetaTags('keywords', $settings->metatag_keywords);
		$this->_view->setMetaTags('description', $settings->metatag_description);
		$this->_view->blogName = $settings->blog_name;
		$this->_view->blogSlogan = $settings->slogan;
		$this->_view->blogFooter = $settings->footer;
		
		$this->_view->activeLink = 'edit_category';
		$this->_view->viewRightMenu = false;
		$this->_view->actionMessage = '';
		$this->_view->errorField = '';
		
	}
	
	public function viewAction($categoryId = 0)
	{
		$this->_view->viewOnePost = false;
		$this->_view->activeLink = '';
		$this->_view->viewRightMenu = true;
		
		$categories = Categories::model()->findByPk($categoryId);
		$catName = (!is_null($categories)) ? $categories->name : '';
		
		$settings = Settings::model()->findByPk(1);
		$this->_view->postMaxChars = ($settings->post_max_chars != null) ? $settings->post_max_chars : -1;
		$this->_view->setMetaTags('title', $catName . ' | ' . $settings->metatag_title);
		
		//All posts from the selected category
		$postsModel = Posts::model();
        if ( ! $postsModel->count('category_id = :category_id', [':category_id' => $categoryId])) {
            $msgType = 'warning';
			$msg = (!empty($catName)) ? 'There are still no posts in category <b>' . $catName . '</b>.' : 'Wrong parameter passed, please try again later.';
		} else {
			
			// prepare pagination vars
			$this->_view->targetPage = 'categories/view/id/' . $categoryId;
			$this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
			$this->_view->pageSize = '5';
            $this->_view->totalRecords = Posts::model()->count(
                ['condition' => 'category_id = :category_id',],
                [':category_id' => $categoryId]
            );

            $msgType = 'info';
			$msg = 'Category: ' . $catName;

            $this->_view->posts = $postsModel->findAll(
                [
                    'condition' => 'category_id = :category_id',
                    'limit'     => (($this->_view->currentPage - 1) * $this->_view->pageSize).', '
                        .$this->_view->pageSize,
                    'order'     => 'post_datetime DESC',
                ],
                [':category_id' => $categoryId]
            );
        }
        $this->_view->mainText = CWidget::create('CMessage', [$msgType, $msg, ['button' => false]]);
        $this->_view->render('categories/view');
	}
	
	public function indexAction($msg = '')
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->setMetaTags('title', 'Categories | ' . $this->_view->blogName);
		$this->_view->activeLink = 'edit_category';
		$msgType = $msgText = '';
		
		if (!empty($msg)) {
			if ($msg == 'delete_success') {
                $msgText = 'Category has been successfully deleted!';
				$msgType = 'success';
			} elseif ($msg == 'delete_error') {
                $msgText = 'An error occurred while deleting the category!';
				$msgType = 'error';
			} elseif ($msg == 'delete_demo') {
                $msgText = '<b>:(</b> Sorry, but delete operation is blocked in DEMO version!';
				$msgType = 'warning';
			} elseif ($msg == 'wrong-id') {
                $msgText = 'Wrong parameter passed! Check category ID.';
				$msgType = 'error';
			}
            if ( ! empty($msgType)) {
                $this->_view->actionMessage = CWidget::create('CMessage', [$msgType, $msgText, ['button' => true]]);
            }
        }

        // prepare pagination vars
		$this->_view->targetPage = 'categories/index';
		$this->_view->currentPage = A::app()->getRequest()->getQuery('page', 'integer', 1);
		$this->_view->pageSize = '15';
		$this->_view->totalRecords = Categories::model()->count();

        if ( ! $this->_view->currentPage) {
            $this->_view->actionMessage = CWidget::create('CMessage',
              [
                  'error',
                  'Wrong parameter passed! Please try again later.',
                  ['button' => true]
              ]
            );
        } else {
            $this->_view->categories = Categories::model()->findAll(
                [
                    'limit' => (($this->_view->currentPage - 1) * $this->_view->pageSize).', '.$this->_view->pageSize,
                    'order' => 'id ASC',
                ]
            );
        }

        $this->_view->render('categories/index');
	}
	
	public function addAction()
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->setMetaTags('title', 'Add Category | ' . $this->_view->blogName);
		$this->_view->activeLink = 'add_category';
		$this->_view->render('categories/add');
	}
	
	public function insertAction()
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->setMetaTags('title', 'Add Category | ' . $this->_view->blogName);
		$this->_view->activeLink = 'add_category';
		$cRequest = A::app()->getRequest();
		
		if ($cRequest->getPost('act') == 'send') {
			
			$this->_view->categoryName = $cRequest->getPost('categoryName');
			
			// perform category add form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'categoryName' => array('title' => 'Category name', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 50)),
				),
			));
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$this->_view->errorField = $result['errorField'];
				$msgType = 'validation';
			} else {
				if (APPHP_MODE == 'demo') {
					$msg = '<b>:(</b> Sorry, but insert operation is blocked in DEMO version!';
					$msgType = 'warning';
				} else {
					$categories = new Categories();
					if (!is_null($categories)) {
						$categories->name = $this->_view->categoryName;
						$categories->posts_count = 0;
						if ($categories->exists('name = :name', array(':name' => $this->_view->categoryName))) {
							$msg = 'Category "' . $this->_view->categoryName . '" already exists! Please re-enter.';
							$msgType = 'error';
							$this->_view->errorField = 'categoryName';
						} elseif ($categories->save()) {
							$msg = 'New category "' . $this->_view->categoryName . '" has been successfully added!';
							$msgType = 'success';
						} else {
							$msg = 'An error occurred while insertion new category! Please re-enter.';
							$msgType = 'error';
							$this->_view->errorField = 'categoryName';
						}
					} else {
						$this->redirect('categories/index/msg/wrong-id');
					}
				}
			}
			if (!empty($msg)) {
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
				$this->_view->render('categories/add');
			}
		} else {
			$this->redirect('categories/add');
		}
	}
	
	
	public function editAction($categoryId = null)
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->setMetaTags('title', 'Edit Category | ' . $this->_view->blogName);
		$this->_view->activeLink = 'edit_category';
		
		$categories = Categories::model()->findByPk($categoryId);
		if (!$categories) {
			$this->redirect('categories/index/msg/wrong-id');
		}
		
		$this->_view->categoryId = $categories->id;
		$this->_view->categoryName = $categories->name;
		$this->_view->render('categories/edit');
	}
	
	public function updateAction($categoryId = null)
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		$this->_view->setMetaTags('title', 'Edit Category | ' . $this->_view->blogName);
		$this->_view->activeLink = 'edit_category';
		$cRequest = A::app()->getRequest();
		
		if ($cRequest->getPost('act') == 'send') {
			
			$this->_view->categoryId = $cRequest->getPost('categoryId');
			$this->_view->categoryName = $cRequest->getPost('categoryName');
			
			// perform category edit form validation
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'categoryName' => array('title' => 'Category name', 'validation' => array('required' => true, 'type' => 'any', 'maxLength' => 50)),
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
					$categories = Categories::model()->findByPk($this->_view->categoryId);
					if (!is_null($categories)) {
						$categories->name = $this->_view->categoryName;
						if ($categories->exists('name = :name AND id != :id', array(':name' => $this->_view->categoryName, ':id' => $this->_view->categoryId))) {
							$msg = 'Category "' . $this->_view->categoryName . '" already exists! Please re-enter.';
							$msgType = 'error';
							$this->_view->errorField = 'categoryName';
						} elseif ($categories->save()) {
							$msg = 'Category has been successfully updated!';
							$msgType = 'success';
						} else {
							$msg = 'An error occurred while updating the category! Please re-enter.';
							$msgType = 'error';
							$this->_view->errorField = 'categoryName';
						}
					} else {
						$this->redirect('categories/index/msg/wrong-id');
					}
				}
			}
			
			if (!empty($msg)) {
				$this->_view->actionMessage = CWidget::create('CMessage', array($msgType, $msg, array('button' => true)));
				$this->_view->render('categories/edit');
			}
		} else {
			$this->redirect('categories/index');
		}
	}
	
	
	public function deleteAction($categoryId)
	{
		// block access to this controller for not-logged users
		if (!CAuth::isLoggedIn()) {
			$this->redirect('index/index');
		}
		
		if (APPHP_MODE == 'demo') {
			$this->redirect('categories/index/msg/delete_demo');
		} else {
			if (Categories::model()->deleteByPk($categoryId)) {
				//Posts::model()->deleteAll('category_id = :category_id', array(':category_id' => $categoryId));
				//Posts::model()->deleteAll('category_id = '.(int)$categoryId);
				$this->redirect('categories/index/msg/delete_success');
			} else {
				$this->redirect('categories/index/msg/delete_error');
			}
		}
	}
	
}