<?php
    $this->_activeMenu = 'account/edit';
?>

<?= $actionMessage; ?>

<fieldset>
    <legend>Edit My Account</legend>

    <?php
        echo CWidget::create('CFormView', array(
            'action'=>'account/edit',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmAccount',
                'enctype'=>'multipart/form-data'
            ),
            'fields'=>array(
                'act'     =>array('type'=>'hidden', 'value'=>'send'),
                'username'=>array('type'=>'textbox', 'value'=>$username, 'title'=>'Username', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'25', 'readonly'=>true)),
                'password'=>array('type'=>'password', 'value'=>'', 'title'=>'Password', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'20', 'placeholder'=>'&#9679;&#9679;&#9679;&#9679;&#9679;')),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>'Update')
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));
    ?>    

</fieldset>

