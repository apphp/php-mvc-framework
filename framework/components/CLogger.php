<?php
/**
 * CLogger is a component that allows to write messages to log files
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ----------               ----------                  ----------
 * __construct              _formatLine
 * init (static)
 * writeLog
 *
 */

class CLogger extends CComponent
{
	
	/** @var string - path to save log files */
	protected $_logPath;
	/** @var int - permissions for log files */
	protected $_filePermissions = 0644;
	/** @var int - log levels */
	protected $_threshold = 1;
	/** @var array - array of threshold levels */
	protected $_thresholdArray = array();
	/** @var string - timestamp format */
	protected $_dateFormat = 'Y-m-d H:i:s';
	/** @var int - log files lifetime in days */
	protected $_lifetime = 30;
	/** @var string - filename extension */
	protected $_fileExtension;
	/** @var bool - whether logger can write or not to the log files */
	protected $_enabled = true;
	/** @var array - array of logging levels */
	protected $_levels = array('error' => 1, 'debug' => 2, 'info' => 3, 'all' => 4);
	
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
		$this->_enabled = CConfig::get('log.enable') !== '' ? CConfig::get('log.enable') : false;
		$this->_logPath = APPHP_PATH.DS.(CConfig::get('log.path') !== '' ? CConfig::get('log.path') : 'protected/tmp/logs/');
		$this->_fileExtension = CConfig::exists('log.fileExtension') && CConfig::get('log.fileExtension') !== '' ? ltrim(CConfig::get('log.fileExtension'), '.') : 'php';
		$this->_dateFormat = CConfig::get('log.dateFormat') !== '' ? CConfig::get('log.dateFormat') : '';
		$this->_lifetime = CConfig::get('log.lifetime') !== '' ? CConfig::get('log.lifetime') : '';
		$logThreshold = CConfig::get('log.threshold') !== '' ? CConfig::get('log.threshold') : '';
		$logFilePermissions = CConfig::get('log.filePermissions') !== '' ? CConfig::get('log.filePermissions') : '';
		
		if (!file_exists($this->_logPath)) {
			mkdir($this->_logPath, 0755, true);
		}
		
		if (!is_dir($this->_logPath) || !CFile::isWritable($this->_logPath)) {
			$this->_enabled = false;
		}
		
		if (is_numeric($logThreshold)) {
			$this->_threshold = (int)$logThreshold;
		} elseif (is_array($logThreshold)) {
			$this->_threshold = 0;
			$this->_thresholdArray = array_flip($logThreshold);
		}
		
		if (!empty($this->$logFilePermissions) && is_int($this->$logFilePermissions)) {
			$this->_filePermissions = $this->$logFilePermissions;
		}
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
	 * Write to log file
	 * This function will be called using the system helper CLog::addMessage() method
	 * @param string $level The error level: 'error', 'debug' or 'info'
	 * @param string $msg The error message
	 * @return bool
	 */
	public function writeLog($level, $msg = '')
	{
		if ($this->_enabled === false) {
			return false;
		}
		
		if ((!isset($this->_levels[$level]) || ($this->_levels[$level] > $this->_threshold))
			&& !isset($this->_thresholdArray[$this->_levels[$level]])) {
			return false;
		}
		
		$filePath = $this->_logPath . 'log-' . date('Y-m-d') . '.' . $this->_fileExtension;
		$message = '';
		
		if (!file_exists($filePath)) {
			$newFile = true;
			// Only add protection to php files
			if ($this->_fileExtension === 'php') {
				$message .= "<?php exit('No direct script access allowed'); ?>\n\n";
			}
			
			// Delete old log files
			if (!empty($this->_lifetime) && is_int($this->_lifetime)) {
				CFile::removeDirectoryOldestFile($this->_logPath, $this->_lifetime, array('error.log', 'payments.log'));
			}
		}
		
		// Open or create file for writing at end-of-file
		if (!$fp = @fopen($filePath, 'ab')) {
			return false;
		}
		
		flock($fp, LOCK_EX);
		
		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos($this->_dateFormat, 'u') !== false) {
			$microtimeFull = microtime(true);
			$microtimeShort = sprintf("%06d", ($microtimeFull - floor($microtimeFull)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.' . $microtimeShort, $microtimeFull));
			$date = $date->format($this->_dateFormat);
		} else {
			$date = date($this->_dateFormat);
		}
		
		$message .= $this->_formatLine(strtoupper($level), $date, $msg);
		
		for ($written = 0, $length = strlen($message); $written < $length; $written += $result) {
			if (($result = fwrite($fp, substr($message, $written))) === false) {
				break;
			}
		}
		
		flock($fp, LOCK_UN);
		fclose($fp);
		
		if (isset($newFile) && $newFile === true) {
			chmod($filePath, $this->_filePermissions);
		}
		
		return is_int($result);
	}
	
	/**
	 * Format the log line
	 * This function is used for extensibility of log formatting
	 * @param string $level The error level
	 * @param string $date Formatted date string
	 * @param string $message The log message
	 * @return string
	 */
	protected function _formatLine($level, $date, $message = '')
	{
		return $level . ' - ' . $date . ' --> ' . $message . "\n";
	}
	
}
