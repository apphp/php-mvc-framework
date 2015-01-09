<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1>Administrator Account</h1>

<p>
    You may now set up an administrator account for yourself. This will allow you to manage your application
    through the admin control panel.
</p>
<?php echo $actionMessage; ?>
<br>

<?php
    echo CWidget::create('CFormView', array(
        'action'=>'setup/administrator',
        'method'=>'post',
        'htmlOptions'=>array(
            'name'=>'frmSetup'
        ),
        'fields'=>array(
            'act'=>array('type'=>'hidden', 'value'=>'send'),            
            'email'=>array('type'=>'textbox', 'value'=>$email, 'title'=>'Email', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxLength'=>'70', 'autocomplete'=>'off')),
            'username'=>array('type'=>'textbox', 'value'=>$username, 'title'=>'Username', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'32', 'autocomplete'=>'off')),
            'password'=>array('type'=>'password', 'value'=>$password, 'title'=>'Password', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'20', 'autocomplete'=>'off')),
        ),
        'buttons'=>array(
            'back'=>array('type'=>'button', 'value'=>'Previous', 'htmlOptions'=>array('name'=>'', 'onclick'=>"$(location).attr('href','setup/database');")),
            'submit'=>array('type'=>'submit', 'value'=>'Next', 'htmlOptions'=>array('name'=>''))
        ),
        'events'=>array(
            'focus'=>array('field'=>$errorField)
        ),
        'return'=>true,
    ));

?>
<br>