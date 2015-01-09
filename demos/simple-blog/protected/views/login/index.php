<div class="login">

<?php
    if(APPHP_MODE == 'demo'){
        echo CWidget::create('CMessage', array('info', 'To access the demo account enter<br>Username: <b>admin</b><br>Password: <b>test</b>'));
    }
?>

<?php echo $actionMessage; ?>

<fieldset>
    <legend>Login</legend>
    
    <?php
        echo CWidget::create('CFormView', array(
            'action'=>'login/run',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmLogin',
            	'id'=>'frmLogin'
            ),
            'fields'=>array(
                'act'     =>array('type'=>'hidden', 'value'=>'send'),
                'username'=>array('type'=>'textbox', 'value'=>$username, 'title'=>A::t('app', 'Username'), 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'20', 'autocomplete'=>'off')),
                'password'=>array('type'=>'password', 'value'=>$password, 'title'=>A::t('app', 'Password'), 'mandatoryStar'=>false, 'htmlOptions'=>array('maxLength'=>'20')),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>'Login')
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));    
    ?>
    
</fieldset>
</div>
