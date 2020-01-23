<?php
A::app()->view->setMetaTags('title', A::t('setup', 'Completed'));

$this->_activeMenu = $this->_controller . '/' . $this->_action;
$baseUrl = A::app()->getRequest()->getBaseUrl();
$backendUrl = $baseUrl . 'backend/login';
?>

<h1><?= A::t('setup', 'Completed'); ?></h1>

<?= $actionMessage; ?>

<p>
	<?= A::t('setup', 'Your website is available at'); ?> <a href="<?= $baseUrl; ?>"><?= $baseUrl; ?></a>
	<br><br>
	<?= A::t('setup', 'You may login to Admin Panel'); ?>: <a href="<?= $backendUrl; ?>"><?= $backendUrl; ?></a><br>
	<?= A::t('setup', 'Username is'); ?>: <i><?= $username; ?></i>
	<br>
	<?= A::t('setup', 'Password is'); ?>: <i><?= $password; ?></i>
	<br><br>
</p>


