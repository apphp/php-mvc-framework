<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content="ApPHP Framework - Code Generators" />
    <meta name="author" content="ApPHP Company - Advanced Power of PHP">
    <meta name="generator" content="ApPHP MVC Framework - Code Generators">
	<title>ApPHP MVC Framework - Code Generators</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script src="js/main.js" type="text/javascript"></script>
</head>
<body>
	<header>
		<nav>
			<ul class="menu">
				<li><a href="../../docs/index.php">Framework Guide</a></li>
				<li><a href="../requirements/index.php">Requirements</a></li>				
				<li><a href="../tests/index.php">Tests</a></li>
                <li class="active"><a href="../generators/index.php">Code Generators</a></li>
				<li><a href="../../demos/index.php">Demo</a></li>
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../../index.html">&laquo; Index</a></li>
			</ul>
		</nav>
	</header>

    <section>
		<aside>            
            
            <h2>Generation Type</h2>
            <p>
                <select id="sel_generation_types" name="generation_type" onchange="javascript:formSubmit('', 'generation_type')">
                    <option value="">-- select --</option>
                    <?php
                        foreach($arr_generation_types as $key => $val){
                            echo '<option'.(($generation_type == $key) ? ' selected="selected"' : '').' value="'.$key.'">'.$val['name'].'</option>';
                        }
                    ?>
                </select>
            </p>
            
        </aside>
        <article class="central">
            <?php
                echo $content;
            ?>		        
        </article>
    </section>
</body>
</html>