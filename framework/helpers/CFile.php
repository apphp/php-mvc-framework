<?php
/**
 * CFile is a helper class that provides a set of helper methods for common file system operations
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
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * getExtension          	                            findFilesRecursive
 * deleteDirectory                                      validatePath
 * emptyDirectory
 * copyDirectory
 * isDirectoryEmpty
 * findSubDirectories
 * copyFile
 * findFiles
 * deleteFile
 * getFileSize
 * 
 */	  

class CFile
{
    
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
	 * Deletes given directory with files it includes 
	 * @param string $dir
	 * @return bool
	 */
	public static function deleteDirectory($dir = '')
	{
		return self::emptyDirectory($dir);
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
		return @rmdir($dir);
	}

	/**
	 * Copies content of source directory into destination directory
	 * Warning: if the destination file already exists, it will be overwritten
	 * @param string $src
	 * @param string $dest
	 * @param bool $fullPath
	 * @return bool
	 */
	public static function copyDirectory($src = '', $dest = '', $fullPath = true)
	{
		$result = false;
		$dirPath = (($fullPath) ? APPHP_PATH.'/' : '').$src;
		
		if(is_dir($dirPath)){
			$dir = opendir($dirPath);
			if(!$dir) return $result;
			if(!file_exists(trim($dest, '/').'/')) mkdir((($fullPath) ? APPHP_PATH.'/' : '').$dest);
			while(false !== ($file = readdir($dir))){	
				if(($file != '.') && ($file != '..')){				
					$fromDir = trim($src, '/').'/'.$file;
					$toDir = trim($dest, '/').'/'.$file;
					if(is_dir($fromDir)){
						$result = self::copyDirectory($fromDir, $toDir, $fullPath);
					}else{
						$result = copy($fromDir, $toDir);	
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
	 * Copies file
	 * @param string $src
	 * @param string $dest
	 * @return bool
	 */
	public static function copyFile($src = '', $dest = '')
	{
		return @copy($src, $dest);
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
		$filesList = self::findFilesRecursive($dir, '', $fileTypes, $exclude, $level, $returnType);
		sort($filesList);
		return $filesList;
	}
	
	/**
	 * Deletes given file
	 * @param string $file
	 * @return bool
	 */
	public static function deleteFile($file = '')
	{
		return unlink($file);
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
	 * Returns the files found under the specified directory and subdirectories
	 * @param string $dir 
	 * @param string $base 
	 * @param array $fileTypes 
	 * @param array $exclude
	 * @param integer $level
	 * @param string $returnType
	 * @return array 
	 */
	protected static function findFilesRecursive($dir, $base, $fileTypes, $exclude, $level, $returnType = 'fileOnly')
	{
		$list = array();
		if($hdir = opendir($dir)){
			while(($file = readdir($hdir)) !== false){
				if($file === '.' || $file === '..') continue;
				$path = $dir.DS.$file;
				$isFile = is_file($path);
				if(self::validatePath($base, $file, $isFile, $fileTypes, $exclude)){
					if($isFile){
						$list[] = ($returnType == 'fileOnly') ? $file : $path;
					}else if($level){
						$list = array_merge($list, self::findFilesRecursive($path, $base.'/'.$file, $fileTypes, $exclude, $level-1, $returnType));
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
	protected static function validatePath($base, $file, $isFile, $fileTypes, $exclude)
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
    
}
