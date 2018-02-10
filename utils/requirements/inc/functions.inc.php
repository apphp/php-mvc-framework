<?php

/**
 * Checks $_SERVER variables
 * @return string
 */
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

/**
 * Checks $_SESSION variables
 * @return string
 */
function check_session_vars()
{
    @session_start();
   	return (!isset($_SESSION)) ? 'Session support disabled. Please make sure your server provide support for sessions.' : '';
}

/**
 * Checks $_POST variables
 * @return string
 */
function check_post_vars()
{
   	return (!isset($_POST)) ? 'POST support disabled. Please make sure your server provide support for POST.' : '';
}

/**
 * Checks mod_rewrite
 * @return bool
 */
function check_module_mod_rewrite()
{
    if(function_exists('apache_get_modules')){
        // works only if PHP is not running as CGI module
        $mod_rewrite = in_array('mod_rewrite', apache_get_modules());
    }else{
        // old - $mod_rewrite = getenv('HTTP_MOD_REWRITE') == 'On' ? true : false ;
		$file_content = null;
		$absolutePath = ini_get('allow_url_fopen') ? get_base_url() : '';
		$file_content = file_get_contents($absolutePath.'tests/test1.txt');	
        $mod_rewrite = ($file_content == '2') ? true : false;
    }
	
    return $mod_rewrite;    
}

/**
 * Returns framework version
 * @return string
 */
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

/**
 * Returns server info
 * @return mixed
 */
function get_server_info()
{
	return isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
}

/**
 * Returns footer info
 * @return string
 */
function get_footer_info()
{
	$info[] = '<a href="https://www.apphp.com/php-framework/">ApPHP Framework</a>';
	$info[] = get_apphp_version();
	$info[] = @strftime('%Y-%m-%d %H:%M',time());
	return implode(' : ',$info);
}

/**
 * Render file
 * @return void
 */
function render_file($_params_ = array())
{
    $_file_ = dirname(__FILE__).'/../views/index.php';
	extract($_params_);
	include($_file_);
}

/**
 * Returns base URL
 * @return string
 */
function get_base_url($absolute = true)
{
    $absolutePart = '';
    
    if($absolute){
        $protocol = 'http://';
        $port = '';
        $httpHost = isset($_SERVER['HTTP_HOST']) ? htmlentities($_SERVER['HTTP_HOST']) : '';
		$serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';

		if((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) || strtolower(substr($serverProtocol, 0, 5)) == 'https'){
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
    }elseif(basename($_SERVER['PHP_SELF']) === $scriptName){
        $scriptUrl = $_SERVER['PHP_SELF'];
    }elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName){
        $scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
    }elseif(($pos=strpos($_SERVER['PHP_SELF'], '/'.$scriptName)) !== false){
        $scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos).'/'.$scriptName;
    }elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0){
        $scriptUrl = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
    }else{
        CDebug::addMessage('error', 'entry_script', 'Framework is unable to determine the entry script URL');
    }

    $folder = rtrim(dirname($scriptUrl),'\\/').'/';
    
    return $absolutePart.$folder;
}

/**
 * Returns array with full PHP info
 * @return array
 */
function get_php_info()
{
	ob_start();        
	if(function_exists('phpinfo')) @phpinfo(-1);
	$phpInfo = array('phpinfo' => array());
	if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
	foreach($matches as $match){
		$arrayKeys = array_keys($phpInfo);
		$endArrayKeys = end($arrayKeys);
		if(strlen($match[1])){
			$phpInfo[$match[1]] = array();
		}elseif(isset($match[3])){
			$phpInfo[$endArrayKeys][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
		}else{				
			$phpInfo[$endArrayKeys][] = $match[2];
		}
	}
	
	return $phpInfo;
}

/**
 * Checks if short PHP tags are allowed
 * @return mixed
 */
function check_short_open_tag()
{
	$phpInfo = get_php_info();
	$phpCoreIndex = version_compare(phpversion(), '5.3.0', '<') ? 'PHP Core' : 'Core';
	// For PHP v5.6 or later
	if(!isset($phpInfo[$phpCoreIndex]) && version_compare(phpversion(), '5.6.0', '>=') ){
		$phpCoreIndex = 'HTTP Headers Information';
	}
	
	$shortOpenTag = isset($phpInfo[$phpCoreIndex]['short_open_tag'][0]) ? strtolower($phpInfo[$phpCoreIndex]['short_open_tag'][0]) : false;
	return $shortOpenTag;
}
