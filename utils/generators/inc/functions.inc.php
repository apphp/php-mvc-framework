<?php

function render_file($_params_ = []){
    $_file_ = dirname(__FILE__).'/../views/index.php';
	extract($_params_);
	require($_file_);
}

function prepare_input($str){
    return preg_replace('/[^A-Za-z0-9\_]/', '', $str); 
}