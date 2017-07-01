<?php
/**
 * CView base class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ---------------         	---------------            	---------------
 * __construct                                          
 * __set
 * __get
 * setMetaTags
 * getMetaTags
 * render
 * renderContent
 * renderView
 * setTemplate
 * getTemplate
 * setLayout
 * getLayout
 * setController
 * getController
 * setAction
 * getAction
 * getContent
 * getLayoutContent
 * isDefaultPage
 * 
 */	  

class CView
{
	/**	@var string */
	private $_template;
	/**	@var string */
	private $_layout;
	/** @var string */
	private $_controller;
	/** @var string */
	private $_action;
	/**	@var string */
	private $_pageTitle;
	/**	@var string */
	private $_pageKeywords;
	/**	@var string */
	private $_pageDescription;
	/** @var array */
	private $_vars = array();
	/** @var bool */
	private $_isRendered = false;
	/** @var bool */
	private $_isCompRendered = false;
	/** @var boolean to enable html output compression */
	private $_htmlCompression = false;
    /** @var int */
    private static $_count = 0;

	/** @var mixed */
	private $__templateFile = '';
	/** @var mixed */
	private $__templateContent = '';
	/** @var bool */
	private $__isTemplateFound = true;
	/** @var mixed */
	private $__layoutFile = '';	
	/** @var bool */
	private $__isLayoutFound = false;
	/** @var mixed */
	private $__layoutContent = '';
	/**	@var mixed */
	private $__viewContent;
	/**	@var mixed */
	private $__renderContent;

