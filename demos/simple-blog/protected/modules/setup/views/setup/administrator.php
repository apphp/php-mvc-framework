<?php
A::app()->view->setMetaTags('title', A::t('setup', 'Administrator Account'));
A::app()->getClientScript()->registerScriptFile('assets/modules/setup/js/setup.js', 2);

$this->_activeMenu = $this->_controller . '/' . $this->_action;
?>

<h1><?= A::t('setup', 'Administrator Account'); ?></h1>
<p><?= A::t('setup', 'Administrator Account Notice'); ?></p>

<?= $actionMessage; ?>
<br>

<?php
echo CWidget::create('CFormView', array(
	'action' => 'setup/administrator',
	'method' => 'post',
	'htmlOptions' => array(
		'name' => 'frmSetup',
	),
	'fields' => array(
		'act' => array('type' => 'hidden', 'value' => 'send'),
		'username' => array('type' => 'textbox', 'value' => $username, 'title' => A::t('setup', 'Username'), 'mandatoryStar' => true, 'htmlOptions' => array('maxLength' => '32', 'autocomplete' => 'off')),
		'password' => array('type' => 'password', 'value' => $password, 'title' => A::t('setup', 'Password'), 'mandatoryStar' => true, 'htmlOptions' => array('maxLength' => '25', 'autocomplete' => 'off', 'id' => 'password'), 'appendCode' => '<div for="password" class="toggle_password" data-field="password"></div>'),
		'email' => array('type' => 'textbox', 'value' => $email, 'title' => A::t('setup', 'Email'), 'mandatoryStar' => false, 'htmlOptions' => array('maxLength' => '100', 'autocomplete' => 'off')),
	),
	'buttons' => array(
		'back' => array('type' => 'button', 'value' => A::t('setup', 'Previous'), 'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','setup/database');")),
		'submit' => array('type' => 'submit', 'value' => A::t('setup', 'Next'), 'htmlOptions' => array('name' => '')),
	),
	'events' => array(
		'focus' => array('field' => $errorField),
	),
	'return' => true,
));

?>
<br>