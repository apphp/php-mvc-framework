<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>"/>
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>"/>
	<title><?= CHtml::encode($this->_pageTitle); ?></title>

	<base href="<?= A::app()->getRequest()->getBaseUrl(); ?>"/>
	
	<?= CHtml::cssFile('templates/setup/css/main.css'); ?>
	
	<?= CHtml::scriptFile('//code.jquery.com/jquery-1.11.3.min.js'); ?>
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
			<b><?= $this->_programName; ?></b><br>
			version: <?= $this->_programVersion; ?>
		</div>
		
		<?php
		CWidget::create('CMenu', array(
			'type' => 'vertical',
			'items' => array(
				array('label' => '1. Server Requirements', 'url' => 'setup/index', 'readonly' => true),
				array('label' => '2. Database Settings', 'url' => 'setup/database', 'readonly' => true),
				array('label' => '3. Administrator Account', 'url' => 'setup/administrator', 'readonly' => true),
				array('label' => '4. Ready to Install', 'url' => 'setup/ready', 'readonly' => true),
				array('label' => '5. Completed', 'url' => 'setup/completed', 'readonly' => true),
			),
			'selected' => $this->_activeMenu,
			'return' => false,
		));
		?>
	</aside>
	<article>
		<?= A::app()->view->getContent(); ?>
	</article>
</section>
<footer>
	<p class="copyright">Copyright &copy; <?= date('Y'); ?> <?= $this->_programName; ?></p>
	<p class="powered"><?= A::powered(); ?></p>
</footer>
</body>
</html>