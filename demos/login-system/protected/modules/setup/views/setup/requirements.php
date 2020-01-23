<?php
	A::app()->view->setMetaTags('title', A::t('setup', 'Check Application Requirements - System Info and Important Settings'));
	
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1><?= A::t('setup', 'Check Application Requirements - System Info and Important Settings'); ?></h1>
<p><?= A::t('setup', 'Requirements Notice'); ?></p>

<?= $notifyMessage; ?>

<fieldset>
<legend><?= A::t('setup', 'PHP Information'); ?></legend>
<ul>
    <li><?= A::t('setup', 'PHP Version'); ?>: <i><?= $phpVersion; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'PDO Extension'); ?>: <i><?= $pdoExtension; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
</ul>
</fieldset>

<fieldset>
<legend><?= A::t('setup', 'Server Information'); ?></legend>
<ul>
    <li><?= A::t('setup', 'System'); ?>: <i><?= $system; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'System Architecture'); ?>: <i><?= $systemArchitecture; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'Build Date'); ?>: <i><?= $buildDate; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'Server API'); ?>: <i><?= $serverApi; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
</ul>
</fieldset>

<fieldset>
<legend><?= A::t('setup', 'Important Settings'); ?></legend>
<ul>
    <li><?= A::t('setup', 'Virtual Directory Support'); ?>: <i><?= $vdSupport; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'Mode_Rewrite'); ?>: <i><?= $modeRewrite; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <?php if(version_compare(phpversion(), '7.0.0', '<')): ?>
		<li><?= A::t('setup', 'ASP Tags'); ?>: <i><?= $aspTags; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
	<?php endif; ?>
    <li><?= A::t('setup', 'Safe Mode'); ?>: <i><?= $safeMode; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'Short Open Tag'); ?>: <i><?= $shortOpenTag; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'Session Support'); ?>: <i><?= $sessionSupport; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
</ul>
</fieldset>

<fieldset>
<legend><?= A::t('setup', 'Mail Server Settings'); ?></legend>
<ul>
    <li>SMTP: <i><?= $smtp; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
    <li><?= A::t('setup', 'SMTP Port'); ?>: <i><?= $smtpPort; ?></i> <span class="checked">&#10004; <?= A::t('setup', 'checked!'); ?></span></li>
</ul>
</fieldset>

<?php
if (!$isCriticalError) {
	echo CWidget::create('CFormView', array(
		'action' => 'setup/requirements',
		'method' => 'post',
		'htmlOptions' => array(
			'name' => 'frmSetup',
		),
		'fields' => array(
			'act' => array('type' => 'hidden', 'value' => 'send'),
		),
		'buttons' => array(
			'back' => array('type' => 'button', 'value' => A::t('setup', 'Previous'), 'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','setup/index');")),
			'submit' => array('type' => 'submit', 'value' => A::t('setup', 'Next'), 'htmlOptions' => array('name' => ''))
		),
		'return' => true,
	));
}
?>
<br>
