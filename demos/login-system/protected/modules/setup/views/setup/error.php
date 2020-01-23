<?php
A::app()->view->setMetaTags('title', A::t('setup', 'Error'));

$this->_activeMenu = $this->_controller . '/' . $this->_action;
?>

<p><?= $errorMessage; ?></p>