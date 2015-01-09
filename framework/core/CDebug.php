<?php
/**
 * CDebug core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init                                                 _getFormattedMicrotime
 * display
 * d
 * write
 * addMessage
 * getMessage
 * displayInfo
 * 
 */	  

class CDebug
{
	/** @var string */
    private static $_startTime;
	/** @var string */
    private static $_endTime;
	/** @var array */
	private static $_arrGeneral;
	/** @var array */
    private static $_arrParams;
	/** @var array */
    private static $_arrWarnings;    
	/** @var array */
    private static $_arrErrors;
	/** @var array */
	private static $_arrQueries;
    

    /**
     * Class init constructor
     */
    public static function init()
    {
        if(APPHP_MODE != 'debug') return false;
        
        self::$_startTime = self::_getFormattedMicrotime();
    }

    /**
     * Alias to method 'display'
     * @param mixed $param
     * @param bool $terminate
     * @return HTML dump
     */
    public static function d($param, $terminate = false)
    {
        self::display($param, $terminate);
    }

    /**
     * Displays parameter on the screen
     * @param mixed $param
     * @param bool $terminate
     * @return HTML dump
     */
    public static function display($param, $terminate = false)
    {
        if($terminate) echo '<!DOCTYPE html><head><meta charset="UTF-8" /></head><html><body>';
        echo '<pre>';
        print_r($param);
        echo '</pre>';
        if($terminate){ echo '</body></html>'; exit(0); }
    }

    /**
     * Write string to the debug stack
     * @param string $val
     * @param string $storeType
     */
    public static function write($val = '', $key = '', $storeType = '')
    {
        if($key == '') $key = 'console-write-'.CHash::getRandomString(4);
        self::addMessage('general', $key, $val, $storeType);
    }
    
    /**
     * Add message to the stack
     * @param string $type
     * @param string $key
     * @param string $val
     * @param string $storeType
     */
    public static function addMessage($type = 'params', $key = '', $val = '', $storeType = '')
    {
        if(APPHP_MODE != 'debug') return false;
        
        if($storeType == 'session'){
            A::app()->getSession()->set('debug-'.$type, $val);
        }
		
        if($type == 'general') self::$_arrGeneral[$key][] = CFilter::sanitize('string', $val);
		else if($type == 'params') self::$_arrParams[$key] = CFilter::sanitize('string', $val);
        else if($type == 'errors') self::$_arrErrors[$key][] = CFilter::sanitize('string', $val);
		else if($type == 'warnings') self::$_arrWarnings[$key][] = CFilter::sanitize('string', $val);
		else if($type == 'queries') self::$_arrQueries[$key][] = CFilter::sanitize('string', $val);
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
		
        if($type == 'errors') $output = isset(self::$_arrErrors[$key]) ? self::$_arrErrors[$key] : '';

		return $output;
    }    
    
