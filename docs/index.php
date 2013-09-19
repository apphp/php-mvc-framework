<?php
	$page = isset($_GET['page']) ? $_GET['page'] : 'introduction';
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
	<meta name="keywords" content="apphp framework, documentation, apphp" />
	<meta name="description" content="This is a documentation for ApPHP Framework." />
    <title>ApPHP Framework - Documentation</title>
    
    <link rel="stylesheet" type="text/css" href="css/default.css" />
	<link rel="stylesheet" type="text/css" href="js/highlight/style.css" media="all" />
	
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/highlight/highlight.js"></script>
	<script type="text/javascript" src="js/highlight/helpers.js"></script>
	<?php
		if($page == 'templates') echo '<script type="text/javascript" src="js/highlight/lang-xml.js"></script>';
	?>
	<script type="text/javascript" src="js/highlight/lang-php.js"></script>	
</head>
<body>
	<a name="top"></a>
    <header>
        <nav>
            <ul class="menu">
				<li class="active"><a href="index.php">Documentation</a></li>
				<li class=""><a href="../utils/requirements/">Requirements</a></li>				
				<li class=""><a href="../utils/tests/">Tests</a></li>				
				<li class=""><a href="../demos/">Demo</a></li>				
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../index.html">Index</a></li>
			</ul>
        </nav>
    </header>
    <section>
		<aside>
			<ul>
				<li><a id="introduction" href="index.php?page=introduction">Introduction</a></li>
				<li><a id="installation" href="index.php?page=installation">Installation</a></li>
				<li><a id="updating" href="index.php?page=updating">Updating</a></li>
				<li><a id="running_examples" href="index.php?page=running_examples">Running Examples</a></li>
                <li><a id="coding_standards" href="index.php?page=coding_standards">Coding Standards</a></li>

                <li class="inner">
					<span class="group" onclick="toggleGroup('group-utils')">Utils</span>
					<ul class="inner" id="group-utils">
                        <li><a id="requirements_checker" href="index.php?page=requirements_checker">Requirements Checker</a></li>
                        <li><a id="tests" href="index.php?page=tests">Tests</a></li>
					</ul>					
				</li>
				
                <li class="inner">
                    <span class="group" onclick="toggleGroup('group-framework-structure')">Framework Structure</span>					
					<ul class="inner" id="group-framework-structure">
                        <li><a id="framework_structure" href="index.php?page=framework_structure">General Review</a></li>
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
						<li><a id="dummy_application" href="index.php?page=dummy_application">Dummy Application</a></li>
                        <li><a id="bootstrap_application" href="index.php?page=bootstrap_application">Bootstrap Application</a></li>
						<li><a id="file_structure" href="index.php?page=file_structure">File Structure</a></li>
                        <li><a id="application_modes" href="index.php?page=application_modes">Application Modes</a></li>
						<li><a id="configuration_files" href="index.php?page=configuration_files">Configuration Files</a></li>
						<li><a id="templates" href="index.php?page=templates">Templates</a></li>
                        <li><a id="routing" href="index.php?page=routing">Routing</a></li>
						<li><a id="controllers_and_actions" href="index.php?page=controllers_and_actions">Controllers & Actions</a></li>
                        <li><a id="models" href="index.php?page=models">Models</a></li>
						<li><a id="views" href="index.php?page=views">Views</a></li>
                        <li><a id="authorization" href="index.php?page=authorization">Authorization</a></li>
                        <li><a id="l10n" href="index.php?page=l10n">Localization (l10n)</a></li>
                        <li><a id="application_components" href="index.php?page=application_components">Components</a></li>
                        <li><i>Working with Forms</i></li>
                        <li><a id="widgets" href="index.php?page=widgets">Widgets</a></li>
						<li><a id="localization" href="index.php?page=application_vendors">Application Vendors</a></li>
                        <li><a id="errors_handling" href="index.php?page=errors_handling">Errors Handling</a></li>
						<li><i>Development Workflow</i></li>                        
					</ul>					
				</li>

                <li class="inner">
					<span class="group" onclick="toggleGroup('group-application-modules')">Application Modules</span>
					<ul class="inner" id="group-application-modules">
						<li><i>Setup</i></li>
                        <li><i>Administrators</i></li>
                        <li><i href="index.php?page=creating_modules">Creating Modules</i></li>
					</ul>					
				</li>
			<ul>
		</aside>
		
		<article class="central">		
		<?php
			include_once('pages/'.preg_replace('/[^A-Za-z0-9_\-]/', '', $page).'.html');
		?>
		</article>
		
	</section>
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