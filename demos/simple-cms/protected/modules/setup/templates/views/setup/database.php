<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1>Database Settings</h1>

<p>
    You may specify your database settings here. Please note that the database for your application
    must be created prior to this step. If you still not created it, do so now.
</p>
<?php echo $actionMessage; ?>
<br>

<?php
    echo CWidget::create('CFormView', array(
        'action'=>'setup/database',
        'method'=>'post',
        'htmlOptions'=>array(
            'name'=>'frmSetup',
            'id'=>'frmSetup'
        ),
        'fields'=>$formFields,
        'buttons'=>array(
            'back'   => array(
                'type'        => 'button',
                'value'       => 'Previous',
                'htmlOptions' => array('name' => '', 'onclick' => "$(location).attr('href','setup/index');")
            ),
            'submit' => array('type' => 'submit', 'value' => 'Next', 'htmlOptions' => array('name' => ''))
        ),
        'events'=>array(
            'focus'=>array('field'=>$errorField)
        ),
        'return'=>true,
    ));
 
?>
<br>
    