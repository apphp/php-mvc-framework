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
            'type'=>'horizontal',
            'class'=>'user_menu',
            'items'=>array(
                array('label'=>($viewRightMenu) ? 'Back to Admin Panel' : 'Home', 'url'=>'backend/index'),
                array('label'=>'Logout', 'url'=>'login/logout'),
            ),
            'return'=>true
        ));
        
        $admin = Admins::model()->findByPk(1);
        
        return $output;        
    }

    public function adminLeftMenu($activeLink = '')
    {
        if(!empty($activeLink)){
            $this->view->renderContent('adminLeftMenu');
        }        
    }

    public function cmsSideMenu($viewRightMenu = '')
    {
        $output = '';
        
        if($viewRightMenu){
			$allMenus = Menus::model()->findAll();
			if(!$allMenus){
				CDebug::addMessage('warnings', 'warning', 'No menus have been created yet.');
			}else{
				$output .= '<aside class="right_side">';
				foreach($allMenus as $menu) {
					$output .= '<div class="menus_list">';
					$output .= '<div class="right_menu_header">'.$menu['name'].'</div>';
					$output .= '<div class="right_menu_content">';
					
					$allPages = Pages::model()->findAll('menu_id = '.(int)$menu['id']);
					foreach($allPages as $page){
						$output .= '&bull; <a href="pages/view/id/'.(int)$page['id'].'">'.$page['link_text'].'</a><br>';
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


