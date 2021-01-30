<article>
	<h1>General Site Settings</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
	<?php
		echo CWidget::create('CFormView', [
			'action' => 'settings/update',
			'method' => 'post',
			'htmlOptions' => [
				'name' => 'frmSettings',
			],
			'fields' => [
				'act' => ['type' => 'hidden', 'value' => 'send'],
				'blogName' => ['type' => 'textbox', 'value' => $blogName, 'title' => 'Blog Name', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '100', 'class' => 'text_header']],
				'slogan' => ['type' => 'textarea', 'value' => $slogan, 'title' => 'Slogan', 'mandatoryStar' => false, 'htmlOptions' => ['maxlength' => '250', 'class' => 'small']],
				'footer' => ['type' => 'textarea', 'value' => $footer, 'title' => 'Footer', 'mandatoryStar' => false, 'htmlOptions' => ['maxlength' => '250', 'class' => 'middle']],
				'postMaxChars' => ['type' => 'textbox', 'value' => $postMaxChars, 'title' => 'Maximum Post Length', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '5', 'class' => 'numeric']],
				'metaTagTitle' => ['type' => 'textarea', 'value' => $metaTagTitle, 'title' => CHtml::encode('Tag <TITLE>'), 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '250', 'class' => 'small']],
				'metaTagKeywords' => ['type' => 'textarea', 'value' => $metaTagKeywords, 'title' => CHtml::encode('Meta Tag <KEYWORDS>'), 'mandatoryStar' => false, 'htmlOptions' => ['maxlength' => '250', 'class' => 'middle']],
				'metaTagDescription' => ['type' => 'textarea', 'value' => $metaTagDescription, 'title' => CHtml::encode('Meta Tag <DESCRIPTION>'), 'mandatoryStar' => false, 'htmlOptions' => ['maxlength' => '250', 'class' => 'middle']],
			],
			'buttons' => [
				'reset' => ['type' => 'reset', 'value' => 'Reset', 'htmlOptions' => ['type' => 'reset']],
				'submit' => ['type' => 'submit', 'value' => 'Update'],
			],
			'events' => [
				'focus' => ['field' => $errorField],
			],
			'return' => true,
		]);
	?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to edit global site settings.
		Enter the data you need and click Update button to save the changes.
	</div>
	<div class="clear"></div>
</article>
