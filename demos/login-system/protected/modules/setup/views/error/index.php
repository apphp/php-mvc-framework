<?php
	A::app()->view->setMetaTags('title', '404 Error');
    $this->_pageTitle = '404 Error';
?>

<h2><?= $header; ?></h2>

<p>
<?= CWidget::create('CMessage', array('error', $text)).'<br>'; ?>
</p>
