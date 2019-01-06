<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>" />
    <meta name="author" content="ApPHP Company - Advanced Power of PHP">
    <meta name="generator" content="ApPHP MVC Framework - Setup Wizard">
    <title><?= CHtml::encode($this->_pageTitle); ?></title>

    <base href="<?= A::app()->getRequest()->getBaseUrl(); ?>" />
    <link rel="shortcut icon" href="templates/setup/images/favicon.ico" />     
    <?= CHtml::cssFile("templates/setup/css/main.css"); ?>
	<?= CHtml::scriptFile('http://code.jquery.com/jquery-1.8.3.min.js'); ?>
</head>
<body>  
<div id="container">
    <header>        
        <nav>
            <?= A::t('setup', 'Setup Wizard'); ?><br>
            <small><?= A::t('setup', 'This wizard will guide you through the installation process'); ?></small>
        </nav>
    </header>
    <section>
        <aside>
            <div>
                <b><?= $this->_programName; ?></b><br>
                <?= A::t('setup', 'version'); ?>: <?= $this->_programVersion; ?>
            </div>            
    
            <?php
                CWidget::create('CMenu', array(
                    'type'=>'vertical',					
                    'items'=>array(
                        array('label'=>'1. '.A::t('setup', 'General'), 'url'=>'setup/index', 'readonly'=>true),
                        array('label'=>'2. '.A::t('setup', 'Check Requirements'), 'url'=>'setup/requirements', 'readonly'=>true),
                        array('label'=>'3. '.A::t('setup', 'Database Settings'), 'url'=>'setup/database', 'readonly'=>true),
                        array('label'=>'4. '.A::t('setup', 'Administrator Account'), 'url'=>'setup/administrator', 'readonly'=>true),
                        array('label'=>'5. '.A::t('setup', 'Ready to Install'), 'url'=>'setup/ready', 'readonly'=>true),
                        array('label'=>'6. '.A::t('setup', 'Completed'), 'url'=>'setup/completed', 'readonly'=>true),
                    ),
                    'selected'=>$this->_activeMenu,
                    'return'=>false
                ));
            ?>
        </aside>
        <article>
            <?= A::app()->view->getContent(); ?>
        </article>
    </section>    
    <footer>
        <p class="copyright"><?= A::t('setup', 'Copyright'); ?> &copy; <?= date('Y'); ?> <?= $this->_programName; ?></p>
        <p class="powered"><?= A::powered(); ?></p>
    </footer>
</div>    
</body>
</html>