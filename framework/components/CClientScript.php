<?php
/**
 * CClientScript manages JavaScript and CSS stylesheets for views
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ----------               ----------                  ----------
 * __construct                _cssToCompression            _sortByLevel
 * init (static)            _jsToCompression
 * registerCss
 * registerCssFile
 * registerCssFiles
 * registerScript
 * registerScriptFile
 * registerScriptFiles
 * registerCoreScript
 * render
 * renderHead
 * renderBodyBegin
 * renderBodyEnd
 * countCompressedFiles
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
	
	/** The limit in amount of css minify files */
	const CSS_MINIFY_LIMIT = 100;
	/** The limit in amount of js minify files */
	const JS_MINIFY_LIMIT = 100;
	
	/** @var boolean */
	public $enableJavaScript = true;
	/** @var array */
	protected $_cssFiles = array();
	/** @var int */
	protected $_countCompressedCssFiles = 0;
	/** @var array */
	protected $_css = array();
	/** @var array */
	protected $_scriptFiles = array();
	/** @var int */
	protected $_countCompressedJsFiles = 0;
	/** @var array */
	protected $_scripts = array();
	/** @var boolean */
	protected $_hasStyles = false;
	/** @var boolean */
	protected $_hasScripts = false;
	/** @var string */
	protected $_countJsFiles = 0;
	/** @var string */
	protected $_countCssFiles = 0;
	
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
	
	}
	
	/**
	 *    Returns the instance of object
	 * @return current class
	 */
	public static function init()
	{
		return parent::init(__CLASS__);
	}
	
	/**
	 * Registers a piece of CSS code
	 * @param string $id
	 * @param string $css
	 * @param string $media
	 * @return void
	 */
	public function registerCss($id, $css, $media = '')
	{
		if (!empty($css)) {
			if (!$this->_hasStyles) $this->_hasStyles = true;
			$this->_css[$id] = array($css, $media);
		}
	}
	
	/**
	 * Registers CSS file
	 * @param string $url
	 * @param string $media
	 * @param string $level
	 * @return void
	 */
	public function registerCssFile($url, $media = 'all', $level = '')
	{
		if (!empty($url)) {
			if (!$this->_hasStyles) $this->_hasStyles = true;
			if (!isset($this->_cssFiles[$media])) $this->_cssFiles[$media] = array();
			if (!in_array($url, $this->_cssFiles[$media])) {
				if (!empty($level)) {
					if (isset($this->_cssFiles[$media][$level])) {
						CDebug::addMessage('warnings', 'css-file-exists', A::t('core', 'CSS file with the same level "{level}" already registered: "{file}"', array('{level}' => $level, '{file}' => $url)));
					}
					$this->_cssFiles[$media][$level] = $url;
				} else {
					$this->_cssFiles[$media][$this->_countCssFiles++] = $url;
				}
			}
		}
	}
	
	/**
	 * Registers CSS files
	 * @param string $urls
	 * @param string $path
	 * @return void
	 */
	public function registerCssFiles($urls, $path = '')
	{
		if (!empty($urls) && array($urls)) {
			foreach ($urls as $key => $url) {
				if (empty($url)) continue;
				$path = !empty($path) ? trim($path, '/') . '/' : '';
				$url = $path . (is_array($url) ? $key : $url);
				$media = (is_array($url) && !empty($url['media'])) ? ' media="' . $url['media'] . '"' : 'all';
				$this->registerCssFile($url, $media);
			}
		}
	}
	
	/**
	 * Registers a piece of javascript code
	 * @param string $id
	 * @param string $script
	 * @param integer $position
	 * @return void
	 */
	public function registerScript($id, $script, $position = self::POS_JQUERY_READY)
	{
		if (!empty($script)) {
			if (!$this->_hasScripts) $this->_hasScripts = true;
			$this->_scripts[$position][$id] = $script;
			if ($position === self::POS_JQUERY_READY || $position === self::POS_ON_LOAD) {
				$this->registerCoreScript('jquery');
			}
		}
	}
	
	/**
	 * Registers javascript file
	 * @param string $url
	 * @param integer $position self::POS_HEAD|self::POS_BODY_BEGIN|self::POS_BODY_END
	 * @param string $level
	 * @return void
	 */
	public function registerScriptFile($url, $position = self::POS_HEAD, $level = '')
	{
		if (!empty($url)) {
			if (!$this->_hasScripts) $this->_hasScripts = true;
			if (!isset($this->_scriptFiles[$position])) $this->_scriptFiles[$position] = array();
			if (!in_array($url, $this->_scriptFiles[$position])) {
				if (!empty($level)) {
					if (isset($this->_scriptFiles[$position][$level])) {
						CDebug::addMessage('warnings', 'js-file-exists', A::t('core', 'Javascript file with the same level "{level}" already registered: "{file}"', array('{level}' => $level, '{file}' => $url)));
					}
					$this->_scriptFiles[$position][$level] = $url;
				} else {
					$this->_scriptFiles[$position][$this->_countJsFiles++] = $url;
				}
			}
		}
	}
	
	/**
	 * Registers a required javascript file
	 * @param string $url
	 * @param string $path
	 * @param integer $position self::POS_HEAD|self::POS_BODY_BEGIN|self::POS_BODY_END
	 * @return void
	 */
	public function registerScriptFiles($urls, $path = '', $position = self::POS_HEAD)
	{
		if (!empty($urls) && array($urls)) {
			foreach ($urls as $key => $url) {
				$path = !empty($path) ? trim($path, '/') . '/' : '';
				$url = $path . (is_array($url) ? $key : $url);
				$position = (is_array($url) && !empty($url['position'])) ? $url['position'] : $position;
				$this->registerScriptFile($url, $position);
			}
		}
	}
	
	/**
	 * Registers a core script package
	 * @param string $name
	 * @return void
	 */
	public function registerCoreScript($name)
	{
		// Registers core script
	}
	
	/**
	 * Renders the registered scripts in our class
	 * This method is called in View->render() class
	 * @param string &$output
	 * @return void|bool
	 */
	public function render(&$output)
	{
		if (!$this->_hasStyles && !$this->_hasScripts) {
			return false;
		}
		
		// Sort CSS according by level
		$this->_sortByLevel($this->_cssFiles);
		
		// Sort JS scripts by level
		if ($this->enableJavaScript) {
			$this->_sortByLevel($this->_scriptFiles);
		}
		
		$this->_cssToCompression();
		$this->_jsToCompression();
		$this->renderHead($output);
		if ($this->enableJavaScript) {
			$this->renderBodyBegin($output);
			$this->renderBodyEnd($output);
		}
	}
	
	/**
	 * Inserts the js scripts/css in the head section
	 * @param string &$output
	 * @return void
	 */
	public function renderHead(&$output)
	{
		$html = '';
		$version = '?v=' . CConfig::get('version');
		
		///CDebug::d($this->_cssFiles);
		///CDebug::d($this->_scriptFiles);
		
		foreach ($this->_cssFiles as $media => $cssFiles) {
			foreach ($cssFiles as $level => $cssFile) {
				$html .= CHtml::cssFile($cssFile . $version, $media);
			}
		}
		foreach ($this->_css as $css) {
			$html .= CHtml::css($css[0], $css[1]);
		}
		
		if ($this->enableJavaScript) {
			if (isset($this->_scriptFiles[self::POS_HEAD])) {
				foreach ($this->_scriptFiles[self::POS_HEAD] as $level => $scriptFile) {
					$html .= CHtml::scriptFile($scriptFile . $version);
				}
			}
			if (isset($this->_scripts[self::POS_HEAD])) {
				$html .= CHtml::script(implode("\n", $this->_scripts[self::POS_HEAD])) . "\n";
			}
		}
		
		if ($html !== '') {
			$count = 0;
			$output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<%%%head%%%>$1', $output, 1, $count);
			if ($count) {
				$output = str_replace('<%%%head%%%>', $html, $output);
			} else {
				$output = $html . $output;
			}
		}
	}
	
	/**
	 * Inserts the scripts at the beginning of the <body>
	 * @param string &$output
	 * @return void
	 */
	public function renderBodyBegin(&$output)
	{
		$html = '';
		if (isset($this->_scriptFiles[self::POS_BODY_BEGIN])) {
			foreach ($this->_scriptFiles[self::POS_BODY_BEGIN] as $level => $scriptFile) {
				$html .= CHtml::scriptFile($scriptFile);
			}
		}
		if (isset($this->_scripts[self::POS_BODY_BEGIN])) {
			$html .= CHtml::script(implode("\n", $this->_scripts[self::POS_BODY_BEGIN])) . "\n";
		}
		
		if ($html !== '') {
			$count = 0;
			$output = preg_replace('/(<body\b[^>]*>)/is', '$1<%%%begin%%%>', $output, 1, $count);
			if ($count) {
				$output = str_replace('<%%%begin%%%>', $html, $output);
			} else {
				$output = $html . $output;
			}
		}
	}
	
	/**
	 * Inserts the scripts at the end of the <body>
	 * @param string &$output
	 * @return void
	 */
	public function renderBodyEnd(&$output)
	{
		if (!isset($this->_scriptFiles[self::POS_BODY_END]) &&
			!isset($this->_scripts[self::POS_BODY_END]) &&
			!isset($this->_scripts[self::POS_JQUERY_READY]) &&
			!isset($this->_scripts[self::POS_DOC_READY]) &&
			!isset($this->_scripts[self::POS_ON_LOAD]))
			return;
		
		$completePage = 0;
		$output = preg_replace('/(<\\/body\s*>)/is', '<%%%end%%%>$1', $output, 1, $completePage);
		$html = '';
		if (isset($this->_scriptFiles[self::POS_BODY_END])) {
			foreach ($this->_scriptFiles[self::POS_BODY_END] as $level => $scriptFile) {
				$html .= CHtml::scriptFile($scriptFile);
			}
		}
		
		$scripts = isset($this->_scripts[self::POS_BODY_END]) ? $this->_scripts[self::POS_BODY_END] : array();
		if (isset($this->_scripts[self::POS_JQUERY_READY])) {
			if ($completePage) {
				$scripts[] = "jQuery(function($){\n" . implode("\n", $this->_scripts[self::POS_JQUERY_READY]) . "\n});";
			} else {
				$scripts[] = implode("\n", $this->_scripts[self::POS_JQUERY_READY]);
			}
		}
		if (isset($this->_scripts[self::POS_DOC_READY])) {
			if ($completePage) {
				$scripts[] = "jQuery(document).ready(function(){\n" . implode("\n", $this->_scripts[self::POS_DOC_READY]) . "\n});";
			} else {
				$scripts[] = implode("\n", $this->_scripts[self::POS_DOC_READY]);
			}
		}
		if (isset($this->_scripts[self::POS_ON_LOAD])) {
			if ($completePage) {
				$scripts[] = "jQuery(window).load(function(){\n" . implode("\n", $this->_scripts[self::POS_ON_LOAD]) . "\n});";
			} else {
				$scripts[] = implode("\n", $this->_scripts[self::POS_ON_LOAD]);
			}
		}
		if (!empty($scripts)) $html .= CHtml::script(implode("\n", $scripts)) . "\n";
		
		if ($completePage) {
			$output = str_replace('<%%%end%%%>', $html, $output);
		} else {
			$output = $output . $html;
		}
	}
	
	/**
	 * Returns count of compressed files
	 * @param string $type
	 * @returm int
	 */
	public function countCompressedFiles($type = 'css')
	{
		if ($type == 'css') {
			return $this->_countCompressedCssFiles;
		} elseif ($type == 'js') {
			return $this->_countCompressedJsFiles;
		}
		
		return 0;
	}
	
	/**
	 * Prepare css files to compression
	 * @returm bool
	 */
	protected function _cssToCompression()
	{
		if (!CConfig::get('compression.css.enable')) {
			return false;
		}
		
		if (is_array($this->_cssFiles)) {
			$tempCssFiles = array();
			foreach ($this->_cssFiles as $media => $styleFiles) {
				
				$this->_countCompressedCssFiles = count($styleFiles);
				$cssFilesHash = md5(serialize($styleFiles));
				$minifiedDir = CConfig::get('compression.css.path');
				$minifyCss = CConfig::get('compression.css.minify.' . (A::app()->view->getTemplate() === 'backend' ? 'backend' : 'frontend'));
				$baseUrl = A::app()->getRequest()->getBaseUrl();
				$controller = A::app()->view->getController();
				$action = A::app()->view->getAction();
				$minifiedFile = md5($baseUrl . $controller . '/' . $action . '/' . $this->_countCompressedCssFiles . '/' . $cssFilesHash) . '.css';
				$cssMinifiedFile = $minifiedDir . $minifiedFile;
				
				// Fix - skip assets directory
				if (in_array(strtolower($controller), array('assets'))) {
					continue;
				}
				
				// Check if minified file exists and exit
				if (!empty($minifiedFile) && file_exists($minifiedDir . $minifiedFile) && (filesize($minifiedDir . $minifiedFile) > 0)) {
					$tempCssFiles[$media][] = $cssMinifiedFile;
					continue;
				}
				
				// Collect CSS files content
				$count = 0;
				$cssContent = '';
				$baseUrl = str_ireplace(array('http:', 'https:'), '', $baseUrl);
				foreach ($styleFiles as $key => $cssFile) {
					$fileContent = CFile::getFileContent($cssFile);
					if ($fileContent) {
						if ($minifyCss) {
							$fileContent = CMinify::css($fileContent);
						}
						
						// Replace relative paths with absolute
						$dirName = dirname($cssFile);
						
						// ../../ : ../../templates/default/css => //domain.com/templates/default/css
						$dirNameLevel2 = explode('/', $dirName);
						array_pop($dirNameLevel2);
						array_pop($dirNameLevel2);
						$dirNameLevel2 = $baseUrl . implode('/', $dirNameLevel2) . '/';
						$fileContent = str_ireplace(array("url('../../", "url(../../"), array("url('" . $dirNameLevel2, "url(" . $dirNameLevel2), $fileContent);
						
						// ../ : ../templates/default/css => //domain.com/templates/default/css
						$dirNameLevel1 = explode('/', $dirName);
						array_pop($dirNameLevel1);
						$dirNameLevel1 = $baseUrl . implode('/', $dirNameLevel1) . '/';
						$fileContent = str_ireplace(array("url('../", "url(../"), array("url('" . $dirNameLevel1, "url(" . $dirNameLevel1), $fileContent);
						
						// url('') : url('fonts/font.eot') => url('//domain.com/fonts/font.eot')
						$dirNameLevel0 = explode('/', $dirName);
						$dirNameLevel0 = $baseUrl . implode('/', $dirNameLevel0) . '/';
						$fileContent = str_ireplace(array("url('font", "url(font"), array("url('" . $dirNameLevel0 . "font", "url(" . $dirNameLevel0 . "font"), $fileContent);
						
						$cssContent .= "/* CSS File " . ++$count . ": " . $cssFile . " */" . PHP_EOL . $fileContent . PHP_EOL . PHP_EOL;
					}
				}
				
				if (!empty($cssContent)) {
					// Remove oldest file if the limit of minified is reached
					if (CFile::getDirectoryFilesCount($minifiedDir, '.css') >= self::CSS_MINIFY_LIMIT) {
						CFile::removeDirectoryOldestFile($minifiedDir, 0, array('index.html'));
					}
					
					CFile::writeToFile($minifiedDir . $minifiedFile, $cssContent);
					$tempCssFiles[$media][] = $cssMinifiedFile;
				}
			}
			$this->_cssFiles = $tempCssFiles;
		}
		
		return true;
	}
	
	/**
	 * Prepare js files to compression
	 * @returm bool
	 */
	protected function _jsToCompression()
	{
		if (!CConfig::get('compression.js.enable')) {
			return false;
		}
		
		if (is_array($this->_scriptFiles)) {
			$tempJsFiles = array();
			foreach ($this->_scriptFiles as $position => $scriptFiles) {
				
				$countCompressedJsFiles = count($scriptFiles);
				$this->_countCompressedJsFiles += $countCompressedJsFiles;
				$jsFilesHash = md5(serialize($scriptFiles));
				$minifiedDir = CConfig::get('compression.js.path');
				$minifyJs = CConfig::get('compression.js.minify.' . (A::app()->view->getTemplate() === 'backend' ? 'backend' : 'frontend'));
				$baseUrl = A::app()->getRequest()->getBaseUrl();
				$controller = A::app()->view->getController();
				$action = A::app()->view->getAction();
				
				$minifiedFile = md5($baseUrl . $controller . '/' . $action . '/' . $position . '/' . $countCompressedJsFiles . '/' . $jsFilesHash) . '.js';
				$jsMinifiedFile = $minifiedDir . $minifiedFile;
				
				// Fix - skip assets directory
				if (in_array(strtolower($controller), array('assets'))) {
					continue;
				}
				
				// Check if minified file exists and exit
				if (!empty($minifiedFile) && file_exists($minifiedDir . $minifiedFile) && (filesize($minifiedDir . $minifiedFile) > 0)) {
					$tempJsFiles[$position][] = $jsMinifiedFile;
					continue;
				}
				
				// Collect JS files content
				$count = 0;
				$jsContent = '';
				$baseUrl = str_ireplace(array('http:', 'https:'), '', $baseUrl);
				foreach ($scriptFiles as $key => $url) {
					$fileContent = CFile::getFileContent($url);
					if ($fileContent) {
						if ($minifyJs) {
							$fileContent = CMinify::js($fileContent);
						}
						$jsContent .= "/* JS File " . ++$count . ": " . $url . " */" . PHP_EOL . $fileContent . PHP_EOL . PHP_EOL;
					}
				}
				
				if (!empty($jsContent)) {
					// Remove oldest file if the limit of minified is reached
					if (CFile::getDirectoryFilesCount($minifiedDir, '.js') >= self::JS_MINIFY_LIMIT) {
						CFile::removeDirectoryOldestFile($minifiedDir, 0, array('index.html'));
					}
					
					CFile::writeToFile($minifiedDir . $minifiedFile, $jsContent);
					$tempJsFiles[$position][] = $jsMinifiedFile;
				}
			}
			$this->_scriptFiles = $tempJsFiles;
		}
		
		return true;
	}
	
	/**
	 * Returns CSS or JS array of files, sorted by level
	 * @param array &$filesArray
	 * @returm voies
	 */
	private function _sortByLevel(&$filesArray = array())
	{
		foreach ($filesArray as $key => $files) {
			ksort($files);
			$filesArray[$key] = $files;
		}
	}
	
}