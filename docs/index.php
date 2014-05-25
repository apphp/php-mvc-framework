<?php
	$page = isset($_GET['page']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $_GET['page']) : 'introduction';	
	$validPage = (file_exists('pages/'.$page.'.html')) ? true : false;	
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="apphp framework, apphp mvc framework, documentation, apphp" />
	<meta name="description" content="ApPHP MVC Framework - documentation for Getting Started" />
    <title><?php echo (($validPage) ? ucwords(str_replace('-', ' ', $page)).' | ' : ''); ?>ApPHP MVC Framework Documentation</title>
    
    <link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="stylesheet" type="text/css" href="js/highlight/style.css" media="all" />
	<?php echo (isset($_SERVER['REQUEST_URI']) && !preg_match('/index.php/i', $_SERVER['REQUEST_URI'])) ? '<link rel="canonical" href="http://'.(isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '').'/docs/index.php" />' : ''; ?>	
	
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/highlight/highlight.js"></script>
	<script type="text/javascript" src="js/highlight/helpers.js"></script>
	<?php
		if(in_array($page, array('templates', 'modules-structure', 'modules-knowledge-base'))){
			echo '<script type="text/javascript" src="js/highlight/lang-xml.js"></script>';
		}else if(in_array($page, array('modules-creating', 'session-custom-storage'))){
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
				<li class="active"><a href="index.php">Documentation</a></li>
				<li><a href="../utils/requirements/index.php">Requirements</a></li>				
				<li><a href="../utils/tests/index.php">Tests</a></li>
                <li><a href="../utils/generators/index.php">Code Generators</a></li>
				<li><a href="../demos/index.php">Demo</a></li>				
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../index.html">&laquo; Index</a></li>
			</ul>           
        </nav>
    </header>
    <section>
		<aside>
			<ul>
				<li><a id="introduction" href="index.php?page=introduction">Introduction</a></li>
				<li><a id="installation" href="index.php?page=installation">Installation</a></li>
				<li><a id="updating" href="index.php?page=updating">Updating</a></li>
				<li><a id="running-examples" href="index.php?page=running-examples">Running Examples</a></li>
                <li><a id="coding-standards" href="index.php?page=coding-standards">Coding Standards</a></li>

                <li class="inner">
					<span class="group" onclick="toggleGroup('group-utils')">Utils</span>
					<ul class="inner" id="group-utils">
                        <li><a id="requirements-checker" href="index.php?page=requirements-checker">Requirements Checker</a></li>
                        <li><a id="tests" href="index.php?page=tests">Tests</a></li>
                        <li><a id="code-generators" href="index.php?page=code-generators">Code Generators</a></li>
					</ul>					
				</li>
				
                <li class="inner">
                    <span class="group" onclick="toggleGroup('group-framework-structure')">Framework Structure</span>					
					<ul class="inner" id="group-framework-structure">
                        <li><a id="framework-structure" href="index.php?page=framework-structure">General Review</a></li>
                        <li><a id="collections" href="index.php?page=collections">Collections</a></li>
                        <li><a id="components" href="index.php?page=components">Components</a></li>
                        <li><a id="core" href="index.php?page=core">Core</a></li>
                        <li><a id="database" href="index.php?page=database">Database</a></li>
                        <li><a id="helpers" href="index.php?page=helpers">Helpers</a></li>
						<li><a id="i18n" href="index.php?page=i18n">Internationalization (i18n)</a></li>
						<li><a id="messages" href="index.php?page=messages">Messages</a></li>
                        <li><a id="vendors" href="index.php?page=vendors">Vendors</a></li>
					</ul>					
				</li>
				
				<li class="inner">
					<span class="group" onclick="toggleGroup('group-application-development')">Application Development</span>
					<ul class="inner" id="group-application-development">
						<li><a id="dummy-application" href="index.php?page=dummy-application">Dummy Application</a></li>
                        <li><a id="directy-cmf" href="index.php?page=directy-cmf">Directy CMF</a></li>
                        <li><a id="setup-application" href="index.php?page=setup-application">Setup</a></li>
						<li><a id="file-structure" href="index.php?page=file-structure">File Structure</a></li>
                        <li><a id="application-modes" href="index.php?page=application-modes">Application Modes</a></li>
						<li><a id="configuration-files" href="index.php?page=configuration-files">Configuration Files</a></li>
						<li><a id="templates" href="index.php?page=templates">Templates</a></li>
                        <li><a id="routing" href="index.php?page=routing">Routing</a></li>
						<li><a id="controllers-and-actions" href="index.php?page=controllers-and-actions">Controllers & Actions</a></li>
                        <li><a id="models" href="index.php?page=models">Models</a></li>
						<li><a id="views" href="index.php?page=views">Views</a></li>
                        <li><a id="authorization" href="index.php?page=authorization">Authorization</a></li>
                        <li><a id="l10n" href="index.php?page=l10n">Localization (l10n)</a></li>
                        <li><a id="application-components" href="index.php?page=application-components">Components</a></li>
                        <li><a id="widgets" href="index.php?page=widgets">Widgets</a></li>
						<li><a id="application-vendors" href="index.php?page=application-vendors">Application Vendors</a></li>
                        <li><a id="errors-handling" href="index.php?page=errors-handling">Errors Handling</a></li>
						<li><a id="development-workflow" href="index.php?page=development-workflow">Development Workflow</a></li>
					</ul>					
				</li>
				
				<li class="inner">
					<span class="group" onclick="toggleGroup('group-special-topics')">Special Topics</span>
					<ul class="inner" id="group-special-topics">
						<li><a id="security" href="index.php?page=security">Security</a></li>
						<li><a id="cron-jobs" href="index.php?page=cron-jobs">Cron Jobs</a></li>
                        <li><a id="database-request-caching" href="index.php?page=database-request-caching">Data Caching</a></li>
                        <li><a id="session-custom-storage" href="index.php?page=session-custom-storage">Session Custom Storage</a></li>
					</ul>					
				</li>				
				                
				<li class="inner">
					<span class="group" onclick="toggleGroup('group-working-with-forms')">Working with Forms</span>
					<ul class="inner" id="group-working-with-forms">
						<li><i>Overview</i></li>
					</ul>					
				</li>				

                <li class="inner">
					<span class="group" onclick="toggleGroup('group-application-modules')">Application Modules</span>
					<ul class="inner" id="group-application-modules">
						<li><a id="modules-overview" href="index.php?page=modules-overview">Overview</a></li>
                        <li><a id="modules-structure" href="index.php?page=modules-structure">Module Structure</a></li>
                        <li><a id="modules-creating" href="index.php?page=modules-creating">Creating a Module</a></li>
                        <li><a id="modules-knowledge-base" href="index.php?page=modules-knowledge-base">Knowledge Base</a></li>
					</ul>					
				</li>
			</ul>
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
		var currentPage = getValue("page");
		if(currentPage == undefined || currentPage == '') currentPage = "introduction";
		if(document.getElementById(currentPage)) document.getElementById(currentPage).className = "active";

		for(i=0; i<docs_menu.length; i++){
			if(getCookie(docs_menu[i]) == 'closed'){
				document.getElementById(docs_menu[i]).style.display = 'none';
			}			
		}
		
		//highlight code
		var args = {
			showWhitespace : false,
			lineNumbers    : true
		};
		DlHighlight.HELPERS.highlightByName('dlhl', "pre", args);
	</script>
</body>
</html>