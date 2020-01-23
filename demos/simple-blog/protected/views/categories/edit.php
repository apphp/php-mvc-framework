<article>
	<h1>Edit Category</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
		<?php
		
		echo CWidget::create('CFormView', array(
			'action' => 'categories/update',
			'method' => 'post',
			'htmlOptions' => array(
				'name' => 'frmEditCategory',
			),
			'fields' => array(
				'act' => array('type' => 'hidden', 'value' => 'send'),
				'categoryId' => array('type' => 'hidden', 'value' => $categoryId),
				'categoryIdLabel' => array('type' => 'label', 'title' => 'Category ID', 'value' => $categoryId),
				'categoryName' => array('type' => 'textbox', 'title' => 'Category Name', 'value' => $categoryName, 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '50', 'class' => 'text_header', 'encode' => true)),
			),
			'buttons' => array(
				'cancel' => array('type' => 'button', 'value' => 'Cancel', 'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','categories/index');")),
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
		This page provides you possibility to edit selected category.
	</div>
	<div class="clear"></div>
</article>
