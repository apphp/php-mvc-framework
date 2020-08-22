<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->_pageKeywords); ?>" />
	<meta name="description" content="<?php echo CHtml::encode($this->_pageDescription); ?>" />
    <title><?php echo CHtml::encode($this->_pageTitle); ?></title>
    
    <base href="<?php echo A::app()->getRequest()->getBaseUrl(); ?>" />

    <?php echo CHtml::cssFile('templates/setup/css/main.css'); ?>

	<?php echo CHtml::scriptFile('//code.jquery.com/jquery-1.11.3.min.js'); ?>
</head>
<body>
    <header>        
        <nav>
            Setup Wizard<br>
            <small>This wizard will guide you through the installation process</small>
        </nav>
    </header>
    <section>
        <aside>
            <div>
                <b><?php echo $this->_programName; ?></b><br>
                version: <?php echo $this->_programVersion; ?>
            </div>            

            <?php
                CWidget::create(
                    'CMenu',
                    [
                        'type'     => 'vertical',
                        'items'    => [
                            ['label' => '1. Server Requirements', 'url' => 'setup/index', 'readonly' => true],
                            ['label' => '2. Database Settings', 'url' => 'setup/database', 'readonly' => true],
                            ['label' => '3. Administrator Account', 'url' => 'setup/administrator', 'readonly' => true],
                            ['label' => '4. Ready to Install', 'url' => 'setup/ready', 'readonly' => true],
                            ['label' => '5. Completed', 'url' => 'setup/completed', 'readonly' => true],
                        ],
                        'selected' => $this->_activeMenu,
                        'return'   => false
                    ]
                );
            ?>
		</aside>
		<article>
            <?php echo A::app()->view->getContent(); ?>
        </article>
    </section>
    <footer>
        <p class="copyright">Copyright &copy; <?php echo date('Y'); ?> <?php echo $this->_programName; ?></p>
        <p class="powered"><?php echo A::powered(); ?></p>
    </footer>    
</body>
</html>