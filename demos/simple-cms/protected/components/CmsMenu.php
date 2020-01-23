<?php

class CmsMenu extends CComponent
{
	
	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns the static model of the specified AR class
	 */
	public static function init()
	{
		return parent::init(__CLASS__);
	}
	
	public function adminTopMenu($viewRightMenu = '')
	{
		$output = CWidget::create('CMenu', array(
			'type' => 'horizontal',
			'class' => 'user_menu',
			'items' => array(
				array('label' => ($viewRightMenu) ? 'Back to Admin Panel' : 'Home', 'url' => 'backend/index'),
				array('label' => 'Logout', 'url' => 'login/logout'),
			),
			'return' => true,
		));
		
		$admin = Admins::model()->findByPk(1);
		
		return $output;
	}
	
	public function adminLeftMenu($activeLink = '')
	{
		$output = '';
		
		if (!empty($activeLink)) {
			$output .= '<aside class="left_side">
                General
                <ul>
                    <li><a class="' . (($activeLink == 'home') ? ' active' : '') . '" href="backend/index">Home</a>
                    <li><a class="' . (($activeLink == 'settings') ? ' active' : '') . '" href="settings/edit">Site Settings</a>
                    <li><a href="index/index">Preview</a>
                </ul>
                Accounts Management
                <ul>
                    <li><a class="' . (($activeLink == 'myAccount') ? ' active' : '') . '" href="admins/myAccount">My Account</a>
                    <li><a class="' . (($activeLink == 'admins') ? ' active' : '') . '" href="admins/index">Admins</a>
                </ul>
                Menus Management
                <ul>
                    <li><a class="' . (($activeLink == 'add_menu') ? ' active' : '') . '" href="menus/add">New Menu</a>
                    <li><a class="' . (($activeLink == 'edit_menu') ? ' active' : '') . '" href="menus/index">Menus</a>
                </ul>
                Pages Management
                <ul>
                    <li><a class="' . (($activeLink == 'add_page') ? ' active' : '') . '" href="pages/add">New Page</a>
                    <li><a class="' . (($activeLink == 'edit_page') ? ' active' : '') . '" href="pages/index">Pages</a>
                </ul>
            </aside>';
		}
		return $output;
	}
	
	public function cmsSideMenu($viewRightMenu = '')
	{
		$output = '';
		
		if ($viewRightMenu) {
			$allMenus = Menus::model()->findAll();
			if (!$allMenus) {
				CDebug::addMessage('warnings', 'warning', 'No menus have been created yet.');
			} else {
				$output .= '<aside class="right_side">';
				foreach ($allMenus as $menu) {
					$output .= '<div class="menus_list">';
					$output .= '<div class="right_menu_header">' . $menu['name'] . '</div>';
					$output .= '<div class="right_menu_content">';
					
					$allPages = Pages::model()->findAll('menu_id = ' . (int)$menu['id']);
					foreach ($allPages as $page) {
						$output .= '&bull; <a href="pages/view/id/' . (int)$page['id'] . '">' . $page['link_text'] . '</a><br>';
					}
					$output .= '</div>';
					$output .= '</div>';
				}
				$output .= '</aside>';
			}
		}
		return $output;
	}
	
}


