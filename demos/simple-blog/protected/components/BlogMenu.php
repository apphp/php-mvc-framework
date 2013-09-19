<?php

class BlogMenu extends CComponent
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
                array('label'=>($viewRightMenu) ? 'Back to Admin Panel' : 'Home', 'url'=>'authors/index'),
                array('label'=>'Logout', 'url'=>'login/logout'),
            ),
            'return'=>true
        ));
        
        $author = Authors::model()->findByPk(1);
        if($author){
            $output .= '<img class="avatar_small" src="templates/default/images/authors/'.$author->avatar_file.'">';
        }
        
        return $output;        
    }

    public function adminLeftMenu($activeLink = '')
    {
        if(!empty($activeLink)){
            $this->view->renderContent('adminLeftMenu');
        }        
    }

    public function blogSideMenu($viewRightMenu = '')
    {
        $output = '';
        
        if($viewRightMenu){
            $author = Authors::model()->findByPk(1);
            $categories = Categories::model();
            
            $output .= '<aside class="right_side">
                <div class="about_me">
                    <div class="right_menu_header">ABOUT ME</div>
                    <div class="right_menu_content">
                        <img class="avatar_about_me" src="templates/default/images/authors/'.$author->avatar_file.'">
                        <div class="about_text">'.$author->about_text.'</div>
                    </div>
                </div>
                <div class="categories_list">
                    <div class="right_menu_header">CATEGORIES</div>
                    <div class="right_menu_content">';
                        
                        // categories box
                        $cats = $categories->findAll();
                        if(!$cats){
                            CDebug::addMessage('warnings', 'warning', 'No categories have been created yet.');
                        }else{
                            foreach($cats as $cat) {
                                $output .= '<a href="categories/view/id/'.$cat['id'].'">'.$cat['name'].' ('.$cat['posts_count'].')</a><br>';
                            }                            
                        }
                        
                    $output .= '</div>
                </div>
            </aside>';
        }
        
        return $output;      
    }
    
}


