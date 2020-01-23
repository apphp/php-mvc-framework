<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>"/>
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>"/>
	<meta name="author" content="ApPHP Company - Advanced Power of PHP">
	<meta name="generator" content="ApPHP Static Site">
	<title><?= CHtml::encode($this->_pageTitle); ?></title>

	<base href="<?= A::app()->getRequest()->getBaseUrl(); ?>"/>
	
	<?= CHtml::cssFile('templates/default/css/main.css'); ?>
	
	<?= CHtml::scriptFile('//code.jquery.com/jquery-1.11.3.min.js'); ?>
	<?= CHtml::scriptFile('templates/default/js/main.js'); ?>
</head>
<body>
<header>
	<nav>
		<?php
		CWidget::create('CMenu', array(
			'type' => 'horizontal',
			'items' => array(
				array('label' => 'Home', 'url' => 'index/index'),
				array('label' => 'Our Services', 'url' => 'page/services'),
				array('label' => 'Free Stuff', 'url' => 'page/stuff'),
				array('label' => 'Information', 'url' => 'page/about'),
				array('label' => 'Contact', 'url' => 'page/contact'),
			),
			'selected' => $this->_activeMenu,
			'return' => false,
		));
		?>
	</nav>
</header>
<section>
	<?php
	CWidget::create('CBreadCrumbs', array(
		'links' => $this->_breadCrumbs,
		'return' => false,
	));
	?>
	<?= A::app()->view->getContent(); ?>
</section>
<footer>
	<p class="copyright">Copyright &copy; <?= @date('Y'); ?> Your Site</p>
	<p class="powered"><?= A::powered(); ?></p>
</footer>
</body>
</html>