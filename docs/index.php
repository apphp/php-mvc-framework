<?php
	include_once('inc/settings.inc.php');
	
	$page = isset($_GET['page']) && my_array_search($_GET['page'], $menus) ? strtolower($_GET['page']) : 'introduction';
	$validPage = (file_exists('pages/'.$page.'.html')) ? true : false;
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="apphp framework, apphp mvc framework, documentation, apphp" />
	<meta name="description" content="ApPHP MVC Framework - documentation for Getting Started" />
    <title><?= (($validPage) ? ucwords(str_replace('-', ' ', $page)).' | ' : ''); ?>ApPHP MVC Framework Documentation</title>
    
    <link rel="SHORTCUT ICON" href="apphp.ico" />
	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="stylesheet" type="text/css" href="js/highlight/style.css" media="all" />
	<?= (isset($_SERVER['REQUEST_URI']) && !preg_match('/index.php/i', $_SERVER['REQUEST_URI'])) ? '<link rel="canonical" href="http://'.(isset($_SERVER['HTTP_HOST']) ? htmlentities($_SERVER['HTTP_HOST']) : '').'/docs/index.php" />' : ''; ?>	
	
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/highlight/highlight.js"></script>
	<script type="text/javascript" src="js/highlight/helpers.js"></script>
	<?php
		if(in_array($page, array('templates', 'layouts', 'modules-structure', 'knowledge-base', 'shopping-cart'))){
			echo '<script type="text/javascript" src="js/highlight/lang-xml.js"></script>';
		}
		if(in_array($page, array('modules-creating', 'session-custom-storage', 'knowledge-base'))){
            echo '<script type="text/javascript" src="js/highlight/lang-sql.js"></script>';
        }
	?>
	<script type="text/javascript" src="js/highlight/lang-php.js"></script>	
</head>
<body>
	<a name="top"></a>
    <header>
        <nav>
            <ul class="menu">
				<li class="active"><a href="index.php">ApPHP Framework Guide 1.0</a></li>
				<li><a href="../utils/requirements/index.php">Requirements</a></li>				
				<li><a href="../utils/tests/index.php">Tests</a></li>
                <li><a href="../utils/generators/index.php">Code Generators</a></li>
				<li><a href="../demos/index.php">Demo</a></li>
				
				<li class="nohover">
					<form action="search.php" method="get">
						<input placeholder="search..." name="keyword" type="text" maxlength="255">
					</form>
				</li>				
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../index.html">&laquo; Index</a></li>
			</ul>           
        </nav>
    </header>
    <section>
		<aside>
			<?php @include_once('inc/menu.inc.php'); ?>
		</aside>
		
		<article class="central">		
		<?php
			if($validPage){
				@include_once('pages/'.$page.'.html');
			}else{
				echo '<h2>Error 404 - Page not Found</h2>';
				echo '<p>THE PAGE YOU WERE LOOKING FOR COULD NOT BE FOUND<br><br>The server encountered an generall error or misconfiguration and was unable to complete your request.<br>This could be the result of the page being removed, the name being changed or the page being temporarily unavailable.</p>';
			}
		?>
		</article>		
	</section>
    
    <a class="scrollup" href="#" style="display:none;"></a>
    
	<script type="text/javascript">
		// Save blocks status
		for(i=0; i<docs_menu.length; i++){
			if(getCookie(docs_menu[i]) == 'closed'){
				document.getElementById(docs_menu[i]).style.display = 'none';
			}			
		}

		// Highlight code
		var args = {
			showWhitespace : false,
			lineNumbers    : true
		};
		DlHighlight.HELPERS.highlightByName('dlhl', "pre", args);
	</script>
</body>
</html>