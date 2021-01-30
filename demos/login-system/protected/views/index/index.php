<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1><?= $header; ?></h1>

<article>
    <p><?= $text; ?></p>
</article>
<?php
    if(!CAuth::isLoggedIn()){
        echo CWidget::create('CMessage', ['info', 'Click <a href="login/index"><b>here</b></a> to log into the system as administrator.']);
    }
?>
