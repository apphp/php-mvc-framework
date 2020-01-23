<?php
    $this->_activeMenu = 'login/index';
?>

<div style="width:400px; margin:100px auto;">

<?php
    if(APPHP_MODE == 'demo'){
        echo CWidget::create('CMessage', array('info', 'To access the demo account enter<br>Username: <b>admin</b><br>Password: <b>test</b>'));
    }
?>
	
<?= $actionMessage; ?>

<fieldset>
    <legend>Login</legend>
    
    <?php
        // draw login form
        echo CWidget::create('CFormView', array(
            'action'=>'login/run',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmLogin'
            ),
            'fields'=>array(
                'act'     =>array('type'=>'hidden', 'value'=>'send'),
                'username'=>array('type'=>'textbox', 'value'=>$username, 'title'=>'Username', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off')),
                'password'=>array('type'=>'password', 'value'=>$password, 'title'=>'Password', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxLength'=>'20')),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>'Login'),
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));    
    ?>
    
</fieldset>
</div>
