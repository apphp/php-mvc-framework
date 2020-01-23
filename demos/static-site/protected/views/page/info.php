<?php
$this->_pageTitle = 'Sample application - Static Site : ' . $header;

$this->_activeMenu = $this->_controller . '/about';

$this->_breadCrumbs = array(
	array('label' => 'Home', 'url' => 'index'),
	array('label' => 'Information', 'url' => 'page/about'),
	array('label' => $header),
);

?>

<h1><?= $header; ?></h1>

<aside>
	<ul>
		<li><a class="<?= (($activeLink == 'about_us') ? ' active' : ''); ?>" href="page/about">About Us</a></li>
		<li><a class="<?= (($activeLink == 'our_history') ? ' active' : ''); ?>" href="page/ourHistory">Our History</a></li>
		<li><a class="<?= (($activeLink == 'our_partners') ? ' active' : ''); ?>" href="page/ourPartners">Our Partners</a></li>
		<li><a class="<?= (($activeLink == 'more_info') ? ' active' : ''); ?>" href="page/moreInfo">More Info</a></li>
		<ul>
</aside>

<article class="central">
	<header>
		<h2>Article Title</h2>
	</header>
	<p><?= $text; ?></p>
</article>