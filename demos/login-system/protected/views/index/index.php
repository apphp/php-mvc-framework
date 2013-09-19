<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1><?php echo $header; ?></h1>

<article>
    <p><?php echo $text; ?></p>
</article>
<?php
    if(!CAuth::isLoggedIn()){
        echo CWidget::create('CMessage', array('info', 'Click <a href="login"><b>here</b></a> to log into the system as administrator.'));
    }
?>
