<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1>Server Requirements - Getting System Info</h1>

<p>
    Before proceeding with the full installation, you have to carry out some tests on your server configuration
    to ensure that you are able to install and run your application. Please ensure you read through the results
    thoroughly and do not proceed until all the required tests are passed.
</p>

<?php
    if($notifyMessage){
        echo $notifyMessage;        
    }else{
?>
    <ul>
        <li><b>PHP Version</b>: <i><?php echo $phpversion; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>System</b>: <i><?php echo $system; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>System Architecture</b>: <i><?php echo $systemArchitecture; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Build Date</b>: <i><?php echo $buildDate; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Server API</b>: <i><?php echo $serverApi; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Virtual Directory Support</b>: <i><?php echo $vdSupport; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>ASP Tags</b>: <i><?php echo $aspTags; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Safe Mode</b>: <i><?php echo $safeMode; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Short Open Tag</b>: <i><?php echo $shortOpenTag; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Session Support</b>: <i><?php echo $sessionSupport; ?></i> <span class="checked">&#10004; checked!</span></li>
        
        <li><b>Magic Quotes GPC</b>: <i><?php echo $magicQuotesGpc; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Magic Quotes Runtime</b>: <i><?php echo $magicQuotesRuntime; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>Magic Quotes Sybase</b>: <i><?php echo $magicQuotesSybase; ?></i> <span class="checked">&#10004; checked!</span></li>
        
        <li><b>SMTP</b>: <i><?php echo $smtp; ?></i> <span class="checked">&#10004; checked!</span></li>
        <li><b>SMTP Port</b>: <i><?php echo $smtpPort; ?></i> <span class="checked">&#10004; checked!</span></li>
    </ul>

    <?php
        echo CWidget::create('CFormView', array(
            'action'=>'setup/index',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmSetup',
            ),
            'fields'=>array(
                'act'=>array('type'=>'hidden', 'value'=>'send'),
            ),
            'buttons'=>array(
                'submit'=>array('type'=>'submit', 'value'=>'Next', 'htmlOptions'=>array('name'=>''))
            ),
            'return'=>true,
        ));     
    ?>

<?php
    }
?>
<br>

