<?php

function ends_with($str, $sub) {
    return (substr($str, strlen($str) - strlen($sub)) == $sub);
}

function starts_with($str, $sub) {
    return strpos($str, $sub) === 0;
}

function render_file($_params_ = []){
    $_file_ = dirname(__FILE__).'/../views/index.php';
	extract($_params_);
	require($_file_);
}

function get_microtime(){
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}