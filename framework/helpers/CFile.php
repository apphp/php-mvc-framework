<?php
/**
 * CFile is a helper class that provides a set of helper methods for common file system operations
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * isWritable											_findFilesRecursive
 * getExtension          	                            _validatePath
 * getMimeType        									_errorHanler
 * getMimeTypeByExtension								
 * deleteDirectory                                      
 * emptyDirectory                                       
 * copyDirectory
 * isDirectoryEmpty
 * getDirectoryFilesNumber
 * removeDirectoryOldestFile
 * findSubDirectories
 * writeToFile
 * copyFile
 * findFiles
 * deleteFile
 * getFileSize
 * createShortenName
 * 
 */	  

class CFile
{
    
	/**
	 * Tests for file writability
	 * Windows servers return true for is_writable() returns TRUE even if you really can't write to file.
	 * But on on Unix servers if safe_mode is "on" it's is also unreliable.
	 * @link https://bugs.php.net/bug.php?id=54709
	 * @param string
	 * @return bool
	 */
	public static function isWritable($file)
	{
		// Check if we're on a Unix server with safe_mode "off", in this case we call is_writable
		if (DIRECTORY_SEPARATOR === '/' && (substr(phpversion(), 0, 3) == '5.4' || !ini_get('safe_mode'))){
			return is_writable($file);
		}

		// For Windows servers and safe_mode "on" we'll actually write a file and then read it.
		if (is_dir($file)){
			$file = rtrim($file, '/').'/'.md5(mt_rand());
			if(($fp = @fopen($file, 'ab')) === false){
				return false;
			}
			fclose($fp);
			@chmod($file, 0777);
			@unlink($file);
			return true;
		}elseif(!is_file($file) || ($fp = @fopen($file, 'ab')) === false){
			return false;
		}
		
		fclose($fp);
		return true;
	}

	/**
	 * Returns the extension name of a given file path (ex.: "path/to/some/thing.php" will return "php")
	 * @param string $path 
	 * @return string 
	 */
	public static function getExtension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Determines the MIME type of the specified file
	 * @param string $file 
	 * @return string the MIME type
	 */
	public static function getMimeType($file, $checkExtension = true)
	{
		if(empty($file)){
			return null;	
		}
		
		if(function_exists('finfo_open')){
			$options = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
			$info = finfo_open($options);

			if($info && ($result=finfo_file($info, $file)) !== false){
				return $result;
			}
		}

		if(function_exists('mime_content_type') && ($result = mime_content_type($file)) !== false){
			return $result;
		}

		return $checkExtension ? self::getMimeTypeByExtension($file) : null;
	}

	/**
	 * Determines the MIME type based on the extension name of the specified file
	 * @param string $file
	 * @return string the MIME type
	 */
	public static function getMimeTypeByExtension($file)
	{
		// Predefined mime types
		$mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // Images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // Archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // Audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // Adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // MS Office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
			'csv' => 'text/csv',
            'ppt' => 'application/vnd.ms-powerpoint',

            // Open Office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
		);
			
		if(($ext = pathinfo($file, PATHINFO_EXTENSION)) !== ''){
			$ext = strtolower($ext);
			if(isset($mime_types[$ext])){
				return $mime_types[$ext];
			}
		}

