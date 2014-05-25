<?php
/**
 * CClientScript manages JavaScript and CSS stylesheets for views
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * registerCssFile
 * registerCss
 * registerScriptFile
 * registerScript
 * registerCoreScript
 * render
 * renderHead
 * renderBodyBegin
 * renderBodyEnd
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * 
 */	  

class CClientScript extends CComponent
{
	/** The script is rendered in the <head>  */
	const POS_HEAD = 0;
	/** The script is rendered at the beginning of the <body>  */
	const POS_BODY_BEGIN = 1;
	/** The script is rendered at the end of the <body>  */
	const POS_BODY_END = 2;
	/** The script is rendered inside window onload function */
	const POS_ON_LOAD = 3;
	/** The body script is rendered inside a jQuery ready function */
	const POS_JQUERY_READY = 4;
	/** The script is rendered inside document ready function */
	const POS_DOC_READY = 5;
    
	/** @var boolean */
	public $enableJavaScript = true;
	/** @var array */
	protected $_cssFiles = array();
	/** @var array */
	protected $_css = array();
	/** @var array */
	protected $_scriptFiles = array();
	/** @var array */
	protected $_scripts = array();
	/** @var boolean */
	protected $_hasScripts = false;
    
	
    /**
	 * Class default constructor
	 */
	function __construct()
	{
        
    }

    /**
     *	Returns the instance of object
     *	@return CClientScript class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}

	/**
	 * Registers a CSS file
	 * @param string $url 
	 * @param string $media 
	 */
	public function registerCssFile($url, $media = '')
	{
		$this->_hasScripts = true;
		$this->_cssFiles[$url] = $media;
	}

	/**
	 * Registers a piece of CSS code
	 * @param string $id 
	 * @param string $css 
	 * @param string $media 
	 */
	public function registerCss($id, $css, $media = '')
	{
		$this->_hasScripts = true;
		$this->_css[$id] = array($css, $media);
	}
    
	/**
	 * Registers a required javascript file
	 * @param string $url
	 * @param integer $position 
	 */
	public function registerScriptFile($url, $position = self::POS_HEAD)
	{
		$this->_hasScripts = true;
		$this->_scriptFiles[$position][$url] = $url;
	}

	/**
	 * Registers a piece of javascript code
	 * @param string $id 
	 * @param string $script 
	 * @param integer $position
	 */
	public function registerScript($id, $script, $position = self::POS_JQUERY_READY)
	{
		$this->_hasScripts = true;
		$this->_scripts[$position][$id] = $script;
		if($position === self::POS_JQUERY_READY || $position === self::POS_ON_LOAD)  $this->registerCoreScript('jquery');
	}
	
	/**
	 * Registers a core script package
	 * @param string $name
	 */
	public function registerCoreScript($name)
	{
	    // registers core script	
	}
	
	/**
	 * Renders the registered scripts in our class
	 * This method is called in View->render() class
	 * @param string &$output 
	 */
	public function render(&$output)
	{
		if(!$this->_hasScripts) return;
        $this->renderHead($output);    
		if($this->enableJavaScript){
			$this->renderBodyBegin($output);
			$this->renderBodyEnd($output);
		}
    }
    
	/**
	 * Inserts the js scripts/css in the head section
	 * @param string &$output 
	 */
	public function renderHead(&$output)
	{
		$html = '';
		foreach($this->_cssFiles as $url=>$media){
            $html .= CHtml::cssFile($url, $media)."\n";
        }
		foreach($this->_css as $css){
            $html .= CHtml::css($css[0], $css[1])."\n";        
        }

		if($this->enableJavaScript){			
			if(isset($this->_scriptFiles[self::POS_HEAD])){
				foreach($this->_scriptFiles[self::POS_HEAD] as $scriptFile){
					$html .= CHtml::scriptFile($scriptFile)."\n";
				}
			}
			if(isset($this->_scripts[self::POS_HEAD])){
				$html .= CHtml::script(implode("\n", $this->_scripts[self::POS_HEAD]))."\n";
			}
		}
		
		if($html !== ''){
			$count = 0;
			$output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<%%%head%%%>$1', $output, 1, $count);
			if($count){
				$output = str_replace('<%%%head%%%>', $html, $output);
			}else{
				$output = $html.$output;
			}
		}
	}

	/**
	 * Inserts the scripts at the beginning of the <body>
	 * @param string &$output 
	 */
	public function renderBodyBegin(&$output)
	{
		$html = '';
		if(isset($this->_scriptFiles[self::POS_BODY_BEGIN])){
			foreach($this->_scriptFiles[self::POS_BODY_BEGIN] as $scriptFile){
				$html .= CHtml::scriptFile($scriptFile)."\n";
			}
		}
		if(isset($this->_scripts[self::POS_BODY_BEGIN])){
			$html .= CHtml::script(implode("\n", $this->_scripts[self::POS_BODY_BEGIN]))."\n";
		}

		if($html !== ''){
			$count = 0;
			$output = preg_replace('/(<body\b[^>]*>)/is', '$1<%%%begin%%%>', $output, 1, $count);
			if($count){
				$output = str_replace('<%%%begin%%%>', $html, $output);
			}else{
				$output = $html.$output;
			}
		}		
	}
	
	/**
	 * Inserts the scripts at the end of the <body>
	 * @param string &$output 
	 */
	public function renderBodyEnd(&$output)
	{
		if(!isset($this->_scriptFiles[self::POS_BODY_END]) &&
		   !isset($this->_scripts[self::POS_BODY_END]) &&
		   !isset($this->_scripts[self::POS_JQUERY_READY]) &&
		   !isset($this->_scripts[self::POS_DOC_READY]) &&
		   !isset($this->_scripts[self::POS_ON_LOAD]))
			return;
		
		$completePage = 0;
		$output = preg_replace('/(<\\/body\s*>)/is', '<%%%end%%%>$1', $output, 1, $completePage);
		$html = '';
		if(isset($this->_scriptFiles[self::POS_BODY_END])){
			foreach($this->_scriptFiles[self::POS_BODY_END] as $scriptFile){
				$html .= CHtml::scriptFile($scriptFile)."\n";
			}
		}
		
		$scripts = isset($this->_scripts[self::POS_BODY_END]) ? $this->_scripts[self::POS_BODY_END] : array(); 
		if(isset($this->_scripts[self::POS_JQUERY_READY])){
			if($completePage){
				$scripts[] = "jQuery(function($){\n".implode("\n",$this->_scripts[self::POS_JQUERY_READY])."\n});";
			}else{
				$scripts[] = implode("\n",$this->_scripts[self::POS_JQUERY_READY]);
			}
		}
		if(isset($this->_scripts[self::POS_DOC_READY])){
			if($completePage){
				$scripts[] = "jQuery(document).ready(function(){\n".implode("\n", $this->_scripts[self::POS_DOC_READY])."\n});";
			}else{
				$scripts[] = implode("\n",$this->_scripts[self::POS_DOC_READY]);
			}			
		}
		if(isset($this->_scripts[self::POS_ON_LOAD])){
			if($completePage){
				$scripts[] = "jQuery(window).load(function(){\n".implode("\n", $this->_scripts[self::POS_ON_LOAD])."\n});";
			}else{
				$scripts[] = implode("\n",$this->_scripts[self::POS_ON_LOAD]);
			}
		}
		if(!empty($scripts)) $html = CHtml::script(implode("\n", $scripts))."\n";
		
		if($completePage){
			$output = str_replace('<%%%end%%%>', $html, $output);
		}else{
			$output = $output.$html;		
		}
	}	
    
}