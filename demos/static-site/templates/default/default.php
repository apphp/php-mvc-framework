<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?php echo CHtml::encode($this->_pageDescription); ?>" />
    <title><?php echo CHtml::encode($this->_pageTitle); ?></title>
    
    <base href="<?php echo A::app()->getRequest()->getBaseUrl(); ?>" />

    <?php echo CHtml::cssFile('templates/default/css/main.css'); ?>
	
	<?php echo CHtml::scriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'); ?>
	<?php echo CHtml::scriptFile('templates/default/js/main.js'); ?>
</head>
<body>
    <header>
        <nav>
            <?php
                CWidget::create('CMenu', array(
                    'type'=>'horizontal',
                    'items'=>array(
                        array('label'=>'Home', 'url'=>'index/index'),
                        array('label'=>'Our Services', 'url'=>'page/services'),
                        array('label'=>'Free Stuff', 'url'=>'page/stuff'),
                        array('label'=>'Information', 'url'=>'page/about'),
                        array('label'=>'Contact', 'url'=>'page/contact'),
                    ),
                    'selected'=>$this->_activeMenu,
                    'return'=>false
                ));
            ?>
        </nav>
    </header>
    <section>
        <?php
            CWidget::create('CBreadCrumbs', array(
                'links'=>$this->_breadCrumbs,
                'return'=>false
            ));
        ?>        
        <?php echo A::app()->view->getContent(); ?>
    </section>
    <footer>
        <p class="copyright">Copyright &copy; <?php echo date('Y'); ?> Your Site</p>
        <p class="powered"><?php echo A::powered(); ?></p>
    </footer>    
</body>
</html>