	/** @var string */
	private $__controller = '';
	/** @var string */
	private $__action = '';
	/** @var string */
	private $__viewSubFolder = '';	
	/** @var string */
	private $__viewPath	= '';
	/** @var string */
	private $__viewFile = '';
	    
  
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_htmlCompression = (CConfig::get('compression.html.enable') === true) ? true : false;
		$this->_template = CConfig::get('template.default');
		$this->_layout = CConfig::get('layouts.default', 'default');
		$this->__viewContent = '';
		$this->__renderContent = ''; 
    }

	/**
	 *	Setter method
	 *	@param string $index
	 *	@param mixed $value
	 */
	public function __set($index, $value)
 	{
		$this->_vars[$index] = $value;
 	}

	/**
	 *	Getter method
	 *	@param string $index
	 */
	public function __get($index)
 	{
		return isset($this->_vars[$index]) ? $this->_vars[$index] : '';
 	}

	/**
	 *	Sets meta tags for page
	 *	@param string $tag
	 *	@param mixed $value
	 */
	public function setMetaTags($tag, $val)
	{
		if($tag === 'title'){
			$this->_pageTitle = $val;	
		}elseif($tag === 'keywords'){
			$this->_pageKeywords = $val;	
		}elseif($tag === 'description'){
			$this->_pageDescription = $val;	
		}		
	}
 
	/**
	 *	Gets meta tags for page
	 *	@param string $tag
	 *	@param mixed $value
	 */
	public function getMetaTags($tag = '')
	{
        $tagValue = '';
		if($tag === 'title'){
			$tagValue = $this->_pageTitle;	
		}elseif($tag === 'keywords'){
			$tagValue = $this->_pageKeywords;	
		}elseif($tag === 'description'){
			$tagValue = $this->_pageDescription;	
		}
        return $tagValue;
    }
   
 	/**
 	 * Renders a view with/without template for comtrollers
 	 * @param string $params (controller/view or hidden controller/view)
 	 * @param bool $isPartial
 	 * @param bool $return
 	 * @throws Exception
 	 * @return void
 	 */
	public function render($params, $isPartial = false, $return = false)
	{
        if($this->_isRendered){
			CDebug::addMessage('warnings', 'render-double-call', A::t('core', 'Double call of function {function} with parameters: {params}', array('{function}'=>'render()', '{params}'=>$params)));
			return '';
		}
		
		// Check if this method is called by CController classes only
        $trace = debug_backtrace();
        if(isset($trace[1])){
            $calledByClass = get_parent_class($trace[1]['class']);
            if($calledByClass != '' && $calledByClass != 'CController'){
				CDebug::addMessage('errors', 'render-caller', A::t('core', 'The View::render() method cannot be called by {class} class. Controller classes allowed only.', array('{class}'=>$calledByClass)));
                return false;                
            }
        }
        
		try{
			$this->__isTemplateFound = true;
			
			// Set default controller and action
			$this->__controller = $this->_controller;
			$this->__action = $this->_action;
			
			$this->__viewPath = $this->__controller.DS.$this->__action;

			// Set controller and action according to passed params
			if(!empty($params)){
				$paramsParts = explode('/', $params);
				$parts = count($paramsParts);
				if($parts == 1){
					$this->__action = isset($paramsParts[0]) ? $paramsParts[0] : $this->action;
				}elseif($parts == 2){
					$this->__controller = isset($paramsParts[0]) ? $paramsParts[0] : $this->_controller;
					$this->__action = isset($paramsParts[1]) ? $paramsParts[1] : $this->action;					
				}elseif($parts >= 2){
					$this->__controller = isset($paramsParts[0]) ? $paramsParts[0] : $this->_controller;
					$this->__viewSubFolder = isset($paramsParts[1]) ? $paramsParts[1].DS : '';
					$this->__action = isset($paramsParts[2]) ? $paramsParts[2] : $this->action;
				}
				
				$this->__viewPath = $this->__controller.DS.$this->__viewSubFolder.$this->__action;
			}

			if(APPHP_MODE == 'test'){
				return $this->__viewPath;
			}else{
                // Force using lower-case names for view files
                $this->__controller = strtolower($this->__controller);
				
				// Get template file
				$this->__templateFile = APPHP_PATH.DS.'templates'.DS.(!empty($this->_template) ? $this->_template.DS : '').'default.php';
				if(!empty($this->_template)){
					if(!file_exists($this->__templateFile)){
						$this->__isTemplateFound = false;
						CDebug::addMessage('errors', 'render-template', A::t('core', 'Template file: "templates/{template}" cannot be found.', array('{template}'=>$this->_template)));
					}
				}else{
					$this->__isTemplateFound = false;
					CDebug::addMessage('errors', 'render-template', A::t('core', 'Template file: "templates/{template}" cannot be found.', array('{template}'=>'default.php')));
				}				
				
				// Get layout file
				if(CConfig::get('layouts.enable')){
					$this->__layoutFile = APPHP_PATH.DS.'templates'.DS.$this->_template.DS.'layouts'.DS.(!empty($this->_layout) ? $this->_layout : '').'.php';
					if(!empty($this->_layout)){
						if(file_exists($this->__layoutFile)){
							$this->__isLayoutFound = true;
						}else{
							CDebug::addMessage('errors', 'render-layout', A::t('core', 'Layout file: "{layout}" cannot be found.', array('{layout}'=>'templates/'.$this->_template.'/layouts/'.$this->_layout.'.php')));
						}
					}
				}
				
				// Get view file
				$this->__viewFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.strtolower($this->__viewPath).'.php';
				if(is_file($this->__viewFile)){
                    // Check application view
                }else{
                    // Check modules view
					$this->__viewFile = APPHP_PATH.DS.'protected'.DS.strtolower(A::app()->mapAppModule($this->__controller)).'views'.DS.strtolower($this->__viewPath).'.php';
                }
				
				// [19.09.2016] under check - doesn't work with uppercase
				// Force using lower-case names for view files
				// $this->__viewFile = strtolower($this->__viewFile);

                // Prepare content variables for view file
                foreach($this->_vars as $key => $value){
                    $$key = $value;
                }
				
				// Get view file content
				// We need to get it before layout content - to be sure it already exists when we show it in our layout
				if(file_exists($this->__viewFile)){
					ob_start();
					include $this->__viewFile;
					$this->__viewContent = ob_get_contents();
					ob_end_clean();
					
					CDebug::addMessage('general', 'included', $this->__viewFile);
					CDebug::addMessage('params', 'view'.(++self::$_count > 1 ? self::$_count : ''), $this->__viewFile);
				}else{
					if(preg_match('/[A-Z]/', $this->_controller)){
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$this->__viewFile)));
					}else{
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$this->__viewFile)));						
					}
				}
				
				// Get layout file content
                if($this->__isLayoutFound){
                    ob_start();
                    include $this->__layoutFile;
                    $this->__layoutContent = ob_get_contents();
                    ob_end_clean();
					
					CDebug::addMessage('general', 'included', $this->__layoutFile);
                }
				
				$output = '';
				if($isPartial){
					if($this->__isLayoutFound){
						$output = $this->__layoutContent;
					}else{
						$output = $this->__viewContent;
					}
				}else{
					// Prepare and include template file
					if($this->__isTemplateFound){
						ob_start();		
						include $this->__templateFile;	
						$this->__templateContent = ob_get_contents();
						ob_end_clean();
						
						CDebug::addMessage('general', 'included', $this->__templateFile);
	
						// Render registered scripts					
						A::app()->getClientScript()->render($this->__templateContent);
					
						$output = $this->__templateContent;
					}else{
						$output = $this->__viewContent;
					}								
				}
				
				// Output content
				if($this->_htmlCompression){
					if(APPHP_MODE == 'debug') {
						$beforeCompression = strlen($output);	
					}
					$output	= CMinify::html($output);
					if(APPHP_MODE == 'debug') {
						$afterCompression = strlen($output);
						CDebug::addMessage('data', 'html-compression-rate', (!empty($beforeCompression) ? 100 - round($afterCompression / $beforeCompression * 100, 1) : '0').'%' );
					}
				}
				
				if($return){					
					return $output;
				}else{
					echo $output;
				}				
				
				///CDebug::addMessage('params', 'view', $this->__viewFile);
				CDebug::addMessage('params', 'layout', $this->_layout ? $this->_layout : A::t('core', 'Unknown'));
				CDebug::addMessage('params', 'template', $this->_template ? $this->_template : A::t('core', 'Unknown'));
			}			
		}catch(Exception $e){
			CDebug::addMessage('errors', 'render', $e->getMessage());
		}
		
		$this->_isRendered = true;
	} 	
 
	/**
 	 * Renders a view for components
 	 * @param string $view
 	 * @return void
 	 */
	public function renderContent($view)
	{
        if($this->_isCompRendered){
			CDebug::addMessage('warnings', 'render-double-call', A::t('core', 'Double call of function {function} with parameters: {params}', array('{function}'=>'renderContent()', '{params}'=>'')));
			return '';
		}

        // Check if this method is called by CController classes only
        $trace = debug_backtrace();
        if(isset($trace[1])){
            $calledByClass = get_parent_class($trace[1]['class']);
            if($calledByClass != '' && $calledByClass != 'CComponent'){
				CDebug::addMessage('errors', 'render-content-caller', A::t('core', 'The View::renderContent() method cannot be called by {class} class. Component classes allowed only.', array('{class}'=>$calledByClass)));
                return false;                
            }
        }

        $content = '';
		// [19.09.2016] under check - doesn't work with uppercase
		//$this->__viewFile = strtolower(APPHP_PATH.DS.'protected'.DS.'views'.DS.'components'.DS.$view.'.php');
		$this->__viewFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.'components'.DS.strtolower($view).'.php';

        if(is_file($this->__viewFile) && file_exists($this->__viewFile)){
            // Prepare content variables for view file
            foreach($this->_vars as $key => $value){
                $$key = $value;
            }		

			// Get view file content
            if(file_exists($this->__viewFile)){
                ob_start();
                include $this->__viewFile;
                $this->__renderContent = ob_get_contents();
                ob_end_clean();
				
				CDebug::addMessage('general', 'included', $this->__viewFile);
            }else{
                if(preg_match('/[A-Z]/', $this->_controller)){
                    CDebug::addMessage('errors', 'render-content', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$this->__viewFile)));
                }else{
                    CDebug::addMessage('errors', 'render-content', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$this->__viewFile)));
                }
            }

			// Output content
			$renderContent = $this->__renderContent;
			if(APPHP_MODE == 'debug') {
				$beforeCompression = strlen($renderContent);	
			}			
			$renderContent = ($this->_htmlCompression) ? CMinify::html($renderContent) : $renderContent;
			if(APPHP_MODE == 'debug') {
				$afterCompression = strlen($renderContent);
				CDebug::addMessage('data', 'html-compression-rate', (!empty($beforeCompression) ? 100 - round($afterCompression / $beforeCompression * 100, 1) : '0').'%' );
			}
			
			echo $renderContent;
			
			CDebug::addMessage('params', 'render-content', $this->__viewFile);
        }else{
            CDebug::addMessage('errors', 'render-content', A::t('core', 'The system is unable to find the requested component view file: {file}', array('{file}'=>$this->__viewFile)));
        }
        
		$this->_isCompRendered = true;
        echo $content;
    }

 	/**
 	 * Renders a view from another view
 	 * @param string $params (controller/view)
 	 * @param array $data
 	 * @throws Exception
 	 * @return void
 	 */
	public function renderView($params, $data = array())
	{		
		try{
			// Set default controller and action
			$controller = $this->_controller;
			$view = CConfig::get('defaultAction');

			// Set controller and action according to passed params
			$paramsParts = explode('/', $params);			
			if(!empty($params)){
				$parts = count($paramsParts);
				if($parts == 1){
					if(isset($paramsParts[0])) $view = $paramsParts[0];
				}elseif($parts >= 2){
					if(isset($paramsParts[0])) $controller = $paramsParts[0];
					if(isset($paramsParts[1])) $view = $paramsParts[1];
				}
			}

			$controller = strtolower($controller);
            // CORRECTION [20.09.2016]
			$view = strtolower($view);

			$viewFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.$controller.DS.$view.'.php';
			if(is_file($viewFile)){
				// Check application view
			}else{
				// Check modules view
				$viewFile = APPHP_PATH.DS.'protected'.DS.A::app()->mapAppModule($controller).'views'.DS.$controller.DS.$view.'.php';
			}
			// Force using lower-case names for view files
			//$viewFile = strtolower($viewFile);

			// Prepare content variables for view file
			if(is_array($data)){
				foreach($data as $key => $value){
					$$key = $value;
				}
			}
			
			// Get view file content
			if(file_exists($viewFile)){
				ob_start();
				include $viewFile;
				$this->__viewContent = ob_get_contents();
				ob_end_clean();
				
				CDebug::addMessage('general', 'included', $viewFile);
			}else{
				if(preg_match('/[A-Z]/', $this->_controller)){
					CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$viewFile)));
				}else{
					CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$viewFile)));						
				}
			}				

			// Output view content
			echo ($this->_htmlCompression) ? CMinify::html($this->__viewContent) : $this->__viewContent;
			
			CDebug::addMessage('params', 'render-view', $viewFile);

		}catch(Exception $e){
			CDebug::addMessage('errors', 'render-view', $e->getMessage());
		}
	} 	

	/**	 
	 * Template setter
	 * @param string $template
	 */
	public function setTemplate($template = '')
	{
		$this->_template = !empty($template) ? $template : '';
	}

  	/**	 
	 * Template getter
	 * @return string $template
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**	 
	 * Layout setter
	 * @param string $layout
	 */
	public function setLayout($layout = '')
	{
		$this->_layout = !empty($layout) ? $layout : '';
	}

  	/**	 
	 * Layout getter
	 * @return string $layout
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

  	/**	 
	 * Layout content getter
	 * @return string $__layoutContent
	 */
	public function getLayoutContent()
	{
		return $this->__layoutContent;
	}

	/**	 
	 * Controller setter
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->_controller = $controller;
	}

	/**	 
	 * Controller getter
	 */
	public function getController()
	{
		return $this->_controller;
	}

	/**	 
	 * Action setter
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->_action = $action;
	}

	/**	 
	 * Action getter
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**	 
	 * Action getter
	 * @return mixed content (HTML code)
	 */
	public function getContent()
	{
		return $this->__viewContent;
	}    

	/**	 
	 * Action checks if page is default
	 * @return bool
	 */
	public function isDefaultPage()
	{
		return (strtolower($this->_controller) == strtolower(CConfig::get('defaultController')) &&
				strtolower($this->_action) == strtolower(CConfig::get('defaultAction'))) ?
			true :
			false;
	}

}