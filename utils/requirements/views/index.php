<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content="ApPHP Framework - Running Requirements Checker Utility" />
    <meta name="author" content="ApPHP Company - Advanced Power of PHP">
    <meta name="generator" content="ApPHP MVC Framework - Requirements Checker">
	<title>ApPHP MVC Framework - Requirements Checker</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
	<header>
		<nav>
			<ul class="menu">
				<li><a href="../../docs/index.php">Framework Guide</a></li>
				<li class="active"><a href="index.php">Requirements</a></li>				
				<li><a href="../tests/index.php">Tests</a></li>
                <li><a href="../generators/index.php">Code Generators</a></li>
				<li><a href="../../demos/index.php">Demo</a></li>
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../../index.html">&laquo; Index</a></li>
			</ul>
		</nav>
	</header>
    <section>
		<aside>
			
			<h2>Description</h2>
			<p>
				<b>ApPHP Framework Requirements Checker.</b> This utility allows you to check if your server configuration meets the requirements
				for running ApPHP Framework Web applications. It checks if the server is running
				the right version of PHP, if appropriate PHP extensions have been loaded,
				if php.ini file settings are correct, etc.
			</p>
		
			<h2>Conclusion</h2>
			<p>
			<?php if($result > 0){ ?>
				<span class="ok">Congratulations! Your server configuration satisfies all requirements by ApPHP Framework.</span>
			<?php }else if($result < 0){ ?>
				<span class="warning">Your server configuration satisfies the minimum requirements by ApPHP Framework. Please pay attention to the warnings listed below if your application will use the corresponding features.</span>
			<?php }else{ ?>
				<span class="failed">Unfortunately your server configuration does not satisfy the requirements by ApPHP Framework.</span>
			<?php } ?>
			</p>
		
			<h2>Legend</h2>
			<table>
			<tr>
				<td class="passed_bg">&nbsp;&nbsp;&nbsp;</td><td>passed</td>
				<td class="failed_bg">&nbsp;&nbsp;&nbsp;</td><td>failed</td>
				<td class="warning_bd">&nbsp;&nbsp;&nbsp;</td><td>warning</td>
			</tr>
			</table>

			<div id="footer">
				<?php echo $server_info; ?>
			</div>
		</aside>
		<article class="central">
			<h2>Details</h2>	
			<table class="result">
			<tr>
				<th>Name</th>
				<th width="210px">Value</th>
				<th width="70px">Result</th>
				<th>Required By</th>
				<th>Memo</th>
			</tr>
			<?php foreach($requirements as $requirement){ ?>
			<tr>
				<td><?php echo $requirement[0]; ?></td>
				<td><?php echo $requirement[2]; ?></td>
				<td class="<?php echo $requirement[3] ? 'passed' : ($requirement[1] ? 'failed' : 'warning'); ?>">
					<?php echo $requirement[3] ? 'Passed' : ($requirement[1] ? 'Failed' : 'Warning'); ?>
				</td>
				<td><?php echo $requirement[4]; ?></td>
				<td><?php echo $requirement[5]; ?></td>
			</tr>
			<?php } ?>
			</table>		
		</article>
	</section>
</body>
</html>