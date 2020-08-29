<?php
/**
 * ApPHP Framework code generation script
 *
 * This script will help you to generate codes for framework and existing applications
 */

$arr_generation_types = [
    'controller'    => ['name' => 'Simple Controller', 'path' => ''],
    'model'         => ['name' => 'Simple Model', 'path' => ''],
    'view'          => ['name' => 'Simple View', 'path' => ''],
    'ar_controller' => ['name' => 'Active Records Controller', 'path' => ''],
    'ar_model'      => ['name' => 'Active Records Model', 'path' => ''],
    'ar_view'       => ['name' => 'Active Records View', 'path' => ''],
    //'module'        => array('name'=>'Module', 'path'=>''),
];

$generation_type = isset($_GET['generation_type']) ? filter_var($_GET['generation_type'], FILTER_SANITIZE_STRING) : '';
$content    = '<h2>Code Generator</h2>To start code generation select a Generation Type from the left dropdown box, then follow instructions.';

////////////////////////////////////////////////////////////////////////////

include_once('inc/functions.inc.php');
//include_once('inc/header.inc.php');

////////////////////////////////////////////////////////////////////////////

if($generation_type == 'controller'){
    if(file_exists('inc/controllers/index.php')){
        include('inc/controllers/index.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/controllers/index.php".</span>';	
    }
}elseif($generation_type == 'model'){
    if(file_exists('inc/models/index.php')){
        include('inc/models/index.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/models/index.php".</span>';	
    }
}elseif($generation_type == 'view'){
    if(file_exists('inc/views/index.php')){
        include('inc/views/index.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/views/index.php".</span>';	
    }
}elseif($generation_type == 'ar_controller'){
    if(file_exists('inc/controllers/index.ar.php')){
        include('inc/controllers/index.ar.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/controllers/index.ar.php".</span>';	
    }
}elseif($generation_type == 'ar_model'){
    if(file_exists('inc/models/index.ar.php')){
        include('inc/models/index.ar.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/models/index.ar.php".</span>';	
    }
}elseif($generation_type == 'ar_view'){
    if(file_exists('inc/views/index.ar.php')){
        include('inc/views/index.ar.php');
    }else{
        $content = '<br><span class="failed">Cannot open "inc/views/index.ar.php".</span>';	
    }
}

////////////////////////////////////////////////////////////////////////////

render_file(array(
    'arr_generation_types' => $arr_generation_types,
    'generation_type' => $generation_type,
    'content' => $content
));
