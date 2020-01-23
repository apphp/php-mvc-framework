<?php
/**
 * CUri is a default application component loaded by Apphp.
 * Parses URIs and determines routing.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ----------               ----------                  ----------
 * __construct                                        _detectUri
 * init (static)                                        _filterUri
 * segment                                                _explodeSegments
 * rTotalSegments                                        _uriToAssoc
 * totalSegments                                        _slashSegment
 * uriString
 * uriToAssoc
 * assocToUri
 * slashSegment
 * rSlashSegment
 * segmentArray
 * rSegmentArray
 *
 */

class CUri extends CComponent
{
	
	/** @var array - list of cached URI segments */
	private $_keyVal = array();
	/** @var string - current URI string */
	private $_uriString = '';
	/** @var array - list of URI segments, starts at 0. */
	private $_segments = array();
	/** @var array - list of routed URI segments, starts at 0. */
	private $_rsegments = array();
	/** @var array - PCRE character group allowed in URI segments */
	private $_permittedUriChars = "a-z 0-9~%.:_\-";
	
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
		$this->_uriString = $this->_detectUri();
		$this->_explodeSegments();
	}
	
	/**
	 * Returns the instance of object
	 * @return current class
	 */
	public static function init()
	{
		return parent::init(__CLASS__);
	}
	
	/**
	 * Fetch a URI Segment
	 * This function returns the URI segment based on the number provided.
	 * @param int $n
	 * @param bool $noResult
	 * @return string
	 */
	public function segment($n, $noResult = false)
	{
		return isset($this->_segments[$n]) ? $this->_segments[$n] : $noResult;
	}
	
	/**
	 * Total number of segments
	 * @return int
	 */
	public function totalSegments()
	{
		return count($this->_segments);
	}
	
	/**
	 * Total number of routed segments
	 * @return int
	 */
	public function rTotalSegments()
	{
		return count($this->_rsegments);
	}
	
	/**
	 * Fetch the entire URI string
	 * @return string
	 */
	function uriString()
	{
		return $this->_uriString;
	}
	
	/**
	 * Generate a key value pair from the URI string
	 * This function generates and associative array of URI data starting
	 * at the supplied segment. For example, if this is your URI:
	 *
	 *    example.com/user/search/name/joe/location/UK/gender/male
	 *
	 * You can use this function to generate an array with this prototype:
	 *
	 * array (
	 *            name => joe
	 *            location => UK
	 *            gender => male
	 *         )
	 *
	 * @param int $n the starting segment number
	 * @param array $default an array of default values
	 * @return array
	 */
	public function uriToAssoc($n = 3, $default = array())
	{
		return $this->_uriToAssoc($n, $default, 'segment');
	}
	
	/**
	 * Generate a URI string from an associative array
	 * @param array $array an associative array of key/values
	 * @return array
	 */
	public function assocToUri($array)
	{
		$temp = array();
		foreach ((array)$array as $key => $val) {
			$temp[] = $key;
			$temp[] = $val;
		}
		
		return implode('/', $temp);
	}
	
	/**
	 * Fetch a URI Segment and add a trailing slash
	 * @param int $n
	 * @param string $where
	 * @return string
	 */
	public function slashSegment($n, $where = 'trailing')
	{
		return $this->_slashSegment($n, $where, 'segment');
	}
	
	/**
	 * Fetch a URI Segment and add a trailing slash
	 * @param int $n
	 * @param string $where
	 * @return string
	 */
	public function rSlashSegment($n, $where = 'trailing')
	{
		return $this->_slashSegment($n, $where, 'rsegment');
	}
	
	/**
	 * Returns segment array
	 * @return array
	 */
	public function segmentArray()
	{
		return $this->_segments;
	}
	
	/**
	 * Returns routed segment array
	 * @return array
	 */
	public function rSegmentArray()
	{
		return $this->_rsegments;
	}
	
	/**
	 * Detects the URI
	 * This function will detect the URI automatically and fix the query string if necessary.
	 * @return string
	 */
	private function _detectUri()
	{
		if (!isset($_SERVER['REQUEST_URI']) || !isset($_SERVER['SCRIPT_NAME'])) {
			return '';
		}
		
		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		} elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
		
		// This section ensures that even on servers that require the URI to be in the query string (Nginx)
		// a correct URI is found, and also fixes the QUERY_STRING server var.
		if (strncmp($uri, '?/', 2) === 0) {
			$uri = substr($uri, 2);
		}
		
		if ($uri == '/' || empty($uri)) {
			return '/';
		}
		
		$uri = parse_url($uri, PHP_URL_PATH);
		
		// Do some final cleaning of the URI and return it
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}
	
	/**
	 * Filter segments for malicious characters
	 * @param string $str
	 * @return string
	 */
	private function _filterUri($str)
	{
		if ($str != '' && $this->_permittedUriChars != '') {
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the _permittedUriChars will be parsed as a regex pattern
			if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->_permittedUriChars, '-')) . "]+$|i", $str)) {
				CDebug::addMessage('warnings', 'uri-disallowed-characters', A::t('core', 'The URI you submitted has disallowed characters.'));
			}
		}
		
		// Convert programmatic characters to entities
		$bad = array('$', '(', ')', '%28', '%29');
		$good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
		
		return str_replace($bad, $good, $str);
	}
	
	/**
	 * Explode the URI Segments. The individual segments will be stored in the $this->_segments array.
	 * @return void
	 */
	private function _explodeSegments()
	{
		foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->_uriString)) as $val) {
			// Filter segments for security
			$val = trim($this->_filterUri($val));
			
			if ($val != '') {
				$this->_segments[] = $val;
			}
		}
	}
	
	/**
	 * Generate a key value pair from the URI string or Re-routed URI string
	 * @param int $n the starting segment number
	 * @param array $default an array of default values
	 * @param string $which which array we should use
	 * @return array
	 */
	function _uriToAssoc($n = 3, $default = array(), $which = 'segment')
	{
		if ($which == 'segment') {
			$total_segments = 'total_segments';
			$segment_array = 'segment_array';
		} else {
			$total_segments = 'total_rsegments';
			$segment_array = 'rsegment_array';
		}
		
		if (!is_numeric($n)) {
			return $default;
		}
		
		if (isset($this->_keyVal[$n])) {
			return $this->_keyVal[$n];
		}
		
		if ($this->$total_segments() < $n) {
			if (count($default) == 0) {
				return array();
			}
			
			$retval = array();
			foreach ($default as $val) {
				$retval[$val] = FALSE;
			}
			return $retval;
		}
		
		$segments = array_slice($this->$segment_array(), ($n - 1));
		
		$i = 0;
		$lastval = '';
		$retval = array();
		foreach ($segments as $seg) {
			if ($i % 2) {
				$retval[$lastval] = $seg;
			} else {
				$retval[$seg] = FALSE;
				$lastval = $seg;
			}
			
			$i++;
		}
		
		if (count($default) > 0) {
			foreach ($default as $val) {
				if (!array_key_exists($val, $retval)) {
					$retval[$val] = FALSE;
				}
			}
		}
		
		// Cache the array for reuse
		$this->_keyVal[$n] = $retval;
		return $retval;
	}
	
	/**
	 * Fetch a URI Segment and add a trailing slash - helper function
	 * @param int $n
	 * @param string $where
	 * @param string $which
	 * @return string
	 */
	private function _slashSegment($n, $where = 'trailing', $which = 'segment')
	{
		$leading = '/';
		$trailing = '/';
		
		if ($where == 'trailing') {
			$leading = '';
		} elseif ($where == 'leading') {
			$trailing = '';
		}
		
		return $leading . $this->$which($n) . $trailing;
	}
	
}
