<article>
	<h1>Add Category</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
	<?php
		echo CWidget::create('CFormView', [
			'action' => 'categories/insert',
			'method' => 'post',
			'htmlOptions' => [
				'name' => 'frmAddCategory',
			],
			'fields' => [
				'act' => ['type' => 'hidden', 'value' => 'send'],
				'categoryName' => ['type' => 'textbox', 'title' => 'Category Name', 'mandatoryStar' => true, 'htmlOptions' => ['maxlength' => '50', 'class' => 'text_header']],
			],
			'buttons' => [
				'cancel' => ['type' => 'button', 'value' => 'Cancel', 'htmlOptions' => ['name' => '', 'onclick' => "$(location).attr('href','categories/index');"]],
				'submit' => ['type' => 'submit', 'value' => 'Create'],
			],
			'events' => [
				'focus' => ['field' => $errorField],
			],
			'return' => true,
		]);
	?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to add new category.
	</div>
	<div class="clear"></div>
</article>
     