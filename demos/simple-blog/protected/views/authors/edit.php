<article>
	<h1>Administrator Account Settings</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
		<?php
		echo CWidget::create('CFormView', array(
			'action' => 'authors/update',
			'method' => 'post',
			'htmlOptions' => array(
				'name' => 'frmAuthor',
				'enctype' => 'multipart/form-data',
			),
			'fields' => array(
				'act' => array('type' => 'hidden', 'value' => 'send'),
				'login' => array('type' => 'hidden', 'value' => $login),
				'loginLabel' => array('type' => 'label', 'value' => $login, 'title' => 'User Name'),
				'password' => array('type' => 'password', 'value' => $password, 'title' => 'Password', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '20', 'placeholder' => '&#9679;&#9679;&#9679;&#9679;&#9679;')),
				'passwordRetype' => array('type' => 'password', 'value' => $passwordRetype, 'title' => 'Repeat Password', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '20', 'placeholder' => '&#9679;&#9679;&#9679;&#9679;&#9679;')),
				'email' => array('type' => 'textbox', 'value' => $email, 'title' => 'Email', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '100', 'class' => 'email', 'autocomplete' => 'off')),
				'aboutText' => array('type' => 'textarea', 'value' => $aboutText, 'title' => 'About Me', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '300', 'class' => 'large')),
				'avatarImg' => array('type' => 'image', 'title' => 'Avatar Preview', 'src' => 'templates/default/images/authors/' . $avatarFile, 'alt' => 'Avatar Preview', 'htmlOptions' => array('class' => 'avatar')),
				'avatarLabel' => array('type' => 'label', 'value' => $avatarFile, 'title' => 'Avatar File', 'mandatoryStar' => false),
				'avatar' => array('type' => 'file', 'title' => ' ', 'mandatoryStar' => false, 'htmlOptions' => array('accept' => 'image/jpeg, image/png, image/gif, image/jpg', 'class' => 'file', 'size' => '25')),
			),
			'buttons' => array(
				'reset' => array('type' => 'reset', 'value' => 'Reset', 'htmlOptions' => array('type' => 'reset')),
				'submit' => array('type' => 'submit', 'value' => 'Update'),
			),
			'events' => array(
				'focus' => array('field' => $errorField),
			),
			'return' => true,
		));
		?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to edit profile information.
		Enter the data you need and click Update button to save the changes.
	</div>
	<div class="clear"></div>
</article>
