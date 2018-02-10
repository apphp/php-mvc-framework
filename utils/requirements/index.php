<?php

/**
 * ApPHP Framework requirement checker script
 *
 * This script will check if your system meets the requirements for running
 * ApPHP-Framework-powered Web applications.
 */

include('inc/functions.inc.php');

/**
 * list of requirements ([0]name, [1]required or not, [2]value, [3]result, [4]used by, [5]memo)
 */
$requirements = array(
	array('Web Server',           false, get_server_info(), true, 'ApPHP Framework', ''),
	array('PHP version',          true,  phpversion(), version_compare(phpversion(), '5.3.0', '>='), 'ApPHP Framework', 'PHP 5.3.0 or higher is required.'),
	array('PHP Short Open Tag',   true,  (($message = check_short_open_tag()) == 'on' ? 'enabled' : ''), $message, 'ApPHP Framework', 'PHP 5.4.0 or higher is required or PHP must be configured with the <b>--enable-short-tags</b> option.'),
	/// Removed, as not needed from v5.3.0
	///array('PHP error_get_last() function',  false, (function_exists('error_get_last')) ? 'exists' : '', function_exists('error_get_last'), 'ApPHP Framework', 'PHP 5 >= 5.2.0, PHP 7'),
	array('PHP "mcrypt_" functions',  false, (function_exists('mcrypt_decrypt') && function_exists('mcrypt_encrypt')) ? 'exists' : 'not found', (function_exists('mcrypt_decrypt') && function_exists('mcrypt_encrypt')), 'ApPHP Framework', 'PHP 4 >= 4.0.2, PHP 5'),
	array('$_POST variable',      true,  (($message = check_post_vars()) === '' ? 'exists' : ''), ($message === ''), 'ApPHP Framework', $message),
	array('$_SERVER variable',    true,  (($message = check_server_vars(realpath(__FILE__))) === '' ? 'exists' : ''), ($message === ''), 'ApPHP Framework', $message),
	array('$_SESSION variable',   true,  (($message = check_session_vars()) === '') ? 'exists' : '', ($message === ''), 'ApPHP Framework', $message),
	array('Apache module mod_rewrite',   true,  (($message = check_module_mod_rewrite()) == true ? 'enabled' : ''), $message, 'ApPHP Framework', 'Required for normal site work, SEO links.'),    
	array('PDO extension',        true,  (extension_loaded('pdo') ? 'installed' : ''), extension_loaded('pdo'), 'All DB-related classes', ''),
	array('PDO MySQL extension',  true,  (extension_loaded('pdo_mysql') ? 'installed' : ''), extension_loaded('pdo_mysql'), 'All DB-related classes', 'Required if you are using MySQL database.'),
	array('PDO SQLite extension', false, (extension_loaded('pdo_sqlite') ? 'installed' : ''), extension_loaded('pdo_sqlite'), 'All DB-related classes', 'Required if you are using SQLite database.'),
);

// 1 - passed, 0 - failed, -1 - passed with warnings
$result = 1;  

foreach($requirements as $i => $requirement){
	if($requirement[1] && !$requirement[2]) $result = 0;
	elseif($result > 0 && !$requirement[1] && !$requirement[2]) $result = -1;
	if($requirement[4] === '') $requirements[$i][4] = '&nbsp;';
}


render_file(array('requirements'=>$requirements, 'result'=>$result, 'server_info'=>get_footer_info()));

