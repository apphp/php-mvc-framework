<?php
/**
 * CDebug core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):            PROTECTED:               PRIVATE (static):
 * ---------------            ---------------           ---------------
 * init
 * dump / d / dd / ddd
 * console / c
 * write
 * prepareBacktrace
 * backtrace
 * addSqlConnectionTime
 * addSqlTime
 * addMessage
 * getMessage
 * displayInfo
 */

class CDebug
{
	/** @var string */
	private static $_startTime;
	/** @var string */
	private static $_endTime;
	/** @var string */
	private static $_startMemoryUsage;
	/** @var string */
	private static $_endMemoryUsage;
	/** @var array */
	private static $_arrGeneral = array();
	/** @var array */
	private static $_arrParams = array();
	/** @var array */
	private static $_arrConsole = array();
	/** @var array */
	private static $_arrWarnings = array();
	/** @var array */
	private static $_arrErrors = array();
	/** @var array */
	private static $_arrQueries = array();
	/** @var array */
	private static $_arrData;
	/** @var float */
	private static $_sqlConnectionTime = 0;
	/** @var float */
	private static $_sqlTotalTime = 0;
	
	
	/**
	 * Class init constructor
	 */
	public static function init()
	{
		if (APPHP_MODE != 'debug') return false;
		
		// Catch start time
		self::$_startTime = CTime::getMicrotime();
		// Displays the amount of memory being used as soon as the script runs
		self::$_startMemoryUsage = memory_get_usage();
	}
	
	/**
	 * Alias to method 'dump'
	 * @param mixed $param
	 * @param bool $terminate
	 * @param bool $useDbug
	 * @return HTML dump
	 */
	public static function d($param, $terminate = false, $useDbug = true)
	{
		self::dump($param, $terminate, $useDbug);
	}

    /**
     * Alias to method 'dump' with $terminate=true param
     * @param mixed $param
     * @param bool $useDbug
     * @return HTML dump
     */
    public static function dd($param, $useDbug = true)
    {
        self::dump($param, true, $useDbug);
    }

    /**
     * Alias to method 'dump' with $terminate=true param and showing debug panel
     * @param mixed $param
     * @return HTML dump
     */
    public static function ddd($param)
    {
        CDebug::displayInfo();
        self::dump($param, true, true);
    }

	/**
	 * Displays parameter on the screen
	 * @param mixed $param
	 * @param bool $terminate
	 * @param bool $useDbug
	 * @return HTML dump
	 */
	public static function dump($param, $terminate = false, $useDbug = true)
	{
		if ($terminate) {
			@header('content-type: text/html; charset=utf-8');
			echo '<!DOCTYPE html><html><head><meta charset="UTF-8" /></head><body>';
		}

		if ($useDbug) {
			include_once(dirname(__FILE__) . DS . '..' . DS . 'vendors' . DS . 'dbug' . DS . 'dbug.php');
			new dBug($param);
		} else {
			echo '<pre>';
			print_r($param);
			echo '</pre>';
		}
		
		if ($terminate) {
			echo '</body></html>';
			exit(0);
		}
	}
	
	/**
	 * Alias to method 'console'
	 * @param mixed $val
	 * @param string $key
	 * @return HTML dump
	 */
	public static function c($val, $key = '')
	{
		self::console($val, $key);
	}
	
	/**
	 * Displays parameter on the screen
	 * @param mixed $val
	 * @param string $key
	 * @return HTML dump
	 */
	public static function console($val, $key = '')
	{
		self::addMessage('console', $key, $val);
	}
	
	/**
	 * Write string to the debug stack
	 * @param string $val
	 * @param string $key
	 * @param string $storeType
	 * @return void
	 */
	public static function write($val = '', $key = '', $storeType = '')
	{
		if ($key == '') $key = 'console-write-' . CHash::getRandomString(4);
		self::addMessage('general', $key, $val, $storeType);
	}
	
	/**
	 * Prepare backtrace
	 * @param array $traceData
	 * @return string
	 */
	public static function prepareBacktrace($traceData = array())
	{
		$stack = '';
		$i = 0;
		
		// Prepare trace data
		if (empty($traceData)) {
			$trace = debug_backtrace();
			// Remove call to this function from stack trace
			unset($trace[0]);
		} else {
			$trace = $traceData;
		}
		
		foreach ($trace as $node) {
			$file = isset($node['file']) ? $node['file'] : '';
			$line = isset($node['line']) ? '(' . $node['line'] . ') ' : '';
			$stack .= '#' . (++$i) . ' ' . $file . $line . ': ';
			if (isset($node['class'])) {
				$stack .= $node['class'] . '->';
			}
			$stack .= $node['function'] . '()' . PHP_EOL;
		}
		
		return $stack;
	}
	
