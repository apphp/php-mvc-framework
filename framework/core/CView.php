<?php
/**
 * CView base class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2015 ApPHP Framework
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
 * setController
 * getController
 * setAction
 * getAction
 * getContent
 * isDefaultPage
 * 
 */	  

class CView
{
	/**	@var string */
	private $_template;
	/**	@var mixed */
	private $_content;
	/**	@var mixed */
	private $_renderContent;
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


	/** @var mixed */
	private $__template = '';
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
	/** @var bool */
	private $__isTemplateFound = true;
	/** @var mixed */
	private $__templateContent = '';
    
  
	/**
	 * Class constructor
	 */
	public function __construct()
	{
        $this->_content = '';
		$this->_renderContent = ''; 
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
		}else if($tag === 'keywords'){
			$this->_pageKeywords = $val;	
		}else if($tag === 'description'){
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
		}else if($tag === 'keywords'){
			$tagValue = $this->_pageKeywords;	
		}else if($tag === 'description'){
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
				}else if($parts == 2){
					$this->__controller = isset($paramsParts[0]) ? $paramsParts[0] : $this->_controller;
					$this->__action = isset($paramsParts[1]) ? $paramsParts[1] : $this->action;					
				}else if($parts >= 2){
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

				$this->__template = APPHP_PATH.DS.'templates'.DS.(!empty($this->_template) ? $this->_template.DS : '').'default.php';			
				if(!file_exists($this->__template)){
					$this->__isTemplateFound = false;
					if(!empty($this->_template)){
						CDebug::addMessage('errors', 'render-template', A::t('core', 'Template file: "templates/{template}" cannot be found.', array('{template}'=>$this->_template)));
					}
				}
                $this->__viewFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.$this->__viewPath.'.php';
				if(is_file($this->__viewFile)){
                    // Check application view
                }else{
                    // Check modules view
                    $this->__viewFile = APPHP_PATH.DS.'protected'.DS.A::app()->mapAppModule($this->__controller).'views'.DS.$this->__viewPath.'.php';
                    // [#001 - 28.05.2013] under check - removed as un-needed
					// if(is_file($moduleView)){
                    // 		$this->__viewFile = $moduleView;
                    // }
                }
				
				// Force using lower-case names for view files
				$this->__viewFile = strtolower($this->__viewFile);

                // Prepare content from view file
                foreach($this->_vars as $key => $value){
                    $$key = $value;
                }
                if(file_exists($this->__viewFile)){
                    ob_start();
                    include $this->__viewFile;
                    $this->_content = ob_get_contents();
                    ob_end_clean();
					
					CDebug::addMessage('general', 'included', $this->__viewFile);
                }else{
					if(preg_match('/[A-Z]/', $this->_controller)){
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$this->__viewFile)));
					}else{
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$this->__viewFile)));						
					}
                }
				
				if($isPartial){
					$output = $this->_content;
				}else{
					// Prepare and include template file
					if($this->__isTemplateFound){
						ob_start();		
						include $this->__template;	
						$this->__templateContent = ob_get_contents();
						ob_end_clean();
						
						CDebug::addMessage('general', 'included', $this->__template);
	
						// Render registered scripts					
						A::app()->getClientScript()->render($this->__templateContent);
					
						$output = $this->__templateContent;					
					}else{
						$output = $this->_content;
					}								
				}
				
				if($return){					
					return $output;
				}else{
					echo $output;					
				}				
				
				CDebug::addMessage('params', 'view', $this->__viewFile);
				CDebug::addMessage('params', 'template', $this->_template);				
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
		// [28.02.2014] DEPRECATED
        //$this->__viewFile = strtolower(APPHP_PATH.DS.'protected'.DS.'components'.DS.'views'.DS.$view.'.php');
		$this->__viewFile = strtolower(APPHP_PATH.DS.'protected'.DS.'views'.DS.'components'.DS.$view.'.php');

        if(is_file($this->__viewFile) && file_exists($this->__viewFile)){
            // Prepare content from view file
            foreach($this->_vars as $key => $value){
                $$key = $value;
            }		

            if(file_exists($this->__viewFile)){
                ob_start();
                include $this->__viewFile;
                $this->_renderContent = ob_get_contents();
                ob_end_clean();
				
				CDebug::addMessage('general', 'included', $this->__viewFile);
            }else{
                if(preg_match('/[A-Z]/', $this->_controller)){
                    CDebug::addMessage('errors', 'render-content', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$this->__viewFile)));
                }else{
                    CDebug::addMessage('errors', 'render-content', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$this->__viewFile)));
                }
            }

            echo $this->_renderContent;
			
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
				}else if($parts >= 2){
					if(isset($paramsParts[0])) $controller = $paramsParts[0];
					if(isset($paramsParts[1])) $view = $paramsParts[1];
				}
			}

			$controller = strtolower($controller);

			$viewFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.$controller.DS.$view.'.php';
			if(is_file($viewFile)){
				// Check application view
			}else{
				// Check modules view
				$viewFile = APPHP_PATH.DS.'protected'.DS.A::app()->mapAppModule($controller).'views'.DS.$controller.DS.$view.'.php';
			}
			// Force using lower-case names for view files
			$viewFile = strtolower($viewFile);

			// Prepare content from view file
			if(is_array($data)){
				foreach($data as $key => $value){
					$$key = $value;
				}
			}
			if(file_exists($viewFile)){
				ob_start();
				include $viewFile;
				$this->_content = ob_get_contents();
				ob_end_clean();
				
				CDebug::addMessage('general', 'included', $viewFile);
			}else{
				if(preg_match('/[A-Z]/', $this->_controller)){
					CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$viewFile)));
				}else{
					CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$viewFile)));						
				}
			}				

			echo $this->_content;
			
			CDebug::addMessage('params', 'render-view', $viewFile);

		}catch(Exception $e){
			CDebug::addMessage('errors', 'render-view', $e->getMessage());
		}
	} 	

	/**	 
	 * Template setter
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->_template = !empty($template) ? $template : 'default';
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
		return $this->_content;
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