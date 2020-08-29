<article>
	<h1>Administrator Account Settings</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
		<?php

        echo CWidget::create(
            'CFormView',
            [
                'action'      => 'authors/update',
                'method'      => 'post',
                'htmlOptions' => ['name' => 'frmAuthor', 'enctype' => 'multipart/form-data'],
                'fields'      => [
                    'act'            => ['type' => 'hidden', 'value' => 'send'],
                    'login'          => ['type' => 'hidden', 'value' => $login],
                    'loginLabel'     => ['type' => 'label', 'value' => $login, 'title' => 'User Name'],
                    'password'       => ['type' => 'password', 'value' => $password, 'title' => 'Password', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '20', 'placeholder' => '&#9679;&#9679;&#9679;&#9679;&#9679;']],
                    'passwordRetype' => ['type' => 'password', 'value' => $passwordRetype, 'title' => 'Repeat Password', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '20', 'placeholder' => '&#9679;&#9679;&#9679;&#9679;&#9679;']],
                    'email'          => ['type' => 'textbox', 'value' => $email, 'title' => 'Email', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '100', 'class' => 'email', 'autocomplete' => 'off']],
					'aboutText'      => ['type' => 'textarea', 'value' => $aboutText, 'title' => 'About Me', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '300', 'class' => 'large']],
					'avatarImg'      => ['type' => 'image', 'title' => 'Avatar Preview', 'src' => 'templates/default/images/authors/'.$avatarFile, 'alt' => 'Avatar Preview', 'htmlOptions' => ['class' => 'avatar']],
					'avatarLabel'    => ['type' => 'label', 'value' => $avatarFile, 'title' => 'Avatar File', 'mandatoryStar' => false],
                    'avatar'         => ['type' => 'file', 'title' => ' ', 'mandatoryStar' => false, 'htmlOptions' => ['accept' => 'image/jpeg, image/png, image/gif, image/jpg', 'class' => 'file', 'size' => '25']],
                ],
                'buttons'     => [
                    'reset'  => ['type' => 'reset', 'value' => 'Reset', 'htmlOptions' => ['type' => 'reset']],
                    'submit' => ['type' => 'submit', 'value' => 'Update'],
                ],
                'events'      => [
                    'focus' => ['field' => $errorField],
                ],
                'return'      => true,
            ]
        );
        ?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to edit profile information.
		Enter the data you need and click Update button to save the changes.
	</div>
	<div class="clear"></div>
</article>
