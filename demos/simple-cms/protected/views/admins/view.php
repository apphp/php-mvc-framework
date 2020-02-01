<article>
	<h1><?= A::t('app', 'Admins Management') ?></h1>
	<div class="content">
		<?= $actionMessage; ?>
		<a href="admins/add" class="add-new"><?= A::t('app', 'Add New'); ?></a>
		<?php
		//A::t('app', 'Avatar')
		echo CWidget::create('CGridView', array(
			'model' => 'Admins',
			'actionPath' => 'admins/view',
			'defaultOrder' => array('username' => 'ASC'),
			'passParameters' => true,
			'condition' => 'role = \'admin\' OR role = \'mainadmin\'',
			'pagination' => array('enable' => true, 'pageSize' => 20),
			'sorting' => true,
			'filters' => array(
				'last_name' => array('title' => A::t('app', 'Last Name'), 'type' => 'textbox', 'operator' => 'like%', 'width' => '100px', 'maxLength' => '32'),
				'first_name' => array('title' => A::t('app', 'First Name'), 'type' => 'textbox', 'operator' => 'like%', 'width' => '100px', 'maxLength' => '32'),
				'is_active' => array('title' => A::t('app', 'Active'), 'type' => 'enum', 'operator' => '=', 'width' => '', 'source' => array('' => '', '0' => A::t('app', 'No'), '1' => A::t('app', 'Yes'))),
			),
			'fields' => array(
				'fullname' => array('title' => A::t('app', 'Full Name'), 'type' => 'label', 'class' => 'left', 'headerClass' => 'left', 'width' => '190px'),
				'username' => array('title' => A::t('app', 'Username'), 'type' => 'label', 'class' => 'left', 'headerClass' => 'left', 'width' => '110px'),
				'email' => array('title' => A::t('app', 'Email'), 'type' => 'label', 'class' => 'left', 'headerClass' => 'left'),
				'role' => array('title' => A::t('app', 'Account Type'), 'type' => 'enum', 'class' => 'center', 'headerClass' => 'center', 'source' => $rolesList, 'width' => '110px'),
				'is_active' => array('title' => A::t('app', 'Active'), 'type' => 'enum', 'class' => 'center', 'headerClass' => 'center', 'source' => array('0' => '<span class="badge-red">' . A::t('app', 'No') . '</span>', '1' => '<span class="badge-green">' . A::t('app', 'Yes') . '</span>'), 'width' => '110px'),
				'last_visited_at' => array('title' => A::t('app', 'Last Visit'), 'type' => 'label', 'class' => 'center', 'headerClass' => 'center', 'definedValues' => array(null => A::t('app', 'Never')), 'width' => '100px', 'format' => $dateTimeFormat),
			),
			'actions' => array(
				'edit' => array('link' => 'admins/edit/id/{id}', 'imagePath' => 'templates/backend/images/edit.png', 'title' => A::t('app', 'Edit this record')),
				'delete' => array('link' => 'admins/delete/id/{id}', 'imagePath' => 'templates/backend/images/delete.png', 'title' => A::t('app', 'Delete this record'), 'onDeleteAlert' => true),
			),
		));
		?>
	</div>
</article>