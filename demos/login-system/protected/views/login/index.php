<?php
    $this->_activeMenu = 'login/index';
?>

<div style="width:400px; margin:100px auto;">

<?php
    if(APPHP_MODE == 'demo'){
        echo CWidget::create('CMessage', ['info', 'To access the demo account enter<br>Username: <b>admin</b><br>Password: <b>test</b>']);
    }
?>
	
<?= $actionMessage; ?>

<fieldset>
    <legend>Login</legend>
    
    <?php
        // draw login form
        echo CWidget::create('CFormView', [
            'action'=>'login/run',
            'method'=>'post',
            'htmlOptions' => [
                'name' => 'frmLogin'
            ],
            'fields'=>[
                'act'     =>['type'=>'hidden', 'value'=>'send'],
                'username'=>['type'=>'textbox', 'value'=>$username, 'title'=>'Username', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'32', 'autocomplete'=>'off')],
                'password'=>['type'=>'password', 'value'=>$password, 'title'=>'Password', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxLength'=>'20')],
            ],
            'buttons' => [
                'submit' => ['type' => 'submit', 'value' => 'Login'],
            ],
            'events'  => [
                'focus' => ['field' => $errorField]
            ],
            'return'=>true,
        ]);
    ?>
    
</fieldset>
</div>
