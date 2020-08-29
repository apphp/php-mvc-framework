<?php
	include_once('inc/settings.inc.php');

	$ignored_words = array('on', 'for', 'a', 'in', 'the', 'this');
	
	/**
	 * Returns formatted microtime
	 * @return long
	 */
	function wscGetFormattedMicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);    
	}

	function listFiles($dir, $keyword, &$array){
		
		global $ignored_words;
		
		if($keyword == '' || strlen($keyword) < 3 || in_array($keyword, $ignored_words)){
		   return false;
		}
		
		if(!$handle = opendir($dir)) return false;
		
		while(false !== ($file=readdir($handle))){
			
			if($file == '.' || $file == '..') continue;
				
			if(preg_match('/\.html/i', $file)){
			
				$data=file_get_contents($dir.'/'.$file);
				$body = strip_tags($data);
				
				if(preg_match('/'.$keyword.'/i', $body)){
					
					if(preg_match('/<h1>(.*)<\/h1>/s', $data, $m)){
						$title = $m['1'];
					}
					else{
						$title = 'No Title';
					}

					$result_text = substr(str_replace(array($title, '¶'), '', $body), 0, 350);
					
					$array[] = str_ireplace('.html', '', $file).'##'.$title.'##'.$result_text;
				}
			}
		}

		closedir($handle); 
		return true; 
	}


	$array = [];
	$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
	$keyword = str_ireplace(array('\\', ':', '../', '%00'), '', $keyword);
	$keyword = str_ireplace(array('(', ')', '[', ']'), array('\(', '\)', '\[', '\]'), $keyword);
	
	// Remove unexpected characters if length more then 255 symbols
	$detect_encoding = function_exists("mb_detect_encoding") ? mb_detect_encoding($keyword) : 'ASCII';
	if($detect_encoding == 'ASCII'){	
		$keyword = substr($keyword, 0, 255);
	}else{
		$keyword = mb_substr($keyword, 0, 255, 'UTF-8');
	}

	$startTime = wscGetFormattedMicrotime();            
	listFiles('pages/', $keyword, $array);
	$finishTime = wscGetFormattedMicrotime();

	$resultnum = count($array);
	$result = '';		

	if(!empty($array)){
		$result .= '<br>Found '.$resultnum.' results for: <i>'.htmlentities(str_ireplace(array('\(', '\)', '\[', '\]'), array('(', ')', '[', ']'), $keyword)).'</i>';
		$result .= '<br>Total running time: '.round((float)$finishTime - (float)$startTime, 5).' sec.';
		$result .= '<br><br>';
		
		$result .= '<ul>';
		foreach($array as $value){
		   list($filedir, $title, $result_text) = explode('##', $value, '3');
		   
		   // Highlight text 
		   $result_text = preg_replace('@('.$keyword.')@si', '<strong style="background-color:yellow">$1</strong>', $result_text);

		   $result .= '<li style="margin-bottom:20px"><a href=index.php?page='.$filedir.' target=_new>'.$title.'</a><br>'.$result_text.'...</li>'."\n";
		}
		$result .= '</ul>';		
	}else{
		$result .= '<br>No results found for: <i>'.htmlspecialchars($keyword).'</i>';		
	}
	
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="apphp framework, apphp mvc framework, documentation, apphp" />
	<meta name="description" content="ApPHP MVC Framework - documentation for Getting Started" />
    <title>Search in ApPHP MVC Framework Documentation</title>
    
    <link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="stylesheet" type="text/css" href="js/highlight/style.css" media="all" />
	<?= (isset($_SERVER['REQUEST_URI']) && !preg_match('/search.php/i', $_SERVER['REQUEST_URI'])) ? '<link rel="canonical" href="http://'.(isset($_SERVER['HTTP_HOST']) ? htmlentities($_SERVER['HTTP_HOST']) : '').'/docs/search.php" />' : ''; ?>	
	
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</head>
<body>
	<a name="top"></a>
    <header>
        <nav>
            <ul class="menu">
				<li class="active"><a href="index.php">ApPHP Framework Guide 1.3</a></li>
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
			<?= $result;	?>
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
	</script>
</body>
</html>