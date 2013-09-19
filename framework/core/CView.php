<?php
/**
 * CView base class file
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
 * __set
 * __get
 * setMetaTags
 * getMetaTags
 * renderContent
 * render
 * setTemplate
 * getTemplate
 * setController
 * setAction
 * getAction
 * getContent
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * 
 */	  

class CView
{
	/**	@var string */
	private $_template;
	/**	@var mixed */
	private $_content;
	/** @var string */
	private $_controller;
	/** @var string */
	private $_action;
	/**	@var string */
	private $_breadCrumbs;
	/**	@var string */
	private $_activeMenu;
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
	private $_isComRendered = false;

  
	/**
	 * Class constructor
	 */
	public function __construct()
	{
        $this->_content = '';
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
 	 * Render a view for component
 	 * @param string $view 
 	 * @return void
 	 */
	public function renderContent($view)
	{
        if($this->_isComRendered){
			CDebug::addMessage('warnings', 'render-double-call', A::t('core', 'Double call of function {function} with parameters: {params}', array('{function}'=>'renderContent()', '{params}'=>$params)));
			return '';
		}

        // check if this method is called by CController classes only
        $trace = debug_backtrace();
        if(isset($trace[1])){
            $calledByClass = get_parent_class($trace[1]['class']);
            if($calledByClass != '' && $calledByClass != 'CComponent'){
				CDebug::addMessage('errors', 'render-content-caller', A::t('core', 'The View::renderContent() method cannot be called by {class} class. Component classes allowed only.', array('{class}'=>$calledByClass)));
                return false;                
            }
        }

        $content = '';
        $viewFile = APPHP_PATH.DS.'protected'.DS.'components'.DS.'views'.DS.$view.'.php';

        if(is_file($viewFile) && file_exists($viewFile)){
            // prepare content from view file
            foreach($this->_vars as $key => $value){
                $$key = $value;
            }		

            ob_start();
            include $viewFile;
            $content = ob_get_contents();
            ob_end_clean();
        }else{
            CDebug::addMessage('errors', 'component-render-view', A::t('core', 'The system is unable to find the requested component view file: {file}', array('{file}'=>$viewFile)));
        }
        
		$this->_isComRendered = true;
        echo $content;
    }
    
 	/**
 	 * Render a view with/without template for comtrollers
 	 * @param string $params (controller/view or hidden controller/view)
 	 * @param bool $isPartial
 	 * @throws Exception
 	 * @return void
 	 */
	public function render($params, $isPartial = false)
	{
        if($this->_isRendered){
			CDebug::addMessage('warnings', 'render-double-call', A::t('core', 'Double call of function {function} with parameters: {params}', array('{function}'=>'render()', '{params}'=>$params)));
			return '';
		}
		
		// check if this method is called by CController classes only
        $trace = debug_backtrace();
        if(isset($trace[1])){
            $calledByClass = get_parent_class($trace[1]['class']);
            if($calledByClass != '' && $calledByClass != 'CController'){
				CDebug::addMessage('errors', 'render-caller', A::t('core', 'The View::render() method cannot be called by {class} class. Controller classes allowed only.', array('{class}'=>$calledByClass)));
                return false;                
            }
        }
        
		try{
			$isTemplateFound = true;
			
			$paramsParts = explode('/', $params);
			
			// set default controller and action
			$controller = $this->_controller;
			$view = $this->_action;

			// set controller and action according to passed params
			if(!empty($params)){
				$parts = count($paramsParts);
				if($parts == 1){
					/// [x] $controller = $this->_controller;
					$view = isset($paramsParts[0]) ? $paramsParts[0] : $this->_action;
				}else if($parts >= 2){
					$controller = isset($paramsParts[0]) ? $paramsParts[0] : $this->_controller;
					$view = isset($paramsParts[1]) ? $paramsParts[1] : $this->_action;
				}
			}

			if(APPHP_MODE == 'test'){
				return $controller.'/'.$view;
			}else{
				if(APPHP_MODE != 'debug') $controller = strtolower($this->_controller);

				$template = APPHP_PATH.DS.'templates'.DS.(!empty($this->_template) ? $this->_template.DS : '').'default.php';			
				if(!file_exists($template)){
					$isTemplateFound = false;
					if(!empty($this->_template)){
						CDebug::addMessage('errors', 'render-template', A::t('core', 'Template file: "templates/{template}" cannot be found.', array('{template}'=>$this->_template)));
					}
				}
                $appFile = APPHP_PATH.DS.'protected'.DS.'views'.DS.$controller.DS.$view.'.php';
                $viewFile = $appFile;
				if(is_file($viewFile)){
                    // check application view
                }else{
                    // check modules view
                    $viewFile = APPHP_PATH.DS.'protected'.DS.A::app()->mapAppModule($controller).'views'.DS.$controller.DS.$view.'.php';
                    // [#001 - 28.05.2013] under check - removed as un-needed
					// if(is_file($moduleView)){
                    // 		$viewFile = $moduleView;
                    // }
                }
				
                // prepare content from view file
                foreach($this->_vars as $key => $value){
                    $$key = $value;
                }
                if(file_exists($viewFile)){
                    ob_start();
                    include $viewFile;
                    $this->_content = ob_get_contents();
                    ob_end_clean();
                }else{
					if(preg_match('/[A-Z]/', $this->_controller)){
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}. Case sensitivity mismatch!', array('{file}'=>$viewFile)));
					}else{
						CDebug::addMessage('errors', 'render-view', A::t('core', 'The system is unable to find the requested view file: {file}', array('{file}'=>$viewFile)));						
					}
                }
				
				if($isPartial){
					echo $this->_content;
				}else{
					// prepare and include template file
					if($isTemplateFound){
						ob_start();		
						include $template;	
						$template_content = ob_get_contents();
						ob_end_clean();
	
						// render registered scripts					
						A::app()->getClientScript()->render($template_content);
					
						echo $template_content;					
					}else{
						echo $this->_content;
					}								
				}
				
				CDebug::addMessage('params', 'view', $viewFile);
				CDebug::addMessage('params', 'template', $this->_template);				
			}			
		}catch(Exception $e){
			CDebug::addMessage('errors', 'render', $e->getMessage());
		}
		
		$this->_isRendered = true;
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

}