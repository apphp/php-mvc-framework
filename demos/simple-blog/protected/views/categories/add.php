<article>
	<h1>Add Category</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
		<?php
		echo CWidget::create('CFormView', array(
			'action' => 'categories/insert',
			'method' => 'post',
			'htmlOptions' => array(
				'name' => 'frmAddCategory',
			),
			'fields' => array(
				'act' => array('type' => 'hidden', 'value' => 'send'),
				'categoryName' => array('type' => 'textbox', 'title' => 'Category Name', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '50', 'class' => 'text_header')),
			),
			'buttons' => array(
				'cancel' => array('type' => 'button', 'value' => 'Cancel', 'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','categories/index');")),
				'submit' => array('type' => 'submit', 'value' => 'Create'),
			),
			'events' => array(
				'focus' => array('field' => $errorField),
			),
			'return' => true,
		));
		?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to add new category.
	</div>
	<div class="clear"></div>
</article>
     