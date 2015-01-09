<article>
    <h1>Administrator Account Settings (<?php echo $isMyAccount ? A::t('app', 'My Account') : A::t('app', 'Edit Admin') ?>)</h1>
	<div class="panel-content">

	<?php echo $actionMessage; ?>

    <?php
        $buttons['submit'] = array('type'=>'submit', 'value'=>A::t('app', 'Update'), 'htmlOptions'=>array('name'=>''));
        if(!$isMyAccount){
        	$buttons['cancel'] = array('type'=>'button', 'value'=>A::t('app', 'Cancel'), 'htmlOptions'=>array('name'=>'', 'class'=>'button white'));
        }
    
		echo CWidget::create('CDataForm', array(
			'model'=>'Admins',
			'primaryKey'=>$admin->id,
			'operationType'=>'edit',
			'action'=>'admins/edit/id/'.$admin->id,
			'successUrl'=>$isMyAccount ? '' : 'admins/view/msg/updated',
			'cancelUrl'=>'admins/view',
			'method'=>'post',
			'passParameters'=>$isMyAccount ? false : true,
			'htmlOptions'=>array(
				'name'=>'frmAdminEdit',
				'enctype'=>'multipart/form-data',
				'autoGenerateId'=>true
			),
			'requiredFieldsAlert'=>true,
			'fieldSetType'=>'frameset',
			'fields'=>array(
				'separatorPersonal' =>array(
					'separatorInfo' => array('legend'=>A::t('app', 'Personal Information')),
					'first_name'    => array('type'=>'textbox', 'title'=>A::t('app', 'First Name'), 'validation'=>array('required'=>true, 'type'=>'mixed', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32')),
					'last_name'     => array('type'=>'textbox', 'title'=>A::t('app', 'Last Name'), 'validation'=>array('required'=>true, 'type'=>'mixed', 'maxLength'=>32), 'htmlOptions'=>array('maxlength'=>'32')),
					'display_name'  => array('type'=>'textbox', 'title'=>A::t('app', 'Display Name'), 'validation'=>array('required'=>false, 'type'=>'mixed', 'maxLength'=>50), 'htmlOptions'=>array('maxlength'=>'50')),
				),
				'separatorContact' =>array(
					'separatorInfo' => array('legend'=>A::t('app', 'Contact Information')),
					'email'			=>array('type'=>'textbox', 'title'=>A::t('app', 'Email'), 'validation'=>array('required'=>true, 'type'=>'email', 'maxLength'=>80, 'unique'=>true), 'htmlOptions'=>array('maxlength'=>'80', 'class'=>'email', 'autocomplete'=>'off')),
				),
				'separatorAccount' =>array(
					'separatorInfo' => array('legend'=>A::t('app', 'Account Information')),
					'role'			=>array('type'=>'select', 'title'=>A::t('app', 'Account Type'), 'data'=>($isMyAccount ? $allRolesList : $rolesList), 'mandatoryStar'=>true, 'htmlOptions'=>($isMyAccount ? array('disabled'=>'disabled') : array()), 'validation'=>array('required'=>false, 'type'=>'set', 'source'=>($isMyAccount ? array('owner') : array_keys($rolesList)))),
					'username'		=>array('type'=>'label', 'title'=>A::t('app', 'Username'), 'tooltip'=>'', 'htmlOptions'=>array()),
					'password'		=>array('type'=>'password', 'title'=>A::t('app', 'Password'), 'validation'=>array('required'=>false, 'type'=>'password', 'minLength'=>6, 'maxlength'=>20), 'encryption'=>array('enabled'=>CConfig::get('password.encryption'), 'encryptAlgorithm'=>CConfig::get('password.encryptAlgorithm'), 'hashKey'=>CConfig::get('password.hashKey')), 'htmlOptions'=>array('maxlength'=>'20', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;')),
					'passwordRetype' =>array('type'=>'password', 'title'=>A::t('app', 'Retype Password'), 'validation'=>array('required'=>false, 'type'=>'confirm', 'confirmField'=>'password', 'minLength'=>6, 'maxlength'=>20), 'htmlOptions'=>array('maxlength'=>'20', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;')),
					'is_active' 	=>array('type'=>'checkbox', 'title'=>A::t('app', 'Active'), 'validation'=>array('type'=>'set', 'source'=>array(0,1)), 'htmlOptions'=>($isMyAccount ? array('disabled'=>'disabled', 'uncheckValue'=>1) : array())),
				),
				'separatorOther' =>array(
					'separatorInfo'  => array('legend'=>A::t('app', 'Other')),
					'created_at'	 =>array('type'=>'label', 'title'=>A::t('app', 'Time Created'), 'definedValues'=>array('0000-00-00 00:00:00'=>A::t('app', 'Unknown')), 'format'=>$dateTimeFormat),
					'updated_at'	 =>array('type'=>'label', 'title'=>A::t('app', 'Last Changed'), 'definedValues'=>array('0000-00-00 00:00:00'=>A::t('app', 'Never')), 'format'=>$dateTimeFormat),
					'last_visited_at' =>array('type'=>'label', 'title'=>A::t('app', 'Last Visit'), 'definedValues'=>array('0000-00-00 00:00:00'=>A::t('app', 'Never')), 'format'=>$dateTimeFormat),
	            ),
			),
			'buttons'=>$buttons,
			'messagesSource'=>'core',
			'return'=>true,
		));
    ?>

    </div>
    <div class="panel-settings">
        This page provides you possibility to edit admin's profile information.
        Enter the data you need and click Update button to save the changes.
    </div>
    <div class="clear"></div>
</article>