    /**
     * Display debug info on the screen
     */
    public static function displayInfo()
    {
        if(APPHP_MODE != 'debug') return false;
		
        self::$_endTime = self::_getFormattedMicrotime();        

		$nl = "\n";
        
        // retrieve stored error messages and show them, then remove
        if($debugError = A::app()->getSession()->get('debug-errors')){
            //self::addMessage('errors', 'debug-errors', $debugError);
            A::app()->getSession()->remove('debug-errors');
        }
        if($debugWarning = A::app()->getSession()->get('debug-warnings')){
            //self::addMessage('warnings', 'debug-warnings', $debugWarning);
            A::app()->getSession()->remove('debug-warnings');
 		}		

        $panelAlign = (A::app()->getLanguage('direction') == 'rtl') ? 'left' : 'right';
        $panelTextAlign = (A::app()->getLanguage('direction') == 'rtl') ? 'right' : 'left';		
		echo $nl.'<style type="text/css" >
			#debug-panel {opacity:0.9;position:fixed;bottom:0;left:0;z-index:2000;width:100%;max-height:90%;font:12px tahoma, verdana, sans-serif;color:#000;}
			#debug-panel fieldset {padding:0px 10px;background-color:#fff;border:1px solid #ccc;width:98%;margin:0px auto 0px auto;text-align:'.$panelTextAlign.';}
			#debug-panel fieldset legend {background-color:#f9f9f9;padding:5px 10px 4px 10px;border:1px solid #ccc;border-left:1px solid #ddd;border-bottom:1px solid #f4f4f4;margin:0 0 0 10px;font:12px tahoma, verdana, sans-serif;width:auto;}
			#debug-panel fieldset legend span {color:#999;font-weight:normal}
			#debug-panel a {text-decoration:none;color:#bbb;font-weight:normal;}
			#debug-panel a.debugArrow {color:#222;}
            #debug-panel pre {border:0px;}
		</style>
		<script type="text/javascript">
			var arrDebugTabs = ["General","Params","Warnings","Errors","Queries"];
			var debugTabsHeight = "200px";
			function appSetCookie(state, tab){ document.cookie = "debugBarState="+state+"; path=/"; if(tab !== null) document.cookie = "debugBarTab="+tab+"; path=/"; }
			function appGetCookie(name){ if(document.cookie.length > 0){ start_c = document.cookie.indexOf(name + "="); if(start_c != -1){ start_c += (name.length + 1); end_c = document.cookie.indexOf(";", start_c); if(end_c == -1) end_c = document.cookie.length; return unescape(document.cookie.substring(start_c,end_c)); }} return ""; }
			function appTabsMiddle(){ appExpandTabs("middle", appGetCookie("debugBarTab")); }
			function appTabsMaximize(){ appExpandTabs("max", appGetCookie("debugBarTab")); }
			function appTabsMinimize(){ appExpandTabs("min", "General"); }			
			function appExpandTabs(act, key){ 
				if(act == "max"){ debugTabsHeight = "500px"; }
				else if(act == "middle"){ debugTabsHeight = "200px"; }
				else if(act == "min"){ debugTabsHeight = "0px";	}
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
					document.getElementById("content"+keyTab).style.display = "";
					document.getElementById("content"+keyTab).style.cssText = "height:"+debugTabsHeight+";overflow-y:auto;";
					document.getElementById("tab"+keyTab).style.cssText = "color:#222;";
				}
				document.getElementById("debug-panel").style.opacity = (act == "min") ? "0.9" : "1";
				appSetCookie(act, key);
			}
		</script>
		
		<div id="debug-panel">
		<fieldset>
		<legend id="debug-panel-legend" align="'.$panelAlign.'">
			<b style="color:#222">'.A::t('core', 'Debug').'</b>:&nbsp;
			<a id="debugArrowExpand" class="debugArrow" style="display:;" href="javascript:void(0)" title="Expand" onclick="javascript:appTabsMiddle()">&#9650;</a>
			<a id="debugArrowCollapse" class="debugArrow" style="display:none;" href="javascript:void(0)" title="Collapse" onclick="javascript:appTabsMinimize()">&#9660;</a>
			<a id="debugArrowMaximize" class="debugArrow" style="display:;" href="javascript:void(0)" title="Maximize" onclick="javascript:appTabsMaximize()">&#9744;</a>
			<a id="debugArrowMinimize" class="debugArrow" style="display:none;" href="javascript:void(0)" title="Minimize" onclick="javascript:appTabsMiddle()">&#9635;</a>
			<span>
				&nbsp;<a id="tabGeneral" href="javascript:void(\'General\')" onclick="javascript:appExpandTabs(\'auto\', \'General\')">'.A::t('core', 'General').'</a> &nbsp;|&nbsp;
				&nbsp;<a id="tabParams" href="javascript:void(\'Params\')" onclick="javascript:appExpandTabs(\'auto\', \'Params\')">'.A::t('core', 'Params').' ('.count(self::$_arrParams).')</a> &nbsp;|&nbsp;
				&nbsp;<a id="tabWarnings" href="javascript:void(\'Warnings\')" onclick="javascript:appExpandTabs(\'auto\', \'Warnings\')">'.A::t('core', 'Warnings').' ('.count(self::$_arrWarnings).')</a> &nbsp;|&nbsp;
				&nbsp;<a id="tabErrors" href="javascript:void(\'Errors\')" onclick="javascript:appExpandTabs(\'auto\', \'Errors\')">'.A::t('core', 'Errors').' ('.count(self::$_arrErrors).')</a> &nbsp;|&nbsp;
				&nbsp;<a id="tabQueries" href="javascript:void(\'Queries\')" onclick="javascript:appExpandTabs(\'auto\', \'Queries\')">'.A::t('core', 'SQL Queries').' ('.count(self::$_arrQueries).')</a>
			</span>				
		</legend>
		
