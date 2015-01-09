<?php
    $this->_pageTitle = 'Sample application - Static Site : '.$header;
    $this->_activeMenu = $this->_controller.'/'.$this->_action;

?>

<h1><?php echo $header; ?></h1>

<article>
    <p><?php echo $text; ?></p>
</article>

<?php if(!empty($comments)){ ?>
<article>
    <p><?php echo $comments; ?></p>
</article>
<?php } ?>

<?php if($this->_action == 'contact'){ ?>
    <?php echo $actionMessage; ?>

    <fieldset>
        <legend>Contact Us</legend>
        <?php echo CWidget::create('CFormView', array(
            'action'=>'page/contact',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'form-contact'
            ),
            'fields'=>array(
                'act'       =>array('type'=>'hidden', 'value'=>'send'),
                'first_name'=>array('type'=>'textbox', 'value'=>$firstName, 'title'=>'First Name', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'50')),
                'last_name' =>array('type'=>'textbox', 'value'=>$lastName, 'title'=>'Last Name', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'50')),
                'email'     =>array('type'=>'textbox', 'value'=>$email, 'title'=>'Email', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'70')),
                'message'   =>array('type'=>'textarea', 'value'=>$message, 'title'=>'Message', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxLength'=>'250')),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>'Send')
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
        )); ?>        
    </fieldset>

<?php } ?>
