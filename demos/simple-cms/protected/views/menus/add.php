<article>
	<h1>Add Menu</h1>
	
	<?= $actionMessage; ?>

	<div class="panel-content">
		<?php
		echo CWidget::create('CFormView', array(
			'action' => 'menus/insert',
			'method' => 'post',
			'htmlOptions' => array(
				'name' => 'frmAddMenu',
			),
			'fields' => array(
				'act' => array('type' => 'hidden', 'value' => 'send'),
				'menuName' => array('type' => 'textbox', 'title' => 'Menu Name', 'mandatoryStar' => true, 'htmlOptions' => array('maxlength' => '50', 'class' => 'text_header')),
				'sortOrder' => array('type' => 'hidden', 'value' => '0'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'value' => 'Create'),
				'cancel' => array('type' => 'button', 'value' => 'Cancel', 'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','menus/index');")),
			),
			'events' => array(
				'focus' => array('field' => $errorField),
			),
			'return' => true,
		));
		?>
	</div>
	<div class="panel-settings">
		This page provides you possibility to add new menu.
	</div>
	<div class="clear"></div>
</article>
     