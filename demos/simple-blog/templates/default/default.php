<!doctype html>
<html>
<head>
	<meta charset="UTF-8"/>
	<meta name="keywords" content="<?= CHtml::encode($this->_pageKeywords); ?>"/>
	<meta name="description" content="<?= CHtml::encode($this->_pageDescription); ?>"/>
	<meta name="author" content="ApPHP Company - Advanced Power of PHP">
	<meta name="generator" content="ApPHP Simple Blog">
	<title><?= CHtml::encode($this->_pageTitle); ?></title>

	<base href="<?= A::app()->getRequest()->getBaseUrl(); ?>"/>
	
	<?= CHtml::cssFile('templates/default/css/main.css'); ?>
	
	<?= CHtml::scriptFile('//code.jquery.com/jquery-1.11.3.min.js'); ?>
	<?= CHtml::scriptFile('templates/default/js/main.js'); ?>
</head>
<body>
<header>
	<nav>
		<div class="header_left">
			<div class="header_title">
				<?php
				if (CAuth::isLoggedIn()) {
					echo CHtml::label($this->blogName);
				} else {
					echo CHtml::link($this->blogName, (CAuth::isLoggedIn() ? 'authors/index' : 'index/index'), array('class' => 'header_title'));
				}
				?>
			</div>
			<div class="header_slogan"><?= CAuth::isLoggedIn() ? 'Admin Panel' : $this->blogSlogan; ?></div>
		</div>
		<div class="header_right">
			<?php
			if (CAuth::isLoggedIn() && isset($viewRightMenu)) {
				echo BlogMenu::init()->adminTopMenu($viewRightMenu);
			}
			?>
		</div>
	</nav>
</header>
<section>
	<?php
	if (CAuth::isLoggedIn() && isset($activeLink)) {
		echo BlogMenu::init()->adminLeftMenu($activeLink);
	}
	
	$centerContent = A::app()->view->getContent();
	$contenClass = '';
	
	if (isset($viewRightMenu) && $viewRightMenu) {
		echo BlogMenu::init()->blogSideMenu($viewRightMenu);
		$contenClass = ' content-main';
	} elseif (CAuth::isLoggedIn() && (A::app()->getResponseCode() != '404')) {
		$contenClass = ' content-admin';
	}
	
	echo '<div class="content' . $contenClass . '">' . $centerContent . '</div>';
	?>
</section>

<footer>
	<div class="footer_right"><?= CAuth::isLoggedIn() ? '' : '<a href="login/index">Author Login</a>'; ?></div>
	<div class="footer_left"><?= $this->blogFooter . ', ' . A::powered(); ?></div>
</footer>
</body>
</html>