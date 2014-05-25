<?php
/**
 * CMessageSource represents a message source that stores translated messages in PHP scripts
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 *                          _loadMessages
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init
 *
 */	  

class CMessageSource extends CComponent
{

	/** @var string */
	private $_basePath;
	/** @var array */    
    private $_messages = array();

	/**
	 * Class constructor
	 * @return void
	 */
	function __construct()
	{
		$this->_basePath = dirname(__FILE__);
	}

    /**
     *	Returns the instance of object
     *	@return CHttpRequest class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}
 
	/**
	 * Loads message translation for specified language and category
	 * @param string $category 
	 * @param string $language 
	 * @return array the loaded messages
	 */
	protected function _loadMessages($category, $language)
	{
        $messages = array();

		if($category == 'core'){
            $messageFile = $this->_basePath.DS.'..'.DS.'messages'.DS.$language.DS.$category.'.php';        
		}else if($category == 'i18n'){
            $messageFile = $this->_basePath.DS.'..'.DS.'i18n'.DS.$language.'.php';
        }else if($category == 'setup'){
            $messageFile = APPHP_PATH.DS.'protected'.DS.'modules'.DS.$category.DS.'messages'.DS.$language.DS.$category.'.php';        
        }else{
            $messageFile = APPHP_PATH.DS.'protected'.DS.'messages'.DS.$language.DS.$category.'.php';        
        }

		if(is_file($messageFile)){
            $messages = include($messageFile);
        }

        return $messages;
	}
    
	/**
	 * Translates a message to the specified language
	 * @param string $category 
	 * @param string $message 
	 * @param string $language 
	 * @return string the translated message 
	 */
	public function translate($category, $message, $language = null)
	{
		if($language === null){
			$language = A::app()->getLanguage();
		}
		
		$key = $language.'.'.$category;
		if(!isset($this->_messages[$key])){
			$this->_messages[$key] = $this->_loadMessages($category, $language);
		}

        if(isset($this->_messages[$key][$message]) && $this->_messages[$key][$message] !== ''){
			return $this->_messages[$key][$message];
        }else if($pos = strpos($message, '.') !== false){
            // check sub-arrays (upto 2 levels)
            $messageParts = explode('.', $message);
            $parts = count($messageParts);
            if($parts == 2){
                $arrMessages = isset($this->_messages[$key][$messageParts[0]]) ? $this->_messages[$key][$messageParts[0]] : '';
                if(is_array($arrMessages) && isset($arrMessages[$messageParts[1]])){
                    return $arrMessages[$messageParts[1]];
                }
            }else if($parts == 3){
                $arrSubMessages = isset($this->_messages[$key][$messageParts[0]][$messageParts[1]]) ? $this->_messages[$key][$messageParts[0]][$messageParts[1]] : '';
                if(is_array($arrSubMessages) && isset($arrSubMessages[$messageParts[2]])){
                    return $arrSubMessages[$messageParts[2]];
                }                
            }
            return $message;
		}else{
            return $message;
		}  
	}
    
}