		<div id="contentGeneral" style="display:none;padding:10px;height:200px;overflow-y:auto;">
			'.A::t('core', 'Total running time').': '.round((float)self::$_endTime - (float)self::$_startTime, 6).' sec.<br>
			'.A::t('core', 'Framework v').A::getVersion().'<br>';
			if(count(self::$_arrGeneral) > 0){
				echo '<pre>';
				print_r(self::$_arrGeneral);
				echo '</pre>';            
			}			
			echo 'POST:';
			echo '<pre style="white-space:pre-wrap;">';
            $arrPost = array();
			if(isset($_POST)){
                foreach($_POST as $key => $val){
                    $arrPost[$key] = is_array($val) ? $val : strip_tags($val);
                }
            }
            print_r($arrPost);
			echo '</pre>';            
		echo '</div>
	
		<div id="contentParams" style="display:none;padding:10px;height:200px;overflow-y:auto;">';
			if(count(self::$_arrParams) > 0){
				echo '<pre>';
				print_r(self::$_arrParams);
				echo '</pre><br>';            
			}
		echo '</div>
	
		<div id="contentWarnings" style="display:none;padding:10px;height:200px;overflow-y:auto;">';
			if(count(self::$_arrWarnings) > 0){
				echo '<pre>';
				print_r(self::$_arrWarnings);
				echo '</pre>';            
			}
		echo '</div>
	
		<div id="contentErrors" style="display:none;padding:10px;height:200px;overflow-y:auto;">';
			if(count(self::$_arrErrors) > 0){
				foreach(self::$_arrErrors as $msg){
					echo '<pre style="white-space:normal;word-wrap:break-word;">';
                    print_r($msg);
                    echo '</pre><br>';
                }               
			}
		echo '</div>
	
		<div id="contentQueries" style="display:none;padding:10px;height:200px;overflow-y:auto;">';
			if(count(self::$_arrQueries) > 0){
				foreach(self::$_arrQueries as $msgKey => $msgVal){
					echo $msgKey.'<br>';
					echo $msgVal[0].'<br><br>';
                }               
			}
		echo '</div>
	
		</fieldset>
		</div>';
		
		$debugBarState = isset($_COOKIE['debugBarState']) ? $_COOKIE['debugBarState'] : 'min';
		//echo isset($_COOKIE['debugBarState']) ? $_COOKIE['debugBarState'] : '--';
		if($debugBarState == 'max'){
			echo '<script type="text/javascript">appTabsMaximize();</script>';
		}else if($debugBarState == 'middle'){
			echo '<script type="text/javascript">appTabsMiddle();</script>';
		}else{
			echo '<script type="text/javascript">appTabsMinimize();</script>';
		}
    }
    
    /**
     * Get formatted microtime
     * @return float
     */
    private static function _getFormattedMicrotime()
    {
        if(APPHP_MODE != 'debug') return false;
        
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }    

}