		return '';
	}

	/**
	 * Deletes given directory with files it includes 
	 * @param string $dir
	 * @return bool
	 */
	public static function deleteDirectory($dir = '')
	{
		self::emptyDirectory($dir);
        return rmdir($dir);
	}

	/**
	 * Removes files and subdirectories of the given directory
	 * @param string $dir
	 * @return bool
	 */
	public static function emptyDirectory($dir = '')
	{
		foreach(glob($dir.'/*') as $file){
			if(is_dir($file)){
				self::emptyDirectory($file);
			}else{
				unlink($file);
			}
		}
		return true;
	}

	/**
	 * Copies content of source directory into destination directory
	 * Warning: if the destination file already exists, it will be overwritten
	 * @param string $src
	 * @param string $dest
	 * @param bool $fullPath
	 * @param array $options 
	 *      newDirMode - the permission to be set for newly copied directories (defaults to 0777)
	 *      newFileMode - the permission to be set for newly copied files (defaults to the current environment setting)
	 * @return bool
	 */
	public static function copyDirectory($src = '', $dest = '', $fullPath = true, $options = array())
	{
		$result = false;
		$dirPath = (($fullPath) ? APPHP_PATH.'/' : '').$src;
		
		if(is_dir($dirPath)){
			$dir = opendir($dirPath);
			if(!$dir) return $result;
			if(!file_exists(trim($dest, '/').'/')){
                mkdir((($fullPath) ? APPHP_PATH.'/' : '').$dest);
            }
            if(is_dir($dest)){
				@chmod($dest, (isset($options['newDirMode']) ? $options['newDirMode'] : 0777));
			}
			while(false !== ($file = readdir($dir))){	
				if(($file != '.') && ($file != '..')){				
					$fromDir = trim($src, '/').'/'.$file;
					$toDir = trim($dest, '/').'/'.$file;
					if(is_dir($fromDir)){
						$result = self::copyDirectory($fromDir, $toDir, $fullPath, $options);
					}else{
						$result = copy($fromDir, $toDir);	
						if(isset($options['newFileMode'])){
                            @chmod($toDir, $options['newFileMode']);    
                        }						
                    }				
				}
			}
			closedir($dir);
		}
		
		return $result;
	}
	
	/**
	 * Returns the result of check if given directory is empty
	 * @param string $dir
	 */
	public static function isDirectoryEmpty($dir = '')
	{
		if($dir == '' || !is_readable($dir)) return false; 
		$hd = opendir($dir);
		while(false !== ($entry = readdir($hd))){
			if($entry !== '.' && $entry !== '..'){
				return false;
			}
		}
		closedir($hd);
		return true;
	}	
	
	/**
	 * Returns the number of files in a given directory
	 * @param string $dir
	 */
	public static function getDirectoryFilesNumber($dir = '')
	{
        return count(glob($dir.'*'));
    }
    
	/**
	 * Deletes the oldest file in a given directory or file older than a given days before
	 * @param string $dir
	 * @param int $days
	 * @param array $exclude
	 * return void
	 */
	public static function removeDirectoryOldestFile($dir = '', $days = 0, $exclude = array())
	{
		if(!empty($days)){
			$oldestFileTime = @date('Y-m-d H:i:s', strtotime('-'.(int)$days.' days'));
		}else{
			$oldestFileTime = @date('Y-m-d H:i:s');	
		}        
        $oldestFileName = '';
        if($hdir = opendir($dir)){
            while(false !== ($obj = @readdir($hdir))){
                if($obj == '.' || $obj == '..' || $obj == '.htaccess' || in_array($obj, $exclude)){
					continue;
				}
                $fileTime = @date('Y-m-d H:i:s', @filectime($dir.$obj));
                if($fileTime < $oldestFileTime){
                    $oldestFileTime = $fileTime;
                    $oldestFileName = $obj;
                }				
            }
        }		
        if(!empty($oldestFileName)){
            self::deleteFile($dir.$oldestFileName);
        }
    }

	/**
	 * Returns the list of subdirectories in a given path 
	 * @param string $dir
	 * @param bool $fullPath
	 * @return array
	 */
	public static function findSubDirectories($dir = '.', $fullPath = false)
	{
		$subDirectories = array();
		$folder = dir($dir); 
		while($entry = $folder->read()){
			if($entry != '.' && $entry != '..' && is_dir($dir.$entry)){
			    $subDirectories[] = ($fullPath ? $dir : '').$entry; 
			}
		}     
		$folder->close(); 
		return $subDirectories;
	}
    
	/**
	 * Writes to the file
	 * @param string $file  
	 * @param mixed $content
	 * @param string $mode 
	 * @return bool
	 */
	public static function writeToFile($file = '', $content = '', $mode = 'w')
	{
        $fp = @fopen($file, $mode);                     
        @fwrite($fp, $content);
        @fclose($fp);
        self::_errorHanler('file-writing-error', A::t('core', 'An error occurred while writing to file {file}.', array('{file}'=>$file)));
        return true;
    }

	/**
	 * Copies a file
	 * @param string $src (absolute path APPHP_PATH.DS.$sourcePath)
	 * @param string $dest (absolute path APPHP_PATH.DS.$targetPath)
	 * @return bool
	 */
	public static function copyFile($src = '', $dest = '')
	{
        $result = @copy($src, $dest);
        self::_errorHanler('file-coping-error', A::t('core', 'An error occurred while copying the file {source} to {destination}.', array('{source}'=>$src, '{destination}'=>$dest)));
        return $result;
	}

	/**
	 * Returns the files found under the given directory and subdirectories
	 * @param string $dir 
	 * @param array $options
	 * Usage:
	 * findFiles(
	 *    $dir,
	 *    array(
	 *       'fileTypes'=>array('php', 'zip'),
	 *   	 'exclude'=>array('html', 'htaccess', 'path/to/'),
	 '*   	 'level'=>-1
	 *       'returnType'=>'fileOnly'
	 *  ))
	 * Description:
	 * fileTypes: array, list of file name suffix (without dot). 
	 * exclude: array, list of directory and file exclusions. Each exclusion can be either a name or a path.
	 * level: integer, recursion depth, (-1 - unlimited depth, 0 - current directory only, N - recursion depth)
	 * returnType : 'fileOnly' or 'fullPath'
	 * @return array of files
	 */
	public static function findFiles($dir, $options = array())
	{
		$fileTypes = isset($options['fileTypes']) ? $options['fileTypes'] : array();
		$exclude = isset($options['exclude']) ? $options['exclude'] : array();
		$level = isset($options['level']) ? $options['level'] : -1;
		$returnType = isset($options['returnType']) ? $options['returnType'] : 'fileOnly';
		$filesList = self::_findFilesRecursive($dir, '', $fileTypes, $exclude, $level, $returnType);
		sort($filesList);
		return $filesList;
	}
	
	/**
	 * Deletes the given file
	 * @param string $file
	 * @return bool
	 */
	public static function deleteFile($file = '')
	{
        $result = @unlink($file);
        self::_errorHanler('file-deleting-error', A::t('core', 'An error occurred while deleting the file {file}.', array('{file}'=>$file)));
		return $result;
	}

	/**
	 * Returns size of the given file
	 * @param string $file
	 * @param string $units
	 * @return number
	 */
	public static function getFileSize($file, $units = 'kb')
	{
		if(!$file || !is_file($file)) return 0;
		
		$filesSize = filesize($file);
		switch(strtolower($units)){
			case 'g':
			case 'gb':
				$result = number_format($filesSize / (1024 * 1024 * 1024), 2, '.', ',');
				break;
			case 'm':
			case 'mb':
				$result = number_format($filesSize / (1024 * 1024), 2, '.', ',');
				break;
			case 'k':
			case 'kb':
				$result = number_format($filesSize / 1024, 2, '.', ',');
				break;
			case 'b':
			default:
				$result = number_format($filesSize, 2, '.', ',');
				break;
		}
		return $result;
	}	
   
	/**
	 * Returns shorten name of the given file
	 * @param string $file
	 * @param int $lengthFirst
	 * @param int $lengthLast
	 * @return string
	 */
	public static function createShortenName($file, $lengthFirst = 10, $lengthLast = 10)
    {
        return preg_replace("/(?<=.{{$lengthFirst}})(.+)(?=.{{$lengthLast}})/", "...", $file);  
    }

	/**
	 * Returns the files found under the specified directory and subdirectories
	 * @param string $dir 
	 * @param string $base 
	 * @param array $fileTypes 
	 * @param array $exclude
	 * @param integer $level
	 * @param string $returnType
	 * @return array 
	 */
	protected static function _findFilesRecursive($dir, $base, $fileTypes, $exclude, $level, $returnType = 'fileOnly')
	{
		$list = array();
		if($hdir = opendir($dir)){
			while(($file = readdir($hdir)) !== false){
				if($file === '.' || $file === '..') continue;
				$path = $dir.DS.$file;
				$isFile = is_file($path);
				if(self::_validatePath($base, $file, $isFile, $fileTypes, $exclude)){
					if($isFile){
						$list[] = ($returnType == 'fileOnly') ? $file : $path;
					}else if($level){
						$list = array_merge($list, self::_findFilesRecursive($path, $base.'/'.$file, $fileTypes, $exclude, $level-1, $returnType));
					}
				}
			}			
		}
		closedir($hdir);
		return $list;
	}

	/**
	 * Validates whether given path is the valid file or directory 
	 * @param string $base
	 * @param string $file
	 * @param boolean $isFile
	 * @param array $fileTypes
	 * @param array $exclude
	 * @return boolean 
	 */
	protected static function _validatePath($base, $file, $isFile, $fileTypes, $exclude)
	{
		foreach($exclude as $e){
			if($file === $e || strpos($base.'/'.$file, $e) === 0) return false;
		}
		if(!$isFile || empty($fileTypes)) return true;
		if(($type = pathinfo($file, PATHINFO_EXTENSION)) !== ''){
			return in_array($type, $fileTypes);
		}else{
			return false;
		}
	}

    /**
     * Handlers errors for specified method
     * @param string $msgType
     * @param string $msg
     */
    private static function _errorHanler($msgType = '', $msg = '')
    {
        if(version_compare(PHP_VERSION, '5.2.0', '>=')){	
            $err = error_get_last();
            if(isset($err['message']) && $err['message'] != ''){
                $lastError = $err['message'].' | file: '.$err['file'].' | line: '.$err['line'];
                $errorMsg = ($lastError) ? $lastError : $msg;
                CDebug::addMessage('errors', $msgType, $errorMsg, 'session');
                @trigger_error('');
            }
        }        
    }
    
}
