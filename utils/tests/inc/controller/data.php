<?php 

$dir = $arr_projects[$project]['path'];

// Open a known directory, and proceed to read its contents
$prepare_data = array();
if(is_dir($dir)){
    if($dh = opendir($dir)){
        while(($file = readdir($dh)) !== false){
        	if(ends_with($file, '.php')) {
            	//echo 'filename: '.$file.' : filetype: '.filetype($dir.$file).'<br>';
                if($file == 'SetupController.php') continue;
            	$cls = explode('.',$file);
				$prepare_data[$cls[0]] = $cls[0];
        	}
        }
        closedir($dh);
    }
}

