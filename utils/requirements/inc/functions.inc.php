<?php

function check_server_vars($realpath = '')
{
	$vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
	$missing = array();
	foreach($vars as $var){
		if(!isset($_SERVER[$var])) $missing[] = $var;
	}
	if(!empty($missing)) return '$_SERVER does not have {'.implode(', ',$missing).'}';

	if(realpath($_SERVER['SCRIPT_FILENAME']) !== $realpath)
		return '$_SERVER[\'SCRIPT_FILENAME\'] must be the same as the entry script file path.';

	if(!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']))
		return 'Either $_SERVER[\'REQUEST_URI\'] or $_SERVER[\'QUERY_STRING\'] must exist.';

	if(!isset($_SERVER['PATH_INFO']) && strpos($_SERVER['PHP_SELF'],$_SERVER['SCRIPT_NAME']) !== 0)
		return 'Unable to determine URL path info. Please make sure that $_SERVER[\'PATH_INFO\'] (or $_SERVER[\'PHP_SELF\'] and $_SERVER[\'SCRIPT_NAME\']) contains proper value.';

	return '';
}

function check_session_vars()
{
    @session_start();
   	return (!isset($_SESSION)) ? 'Session support disabled. Please make sure your server provide support for sessions.' : '';
}

function check_module_mod_rewrite()
{
    if(function_exists('apache_get_modules')){
        // works only if PHP is not running as CGI module
        $mod_rewrite = in_array('mod_rewrite', apache_get_modules());
    }else{
        // old - $mod_rewrite = getenv('HTTP_MOD_REWRITE') == 'On' ? true : false ;
        $file_content = file_get_contents(get_base_url().'tests/test1.txt');
        $mod_rewrite = ($file_content == '2') ? true : false;
    }   
    return $mod_rewrite;    
}

function get_apphp_version()
{
    $version = '';
	$core_file = dirname(__FILE__).'/../../../framework/Apphp.php';
	
	if(is_file($core_file)){
		include($core_file);
		$version = 'v'.A::getVersion();
	}
	return $version;
}

function get_server_info()
{
	return isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
}

function get_footer_info()
{
	$info[] = '<a href="http://www.apphp.com/php-framework/">ApPHP Framework</a>';
	$info[] = get_apphp_version();
	$info[] = @strftime('%Y-%m-%d %H:%M',time());
	return implode(' : ',$info);
}

function render_file($_params_ = array())
{
    $_file_ = dirname(__FILE__).'/../views/index.php';
	extract($_params_);
	include($_file_);
}

function get_base_url($absolute = true)
{
    $absolutePart = '';
    
    if($absolute){
        $protocol = 'http://';
        $port = '';
        $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ||
            strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https'){
            $protocol = 'https://';
        }	
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
            if(!strpos($httpHost, ':')){
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        }
        $absolutePart = $protocol.$httpHost.$port;
    }

    $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
    if(basename($_SERVER['SCRIPT_NAME']) === $scriptName){
        $scriptUrl = $_SERVER['SCRIPT_NAME'];
    }else if(basename($_SERVER['PHP_SELF']) === $scriptName){
        $scriptUrl = $_SERVER['PHP_SELF'];
    }else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName){
        $scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
    }else if(($pos=strpos($_SERVER['PHP_SELF'], '/'.$scriptName)) !== false){
        $scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos).'/'.$scriptName;
    }else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0){
        $scriptUrl = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
    }else{
        CDebug::addMessage('error', 'entry_script', 'Framework is unable to determine the entry script URL');
    }

    $folder = rtrim(dirname($scriptUrl),'\\/').'/';
    
    return $absolutePart.$folder;
}