	/**
	 * Debug backtrace
	 * @param string $message
	 * @param array $traceData
	 * @param bool $formatted
	 * @return HTML
	 */
	public static function backtrace($message = '', $traceData = array(), $formatted = true)
	{
		if (APPHP_MODE == 'debug') {
			$stack = self::prepareBacktrace($traceData);
		} else {
			$message = A::t('core', 'A fatal exception has occurred. Program will exit.');
			$stack = A::t('core', 'Backtrace information is available in debug mode');
		}
		
		if ($formatted) {
			$return = '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="utf-8">
				<title>Error</title>
				<style type="text/css">
					::selection { background-color: #E13300; color: white; }
					::-moz-selection { background-color: #E13300; color: white; }
					body { background-color: #fff; margin: 40px; font: 13px/20px normal Helvetica, Arial, sans-serif; color: #4F5155;}
					a {	color: #003399;	background-color: transparent; font-weight: normal;}
					h1 { color: #444; background-color: transparent; border-bottom: 1px solid #D0D0D0; font-size: 19px; font-weight: normal; margin: 0 0 14px 0; padding: 14px 15px 10px 15px;}
					code { font-family: Consolas, Monaco, Courier New, Courier, monospace; font-size: 12px; background-color: #f9f9f9; border: 1px solid #D0D0D0; color: #002166; display: block; margin: 14px 0 14px 0; padding: 12px 10px 12px 10px;}
					#container { margin: 10px; border: 1px solid #D0D0D0; box-shadow: 0 0 8px #D0D0D0; }
					#container-content { padding:10px 20px; }
					p {	margin: 12px 15px 12px 15px; }
					pre { margin: 0px 15px; white-space: pre-wrap; word-wrap: break-word; }
				</style>
			</head>
			<body>
				<div id="container">
					<h1>' . A::t('core', 'An Error Was Encountered') . '</h1>
					<div id="container-content">
						<p>
							' . A::t('core', 'Exception caught') . ':<br>
							' . $message . '
						</p>
						<p>
							' . A::t('core', 'Backtrace') . ':<br>
							<pre>' . $stack . '</pre>
						</p>
					</div>
				</div>
			</body>
			</html>';
		} else {
			$return = $stack;
		}
		
		return $return;
	}
	
	/**
	 * Add SQL connection time
	 * @param float $time
	 * @return void
	 */
	public static function addSqlConnectionTime($time = 0)
	{
		self::$_sqlConnectionTime += (float)$time;
	}

	/**
	 * Add SQL running time
	 * @param float $time
	 * @return void
	 */
	public static function addSqlTime($time = 0)
	{
		self::$_sqlTotalTime += (float)$time;
	}
	
	/**
	 * Add message to the stack
	 * @param string $type
	 * @param string $key
	 * @param string $val
	 * @param string $storeType
	 * @return void
	 */
	public static function addMessage($type = 'params', $key = '', $val = '', $storeType = '')
	{
		if (APPHP_MODE != 'debug') return false;
		
		// Store message in session
		if ($storeType == 'session') {
			$objSession = A::app()->getSession();
			if (!empty($objSession)) {
				$objSession->set('debug-' . $type, $val);
			}
			return false;
		}

		if ($type == 'general') self::$_arrGeneral[$key][] = CFilter::sanitize('string', $val);
		elseif ($type == 'params') self::$_arrParams[$key] = CFilter::sanitize('string', $val);
		elseif ($type == 'errors') self::$_arrErrors[$key][] = CFilter::sanitize('string', $val);
		elseif ($type == 'warnings') self::$_arrWarnings[$key][] = CFilter::sanitize('string', $val);
		elseif ($type == 'queries') self::$_arrQueries[$key][] = CHtml::encode($val);
		else if ($type == 'data') self::$_arrData[$key] = $val;
		elseif ($type == 'console') {
			if (is_array($val)) {
				$value = $val;
			} elseif (is_object($val)) {
				$value = array('class' => get_class($val), 'properties' => get_object_vars($val), 'methods' => get_class_methods($val));
			} else {
				$value = CFilter::sanitize('string', $val);
			}
			
			$key = CFilter::sanitize('string', $key);
			if ($key != '') {
				self::$_arrConsole[$key] = $value;
			} else {
				self::$_arrConsole[] = $value;
			}
		}
	}
	
	/**
	 * Get message from the stack
	 * @param string $type
	 * @param string $key
	 * @return string
	 */
	public static function getMessage($type = 'params', $key = '')
	{
		$output = '';
		
		if ($type == 'errors') $output = isset(self::$_arrErrors[$key]) ? self::$_arrErrors[$key] : '';
		
		return $output;
	}
	
	/**
	 * Display debug info on the screen
	 * @return void
	 */
	public static function displayInfo()
	{
		if (APPHP_MODE != 'debug') return false;
		
		self::$_endTime = CTime::getMicrotime();
		self::$_endMemoryUsage = memory_get_usage();
		$htmlCompression = (CConfig::get('compression.html.enable') === true) ? true : false;
		
		$nl = "\n";
		
		// Retrieve stored error messages and show them, then remove
		if ($debugError = A::app()->getSession()->get('debug-errors')) {
			self::addMessage('errors', 'debug-errors', $debugError);
			A::app()->getSession()->remove('debug-errors');
		}
		if ($debugWarning = A::app()->getSession()->get('debug-warnings')) {
			self::addMessage('warnings', 'debug-warnings', $debugWarning);
			A::app()->getSession()->remove('debug-warnings');
		}
		
		$totalParams = count(self::$_arrParams);
		$totalConsole = count(self::$_arrConsole);
		$totalWarnings = count(self::$_arrWarnings);
		$totalErrors = count(self::$_arrErrors);
		$totalQueries = count(self::$_arrQueries);

        $totalRunningTime = round((float)self::$_endTime - (float)self::$_startTime, 5);
        $totalRunningTimeQueriesSql = round((float)self::$_sqlTotalTime, 5);
        $totalRunningTimeConnectionSql = round((float)self::$_sqlConnectionTime, 5);
        $totalRunningTimeScript = round($totalRunningTime - $totalRunningTimeConnectionSql - $totalRunningTimeQueriesSql, 5);
        $totalMemoryUsage = CConvert::fileSize((float)self::$_endMemoryUsage - (float)self::$_startMemoryUsage);
        $htmlCompressionRate = !empty(self::$_arrData['html-compression-rate']) ? self::$_arrData['html-compression-rate'] : A::t('core', 'Unknown');

        // Debug bar status
		$debugBarState = isset($_COOKIE['debugBarState']) ? $_COOKIE['debugBarState'] : 'min';
		$onDblClick = 'appTabsMinimize()';
		
		$panelAlign = A::app()->getLanguage('direction') == 'rtl' ? 'left' : 'right';
		$panelTextAlign = A::app()->getLanguage('direction') == 'rtl' ? 'right' : 'left';

		$output = $nl . '<style type="text/css">
			#debug-panel {opacity:0.9;position:fixed;bottom:0;left:0;z-index:2000;width:100%;max-height:90%;font:12px tahoma, verdana, sans-serif;color:#000;}
			#debug-panel fieldset {padding:0px 10px;background-color:#fff;border:1px solid #ccc;width:98%;margin:0px auto 0px auto;text-align:' . $panelTextAlign . ';}
			#debug-panel fieldset legend {float:' . $panelAlign . ';background-color:#f9f9f9;padding:5px 5px 4px 5px;border:1px solid #ccc;border-left:1px solid #ddd;border-bottom:1px solid #f4f4f4;margin:-15px 0 0 10px;font:12px tahoma, verdana, sans-serif;width:auto;}
			#debug-panel fieldset legend ul {color:#999;font-weight:normal;margin:0px;padding:0px;}
			#debug-panel fieldset legend ul li{float:left;width:auto;list-style-type:none;}
			#debug-panel fieldset legend ul li.title{min-width:10px;width:auto;padding:0 2px;}
			#debug-panel fieldset legend ul li.narrow{width:auto;padding:0 2px;}
			#debug-panel fieldset legend ul li.item{width:auto;padding:0 12px;border-right:1px solid #999;}
			#debug-panel fieldset legend ul li.item.item-last{margin:' . (A::app()->getLanguage('direction') == 'rtl' ? '0 12px 0 0' : '0 12px 0 0') . ';}
			#debug-panel a {text-decoration:none;text-transform:none;color:#bbb;font-weight:normal;}
			#debug-panel a.debugArrow {color:#222;margin: auto 2px}
			#debug-panel a.black {color:#222;}
            #debug-panel pre {border:0px;}
			#debug-panel strong {font-weight:bold;}
			#debug-panel strong.uppercase {text-transform:uppercase}
			#debug-panel .tab-orange { color:#d15600 !important; }
			#debug-panel .tab-red { color:#cc0000 !important; }
			@media (max-width: 680px) {
				#debug-panel fieldset legend ul li.item.item-clock {display:none;}
				#debug-panel fieldset legend ul li.title > .item-debug {display:none;}				
				#debug-panel fieldset legend ul li.item a {display:block;visibility:hidden;}				
				#debug-panel fieldset legend ul li.item a:first-letter {visibility:visible !important;}
				#debug-panel fieldset legend ul li.item {width:30px; height:15px; margin-bottom:3px;)
			}
		</style>';

		$output .= $nl . '<script type="text/javascript">
			var arrDebugTabs = ["General","Params","Console","Warnings","Errors","Queries"];
			var debugTabsHeight = "200px";
			var cssText = keyTab = "";
			
			function toggleSystemQueries(){var x, i; x = document.getElementsByClassName("dbugQuery");for(i = 0; i < x.length; i++){if(x[i].style.display === "none"){x[i].style.display = "";}else {x[i].style.display = "none";}}}
			function appSetCookie(state, tab){ document.cookie = "debugBarState="+state+"; path=/"; if(tab !== null) document.cookie = "debugBarTab="+tab+"; path=/"; }
			function appGetCookie(name){ if(document.cookie.length > 0){ start_c = document.cookie.indexOf(name + "="); if(start_c != -1){ start_c += (name.length + 1); end_c = document.cookie.indexOf(";", start_c); if(end_c == -1) end_c = document.cookie.length; return unescape(document.cookie.substring(start_c,end_c)); }} return ""; }
			function appTabsMiddle(){ appExpandTabs("middle", appGetCookie("debugBarTab")); }
			function appTabsMaximize(){ appExpandTabs("max", appGetCookie("debugBarTab")); }
			function appTabsMinimize(){ appExpandTabs("min", "General"); }			
			function appTabsClose(){ appExpandTabs("close", appGetCookie("debugBarTab")); }			
			function appExpandTabs(act, key){ 
				if(act == "max"){ debugTabsHeight = "500px"; }
				else if(act == "middle"){ debugTabsHeight = "200px"; }
				else if(act == "min"){ debugTabsHeight = "0px";	}
				else if(act == "close"){ debugTabsHeight = "0px";	}
				else if(act == "auto"){ 
					if(debugTabsHeight == "0px"){ debugTabsHeight = "200px"; act = "middle"; }
					else if(debugTabsHeight == "200px"){ act = "middle"; }
					else if(debugTabsHeight == "500px"){ act = "max"; }
				}
				keyTab = (key == null) ? "General" : key;
				document.getElementById("debugArrowExpand").style.display = ((act == "max") ? "none" : (act == "middle") ? "none" : "");
				document.getElementById("debugArrowCollapse").style.display = ((act == "max") ? "" : (act == "middle") ? "" : "none");
				document.getElementById("debugArrowMaximize").style.display = ((act == "max") ? "none" : (act == "middle") ? "" : "");
				document.getElementById("debugArrowMinimize").style.display = ((act == "max") ? "" : (act == "middle") ? "none" : "none");
				for(var i = 0; i < arrDebugTabs.length; i++){
					if(act == "min" || arrDebugTabs[i] != keyTab){
						document.getElementById("content"+arrDebugTabs[i]).style.display = "none";
						document.getElementById("tab"+arrDebugTabs[i]).style.cssText = "color:#bbb;";
					}
				}
				if(act != "min"){
				    x = document.getElementsByClassName("item");
				    if(act == "close"){
                        for (i = 0; i < x.length; i++) x[i].style.display = "none";
                        document.getElementsByClassName("narrow-close")[0].style.display = "none";
				    }else{
                        for (i = 0; i < x.length; i++) x[i].style.display = "";
                        document.getElementsByClassName("narrow-close")[0].style.display = "";
				    }
					document.getElementById("content"+keyTab).style.display = "";
					document.getElementById("content"+keyTab).style.cssText = "width:100%;height:"+debugTabsHeight+";overflow-y:auto;";
					if(document.getElementById("tab"+keyTab).className == "tab-orange"){
						cssText = "color:#b13600 !important;";
					}else if(document.getElementById("tab"+keyTab).className == "tab-red"){
						cssText = "color:#aa0000 !important;";
					}else{
						cssText = "color:#222;";	
					}
					document.getElementById("tab"+keyTab).style.cssText = cssText;
				}
				document.getElementById("debug-panel").style.opacity = (act == "min") ? "0.9" : "1";
				appSetCookie(act, key);
			}
		</script>';
		
		$output .= $nl . '<div id="debug-panel">
		<fieldset>
		<legend id="debug-panel-legend">
			<ul>
				<li class="title" style="display:flex">
				    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAmRJREFUeNpcUz1vE0EQfbu3Pp99jo/YB7FkCloKoIlQYuAPhIICBEIgJ3TIRkKiAqeBnhCFAhoiWUJCKBREiCYJHxWiSIWE4CcgURDZDo4/7oPZuTs75qS73Zud9/btzhthu+Zcr+19FRKQhgCEoJEGSfNDTxiECHw90WNI/4CVV/PCMGWogcIYA6URvYcfDYqAEVEY/wtlEYGSDJZKwNDzlGCi3IyJi49PotfysFn7HpEMI6BPoyZRMt6ZwaaEkZI86ljhhE0JafT3hlCWgcAL4IsQGASsyqfjKC1d75aAr708w4vv7v6AU8rCVGkM2/u4sn6K4xuL3+grEfb96Nh0Z6MzL76uwErZeH/vJxQRFsoO0ioL7y84pteqryoEHGNkcmn1zQXY5hFs1HZJCSlKS7jlaQb1Wz7H3tR3Oaf+doF3B2uhp/GxiimriBfVLSZLSIslF5ay0et4fEc6tr60zbmNnSp06ZUmyGdcaL7JwgHHSjNImQa6rd5E3KH8MJ6zgvvnnyBvHcXKziOuc0i3m3Ns5HPTyKTyaP/usHEojJWth5z74MIqx5hA17M+16CFIp5/eMq1dgoOMqZNJTNw0BlQ3QM8217jnDvzy4xhBSOHkbuWZmt0SQ6an5souvr8U+j+GcDrB2h+avLarbO12I2RKxVLo5dNQnqunr5BnhC4fPsSVSBL8rvwDnxcn71JOSE8MlHiRm1pxZ6WI8dHZL5A6XiZTJTF3q8OK2CAF1mZSfzIyqNm0v4fmSNuLiGTThw3z/jIcTNRO1f6+94XISY7Ukj8186Y6ERdqXROnfsnwADuWSOCrzpCtgAAAABJRU5ErkJggg==" alt="logo" />
				    <b class="item-debug" style="color:#222;margin-left:7px;">' . A::t('core', 'Debug') . ':&nbsp;</b>
				</li>				

				<li class="item item-clock">&#9716 ' . round($totalRunningTime, 3) . ' ' . A::t('core', 'sec') . '</li>				
				<li class="item"><a id="tabGeneral" href="javascript:void(\'General\')" onclick="javascript:appExpandTabs(\'auto\', \'General\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'General') . '</a></li>
				<li class="item"><a id="tabParams" href="javascript:void(\'Params\')" onclick="javascript:appExpandTabs(\'auto\', \'Params\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'Params') . ' (' . $totalParams . ')</a></li>
				<li class="item"><a id="tabConsole" href="javascript:void(\'Console\')" onclick="javascript:appExpandTabs(\'auto\', \'Console\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'Console') . ' (' . $totalConsole . ')</a></li>
				<li class="item"><a id="tabWarnings" href="javascript:void(\'Warnings\')" ' . ($totalWarnings ? 'class="tab-orange"' : '') . ' onclick="javascript:appExpandTabs(\'auto\', \'Warnings\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'Warnings') . ' (' . $totalWarnings . ')</a></li>
				<li class="item"><a id="tabErrors" href="javascript:void(\'Errors\')" ' . ($totalErrors ? 'class="tab-red"' : '') . ' onclick="javascript:appExpandTabs(\'auto\', \'Errors\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'Errors') . ' (' . $totalErrors . ')</a></li>
				<li class="item item-last"><a id="tabQueries" href="javascript:void(\'Queries\')" onclick="javascript:appExpandTabs(\'auto\', \'Queries\')" ondblclick="javascript:' . $onDblClick . '">' . A::t('core', 'SQL Queries') . ' (' . $totalQueries . ')</a></li>

				<li class="narrow"><a id="debugArrowExpand" class="debugArrow" style="display:;" href="javascript:void(0)" title="Expand" onclick="javascript:appTabsMiddle()">&#9650;</a></li>
				<li class="narrow"><a id="debugArrowCollapse" class="debugArrow" style="display:none;" href="javascript:void(0)" title="Collapse" onclick="javascript:appTabsMinimize()">&#9660;</a></li>
				<li class="narrow"><a id="debugArrowMaximize" class="debugArrow" style="display:;" href="javascript:void(0)" title="Maximize" onclick="javascript:appTabsMaximize()">&#9744;</a></li>
				<li class="narrow"><a id="debugArrowMinimize" class="debugArrow" style="display:none;" href="javascript:void(0)" title="Minimize" onclick="javascript:appTabsMiddle()">&#9635;</a></li>
				<li class="narrow narrow-close"><a class="debugArrow" href="javascript:void(0)" onclick="javascript:appTabsClose()" title="Close">&times;</a></li>
			</ul>
		</legend>
		
		<div id="contentGeneral" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		
		$output .= A::t('core', 'Script name') . ': ' . CConfig::get('name') . '<br>';
		$output .= A::t('core', 'Script version') . ': ' . CConfig::get('version') . '<br>';
		$output .= A::t('core', 'Framework version') . ': ' . A::getVersion() . '<br>';
		$output .= A::t('core', 'Directy CMF version') . ': ' . CConfig::get('directy_cmf_version') . '<br>';
		$output .= A::t('core', 'PHP version') . ': ' . phpversion() . '<br>';
		$output .= (CConfig::get('db.driver') != '' ? ucfirst(CConfig::get('db.driver')) . ' ' . A::t('core', 'version') . ': ' . CDatabase::init()->getVersion() : 'DB: ' . A::te('core', 'no')) . '<br><br>';
		
		$output .= A::t('core', 'Total running time') . ': ' . $totalRunningTime . ' ' . A::t('core', 'sec') . '.<br>';
		$output .= A::t('core', 'SQL connection running time') . ': ' . $totalRunningTimeConnectionSql . ' ' . A::t('core', 'sec') . '.<br>';
		$output .= A::t('core', 'SQL queries running time') . ': ' . $totalRunningTimeQueriesSql . ' ' . A::t('core', 'sec') . '.<br>';
		$output .= A::t('core', 'Script running time') . ': ' . $totalRunningTimeScript . ' ' . A::t('core', 'sec') . '.<br>';
		$output .= A::t('core', 'Total memory usage') . ': ' . $totalMemoryUsage . '<br><br>';
		
		$dbCacheFiles = CConfig::get('cache.db.enable') ? CFile::getDirectoryFilesCount(CConfig::get('cache.db.path'), '.cch') : 0;
		$cssMinifyFiles = CConfig::get('compression.css.enable') ? CFile::getDirectoryFilesCount(CConfig::get('compression.css.path'), '.css') : 0;
		$jsMinifyFiles = CConfig::get('compression.js.enable') ? CFile::getDirectoryFilesCount(CConfig::get('compression.js.path'), '.js') : 0;
		$totalMinifyFiles = $cssMinifyFiles + $jsMinifyFiles;
		$cssMinifiedFiles = A::app()->getClientScript()->countCompressedFiles('css');
		$jsMinifiedFiles = A::app()->getClientScript()->countCompressedFiles('js');
		
		if (!empty(self::$_arrGeneral['cache'])) $output .= A::t('core', 'Database Query Cache') . ': ' . implode('', self::$_arrGeneral['cache']) . ' - (' . $dbCacheFiles . ' ' . ($dbCacheFiles == 1 ? A::t('core', 'file') : A::t('core', 'files')) . ')<br>';
		$output .= 'GZip ' . A::t('core', 'Output compression') . ': ' . (CConfig::get('compression.gzip.enable') ? A::t('core', 'enabled') : A::t('core', 'no')) . '<br>';
		$output .= 'HTML ' . A::t('core', 'Output compression') . ': ' . (CConfig::get('compression.html.enable') ? A::t('core', 'enabled') . ' (' . A::t('core', 'compression rate') . ': ' . $htmlCompressionRate . ')' : A::t('core', 'no')) . '<br>';
		$output .= 'CSS ' . A::t('core', 'Output compression') . ': ' . (CConfig::get('compression.css.enable') ? A::t('core', 'enabled') . ' - (' . $cssMinifiedFiles . ' ' . ($cssMinifiedFiles == 1 ? A::t('core', 'file') : A::t('core', 'files')) . ')' : A::t('core', 'no')) . '<br>';
		$output .= 'JS ' . A::t('core', 'Output compression') . ': ' . (CConfig::get('compression.js.enable') ? A::t('core', 'enabled') . ' - (' . $jsMinifiedFiles . ' ' . ($jsMinifiedFiles == 1 ? A::t('core', 'file') : A::t('core', 'files')) . ')' : A::t('core', 'no')) . '<br>';
		
		$output .= A::t('core', 'Action') . ': [ <a href="index/clear/type/session_and_cookie" class="black">' . A::t('core', 'Clear Session & Cookies') . '</a> ] <br>';
		$output .= A::t('core', 'Action') . ': [ <a href="index/clear/type/cache_and_minified" class="black">' . A::t('core', 'Clear Cache & Minified') . ' - ' . $totalMinifyFiles . ' ' . ($totalMinifyFiles == 1 ? A::t('core', 'file') : A::t('core', 'files')) . '</a> ] <br><br>';
		
		if (count(self::$_arrGeneral) > 0) {
			$output .= '<strong class="uppercase">'.A::t('core', 'Loaded components').'</strong>:';
			$output .= '<pre>';
			$components = isset(self::$_arrGeneral['components']) ? print_r(self::$_arrGeneral['components'], true) : '';
			$output .= $htmlCompression ? nl2br($components) : $components;
			$output .= '</pre>';
			$output .= '<br>';
			$output .= '<strong class="uppercase">'.A::t('core', 'Loaded classes').'</strong>:';
			$output .= '<pre>';
			$classes = isset(self::$_arrGeneral['classes']) ? print_r(self::$_arrGeneral['classes'], true) : '';
			$output .= $htmlCompression ? nl2br($classes) : $classes;
			$output .= '</pre>';
			$output .= '<br>';
			$output .= '<strong class="uppercase">'.A::t('core', 'Included files').'</strong>:';
			$output .= '<pre>';
			$included = isset(self::$_arrGeneral['included']) ? print_r(self::$_arrGeneral['included'], true) : '';
			$output .= $htmlCompression ? nl2br($included) : $included;
			$output .= '</pre>';
			$output .= '<br>';
		}
		$output .= '</div>
	
		<div id="contentParams" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		
		$output .= '<strong class="uppercase">'.A::t('core', 'Application').'</strong>:';
		if ($totalParams > 0) {
			$output .= '<pre>';
			$arrParams = print_r(self::$_arrParams, true);
			$output .= $htmlCompression ? nl2br($arrParams) : $arrParams;
			$output .= '</pre><br>';
		}
		
		$output .= '<strong>$_GET</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrGet = array();
		if (isset($_GET)) {
			foreach ($_GET as $key => $val) {
				$arrGet[$key] = is_array($val) ? $val : strip_tags($val);
			}
		}
		$arrGet = print_r($arrGet, true);
		$output .= $htmlCompression ? nl2br($arrGet) : $arrGet;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>$_POST</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrPost = array();
		if (isset($_POST)) {
			foreach ($_POST as $key => $val) {
				$arrPost[$key] = is_array($val) ? $val : strip_tags($val);
			}
		}
		$arrPost = print_r($arrPost, true);
		$output .= $htmlCompression ? nl2br($arrPost) : $arrPost;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>$_FILES</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrFiles = array();
		if (isset($_FILES)) {
			foreach ($_FILES as $key => $val) {
				$arrFiles[$key] = is_array($val) ? $val : strip_tags($val);
			}
		}
		$arrFiles = print_r($arrFiles, true);
		$output .= $htmlCompression ? nl2br($arrFiles) : $arrFiles;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>$_COOKIE</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrCookie = array();
		if (isset($_COOKIE)) {
			foreach ($_COOKIE as $key => $val) {
				$arrCookie[$key] = is_array($val) ? $val : strip_tags($val);
			}
		}
		$arrCookie = print_r($arrCookie, true);
		$output .= $htmlCompression ? nl2br($arrCookie) : $arrCookie;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>$_SESSION</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrSession = array();
		if (isset($_SESSION)) {
			foreach ($_SESSION as $key => $val) {
				$arrSession[$key] = is_array($val) ? $val : strip_tags($val);
			}
		}
		$arrSession = print_r($arrSession, true);
		$output .= $htmlCompression ? nl2br($arrSession) : $arrSession;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>CONSTANTS</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrConstants = @get_defined_constants(true);
		$arrUserConstants = isset($arrConstants['user']) ? print_r($arrConstants['user'], true) : array();
		$output .= $htmlCompression ? nl2br($arrUserConstants) : $arrUserConstants;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '<strong>VIEW VARIABLES</strong>:';
		$output .= '<pre style="white-space:pre-wrap;">';
		$arrViewVars = A::app()->view->getAllVars();
		$arrViewVars = print_r(@array_map('htmlspecialchars', $arrViewVars), true);
		$output .= $htmlCompression ? nl2br($arrViewVars) : $arrViewVars;
		$output .= '</pre>';
		$output .= '<br>';
		
		$output .= '</div>
	
		<div id="contentConsole" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		if ($totalConsole > 0) {
			$output .= '<pre>';
			$arrConsole = print_r(self::$_arrConsole, true);
			$output .= $htmlCompression ? nl2br($arrConsole) : $arrConsole;
			$output .= '</pre>';
		}
		$output .= '</div>
		
		<div id="contentWarnings" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		if ($totalWarnings > 0) {
			foreach (self::$_arrWarnings as $warnKey => $warnVal) {
				$output .= '<strong>' . CString::humanize($warnKey) . '</strong><br>';
				$output .= '<pre>';
				if (is_array($warnVal)) {
					foreach ($warnVal as $warnValVal) {
						$output .= '- ' . $warnValVal . '<br>';
					}
				} else {
					$output .= '- ' . $warnVal[0] . '<br>';
				}
				$output .= '</pre>';
			}
			$output .= '<br>';
		}
		$output .= '</div>
		
		<div id="contentErrors" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		if ($totalErrors > 0) {
			foreach (self::$_arrErrors as $msg) {
				$output .= '<pre style="white-space:normal;word-wrap:break-word;">';
				$msg = print_r($msg, true);
				$output .= $htmlCompression ? nl2br($msg) : $msg;
				$output .= '</pre>';
				$output .= '<br>';
			}
		}
		$output .= '</div>
	
		<div id="contentQueries" style="display:none;padding:10px;width:100%;height:200px;overflow-y:auto;">';
		if ($totalQueries > 0) {
			$output .= A::t('core', 'SQL queries running time') . ': ' . $totalRunningTimeQueriesSql . ' sec.  <br><input type="checkbox" id="dbugToggleSysQueries" style="margin:'.($panelTextAlign == 'left' ? '5px 5px 0px 0px' : '5px 0px 0px 5px').';" onclick="toggleSystemQueries()" /><label for="dbugToggleSysQueries">Hide system queries</label><br><br>';
            $output .= '<ol style="padding-left:20px;">';
			foreach (self::$_arrQueries as $msgKey => $msgVal) {
                $dbugQuery = preg_match('/(show|truncate)/i', $msgKey);
			    $output .= '<li'.($dbugQuery ? ' class="dbugQuery"' : '').'>';
                $output .= preg_replace('#<span[^>]*>.*?</span>#si', '', $msgKey) . '<br>';
				$output .= $msgVal[0] . '<br><br>';
                $output .= '</li>';
			}
            $output .= '</ol>';
		}
		$output .= '</div>
	
		</fieldset>
		</div>';
		
		if ($debugBarState == 'max') {
			$output .= '<script type="text/javascript">appTabsMaximize();</script>';
		} elseif ($debugBarState == 'middle') {
			$output .= '<script type="text/javascript">appTabsMiddle();</script>';
		} elseif ($debugBarState == 'close') {
			$output .= '<script type="text/javascript">appTabsClose();</script>';
		} else {
			$output .= '<script type="text/javascript">appTabsMinimize();</script>';
		}
		
		// Compress output
		if (CConfig::get('compression.html.enable') === true) {
			$output = CMinify::html($output);
		}
		
		echo $output;
	}
}