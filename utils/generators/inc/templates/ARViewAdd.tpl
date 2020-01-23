<?php
    $this->_activeMenu = '[MODULE_CODE]/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('[MODULE_CODE]', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('[MODULE_CODE]', '[MODULE_NAME]'), 'url'=>'modules/settings/code/[MODULE_CODE]'),
        array('label'=>A::t('[MODULE_CODE]', '[MODEL_NAME]'), 'url'=>'[CONTROLLER_NAME_LC]/manage'),
		array('label'=>A::t('[MODULE_CODE]', 'Add New [MODEL_NAME]')),
    );    
?>

<h1><?= A::t('[MODULE_CODE]', '[MODEL_NAME] Management')?></h1>

<div class="bloc">
	<?= $tabs; ?>
		
	<div class="sub-title"><?= A::t('[MODULE_CODE]', 'Add New [MODEL_NAME]'); ?></div>
    <div class="content">
	<?php
		echo CWidget::create('CDataForm', array(
			'model'=>'[MODEL_NAME]',
			'primaryKey'=>0,
			'operationType'=>'add',
			'action'=>'[CONTROLLER_NAME_LC]/add/',
			'successUrl'=>'[CONTROLLER_NAME_LC]/manage/msg/added',
			'cancelUrl'=>'[CONTROLLER_NAME_LC]/manage',
			'passParameters'=>false,
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmAdd',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
			'fields'=>array(
				'field_1' => array('type'=>'textbox', 'title'=>'Field 1', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any'),
				'field_2' => array('type'=>'textbox', 'title'=>'Field 2', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any'),
				'field_3' => array('type'=>'textbox', 'title'=>'Field 3', 'default'=>'', 'validation'=>array('required'=>true, 'type'=>'any'),
			),
			//'translationInfo' => array('relation'=>array('id', 'foreign_key'), 'languages'=>Languages::model()->findAll('is_active = 1')),
			//'translationFields' => array(
			//  'name' 		 => array('type'=>'textbox', 'title'=>A::t('[MODULE_CODE]', 'Name'), 'validation'=>array('required'=>true, 'type'=>'any', 'maxLength'=>125), 'htmlOptions'=>array('maxLength'=>'125')),
			//  'description'=> array('type'=>'textarea', 'title'=>A::t('[MODULE_CODE]', 'Description'), 'validation'=>array('required'=>false, 'type'=>'any', 'maxLength'=>256), 'htmlOptions'=>array('maxLength'=>'256')),
			// ),
			'buttons'=>array(
			   'submit' => array('type'=>'submit', 'value'=>A::t('[MODULE_CODE]', 'Create'), 'htmlOptions'=>array('name'=>'')),
			   'cancel' => array('type'=>'button', 'value'=>A::t('[MODULE_CODE]', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white')),
			),
			'messagesSource'=>'core',
			'return'=>true,
		));
	?>
    </div>
</div>
