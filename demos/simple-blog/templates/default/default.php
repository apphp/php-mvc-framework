<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?php echo CHtml::encode($this->_pageDescription); ?>" />
    <title><?php echo CHtml::encode($this->_pageTitle); ?></title>
    
    <base href="<?php echo A::app()->getRequest()->getBaseUrl(); ?>" />

    <?php echo CHtml::cssFile("templates/default/css/main.css"); ?>
    
	<?php echo CHtml::scriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'); ?>
	<?php echo CHtml::scriptFile('templates/default/js/main.js'); ?>
</head>
<body>
    <header>
    	<nav>
    		<div class="header_left">
    			<div class="header_title">
                <?php
                    if(CAuth::isLoggedIn()){
                        echo CHtml::label($this->blogName);    
                    }else{
                        echo CHtml::link($this->blogName, (CAuth::isLoggedIn() ? 'authors/index' :  'index/index'), array('class'=>'header_title'));
                    }                
                ?>
                </div>
		       	<div class="header_slogan"><?php echo CAuth::isLoggedIn() ? 'Admin Panel' :  $this->blogSlogan; ?></div>
		   	</div>
    		<div class="header_right">                
	    		<?php
                    if(CAuth::isLoggedIn() && isset($viewRightMenu)){
                        echo BlogMenu::init()->adminTopMenu($viewRightMenu);
                    }                
                ?>
    		</div>        	
        </nav>
    </header>
    <section>       
	<?php
        if(CAuth::isLoggedIn() && isset($activeLink)){
            BlogMenu::init()->adminLeftMenu($activeLink);    
        }        
        
        $centerContent = A::app()->view->getContent();
        $contenClass = '';
        
        if(isset($viewRightMenu) && $viewRightMenu){
            echo BlogMenu::init()->blogSideMenu($viewRightMenu);
            $contenClass = ' content-main';
        }else if(CAuth::isLoggedIn() && (A::app()->getResponseCode() != '404')){
            $contenClass = ' content-admin';
        }
        
        echo '<div class="content'.$contenClass.'">'.$centerContent.'</div>';
    ?>
    </section>
    
    <footer>
        <div class="footer_right"><?php echo CAuth::isLoggedIn() ? '' : '<a href="login/index">Author Login</a>'; ?></div>
       	<div class="footer_left"><?php echo $this->blogFooter.', '.A::powered(); ?></div> 
   	</footer>    
</body>
</html>