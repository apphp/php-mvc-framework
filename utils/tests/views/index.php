<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>ApPHP Framework - Tests</title>	
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script src="js/main.js" type="text/javascript"></script>	
</head>
<body>
	<header>
		<nav>
			<ul class="menu">
				<li class=""><a href="../../docs/">Documentation</a></li>
				<li class=""><a href="../requirements/">Requirements</a></li>				
				<li class="active"><a href="index.php">Tests</a></li>				
				<li class=""><a href="../../demos/">Demo</a></li>
			</ul>
			<ul class="menu" style="float:right">
				<li><a href="../../index.html">Index</a></li>
			</ul>
		</nav>
	</header>

    <section>
		<aside>            
            
            <h2>Project</h2>
            <p>
                <select id="sel_project" name="project" onchange="javascript:formSubmit('', 'project')">
                    <option value="">-- select --</option>
                    <?php
                        foreach($arr_projects as $key => $val){
                            echo '<option'.(($project == $key) ? ' selected="selected"' : '').' value="'.$key.'">'.$val['name'].'</option>';
                        }
                    ?>
                </select>
            </p>
                
            
            <?php if(count($arr_actions) > 0){ ?>            
            <h2>Action</h2>
            <p>
                <select id="sel_action" name="action" onchange="javascript:formSubmit()">
                    <option value="">-- select --</option>
                    <?php
                        foreach($arr_actions as $key => $val){
                            echo '<option'.(($action == $key) ? ' selected="selected"' : '').' value="'.$key.'">'.$val.'</option>';
                        }
                    ?>
                </select>
            </p>
            <?php } ?>
    
            <?php
                if(count($arr_operations) > 0){
					$operation  = isset($_GET['operation']) ? filter_var($_GET['operation'], FILTER_SANITIZE_STRING) : '';
                    echo '<ul>';
                    foreach($arr_operations as $key => $val){
						if($operation == $key) $val = '<b>'.$val.'</b>';
                        echo '<li><a href="javascript:formSubmit(\''.$key.'\')">'.$val.'</a></li>';
                    }				
                    echo '</ul>';
                }
            ?>
        </aside>
        <article class="central">
            <?php
                echo $content;
            ?>		        
        </article>
    </section>
</body>
</html>