<?php
/**
 * CLoader is a helper class that provides a set of helper methods for load/include files
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2018 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * library
 * file
 * config
 * 
 */	  

class CLoader
{
 
    /**
     * Loads library
     * @param mixed $libraryName		Library name to load
     * @param string $path				Relative path to protected/libraries/
     * @param array $params			
     * @return void
     */
    public static function library($libraryName = null, $path = '', $params = array())
    {
		if(!empty($libraryName)){			
			$exclusions = isset($params['exclusions']) ? (array)$params['exclusions'] : array();
			
			// Remove traling slashes
			if(!empty($path)){
				$path = trim($path, '/\\');
			}

			if($libraryName == '*'){
				$libraryFiles = CFile::findFiles('protected'.DS.'libraries'.DS.$path, array('returnType'=>'fullPath'));
				if(is_array($libraryFiles)){
					foreach($libraryFiles as $file){
						if(!in_array(basename($file), $exclusions)){
							include(APPHP_PATH.DS.$file);	
						}
					}
				}
			}else{
				// Force array
				$libraryNames = array($libraryName);

				foreach($libraryNames as $file){					
					// Look in application libraries directory
					$fullPath = 'protected'.DS.'libraries'.DS.$path.DS.$file;
					
					if(file_exists($fullPath)){
						include_once(APPHP_PATH.DS.$fullPath);
					}
				}				
			}
		}
	}

    /**
     * Loads file
     * @param mixed $fileName			File name to load
     * @param string $path
     * @param bool $return
     * @return void|mixed
     */
    public static function file($fileName = null, $path = '', $return = false)
    {
		if(!empty($fileName)){			
			
			// Remove traling slashes
			if(!empty($path)){
				$path = trim($path, '/\\');
			}
			
			// Force array
			$fileNames = array($fileName);
			
			foreach($fileNames as $file){
				// Look in application libraries directory
				$fullPath = $path.DS.$fileName;
				
				if(file_exists($fullPath)){
					if($return){
						// Turn on output buffering
						ob_start();
						include(APPHP_PATH.DS.$fullPath);
						$buffer = ob_get_contents();
						ob_end_clean();
						
                        return $buffer;
					}else{
						include_once(APPHP_PATH.DS.$fullPath);	
					}				
				}				
			}
		}
	}

    /**
     * Load config file
     * @param mixed $module
     * @param string $name          File name to load
     * @return false|mixed
     */
    public static function config($module = '', $name = '')
    {
        if(!empty($module) && !empty($name)){
            $configFile = APPHP_PATH.DS.'protected'.DS.'modules'.DS.$module.DS.'config'.DS.$name.'.php';
			
            if(file_exists($configFile)){
                return include($configFile);
            }
        }

        return false;
    }
}
