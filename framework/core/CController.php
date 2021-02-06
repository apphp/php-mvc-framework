<?php
/**
 * CController base class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:                    PROTECTED:                  PRIVATE:
 * ---------------            ---------------             ---------------
 * __construct                _accessRules                _getCalledClass
 * execute                    _filtersAccessControl
 * testAction
 * errorAction
 * redirect
 *
 */

class CController
{
	/** @var string */
	protected $_view;
	protected $_action;
	
	/**
	 * Class constructor
	 * @return void
	 */
	function __construct()
	{
		$this->_view = A::app()->view;
		$this->_action = '';
	}
	
	/**
	 * Runs action
	 * @param string $action
	 * @param array $params
	 * @return void
	 */
	final public function execute($action = '', $params = [])
	{
		if (!empty($action)) {
			$this->_action = $action;
			$rules = $this->_accessRules();
			if ($this->_filtersAccessControl($rules)) {
				call_user_func_array(array($this, $action), $params);
			} else {
				// TODO add warning in Debug panel
			}
		} else {
			$this->redirect('error/index');
		}
	}
	
	/**
	 * Renders test action
	 * @return bool|void
	 */
	public function testAction()
	{
		if (APPHP_MODE == 'test') {
			$controller = $this->_getCalledClass();
			if ($controller . DS . 'index' == $this->_view->render($controller . '/index')) {
				return true;
			} else {
				return false;
			}
		} else {
			$this->redirect('error/index');
		}
	}
	
	/**
	 * Renders error 404 view
	 * @return void
	 */
	public function errorAction()
	{
		$this->_view->header = 'Error 404';
		$this->_view->text = '';
		
		$errors = CDebug::getMessage('errors', 'action');
		if (is_array($errors)) {
			foreach ($errors as $error) {
				$this->_view->text .= $error;
			}
		}
		$this->_view->render('error/index');
	}
	
	/**
	 * Redirects to another controller
	 * Parameter may consist from 2 parts: controller/action or just controller name
	 * @param string $path Redirect path
	 * @param int $code HTTP Response status code
	 * @param bool $isDirectUrl
	 * @return void
	 */
	public function redirect($path, $isDirectUrl = false, $code = '')
	{
		if (APPHP_MODE == 'test'){
			return true;
		}
		
		if (!$isDirectUrl) {
			$paramsParts = explode('/', $path);
			$calledController = str_replace('controller', '', strtolower($this->_getCalledClass()));
			$params = '';
			$baseUrl = A::app()->getRequest()->getBaseUrl();
			
			// Set controller and action according to given parameters
			if (!empty($path)) {
				$parts = count($paramsParts);
				if ($parts == 1) {
					$controller = $calledController;
					$action = isset($paramsParts[0]) ? $paramsParts[0] : '';
				} elseif ($parts == 2) {
					$controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
					$action = isset($paramsParts[1]) ? $paramsParts[1] : '';
				} elseif ($parts > 2) {
					$controller = isset($paramsParts[0]) ? $paramsParts[0] : $calledController;
					$action = isset($paramsParts[1]) ? $paramsParts[1] : '';
					for ($i = 2; $i < $parts; $i++) {
						$params .= (isset($paramsParts[$i]) ? '/' . $paramsParts[$i] : '');
					}
				}
			}
			
			$newLocation = $baseUrl . $controller . '/' . $action . $params;
		} else {
			$newLocation = $path;
		}
		
		// Prepare redirection code
		// 301 - Moved Permanently
		// 303 - See Other (since HTTP/1.1)
		// 307 - Temporary Redirect (since HTTP/1.1)
		// 302 - Found
		if (empty($code) || !is_numeric($code)) {
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
				// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
			} else {
				$code = 302;
			}
		}
		
		// Close the session with user data
		A::app()->getSession()->closeSession();
		
		// Perform redirection
		header('location: ' . $newLocation, true, $code);
		exit;
	}
	
	/**
	 * Used to define access rules to controller
	 * This method should be overridden
	 * @return array
	 *
	 * @usage
	 *    return array(
	 *        array('allow',
	 *            'actions' => array('*'),
	 *            'ips' => array('127.0.0.1'),
	 *        ),
	 *        array('deny',
	 *            'actions' => array('index','view', 'create', 'update', 'manage'),
	 *            'ips' => array('*')
	 *        ),
	 * );
	 *
	 */
	protected function _accessRules()
	{
		return [];
	}
	
	/**
	 * Applies rules to action
	 * Hint: deny actions have priority on allow actions
	 * @param array $rules
	 * @return bool
	 */
	protected function _filtersAccessControl($rules = [])
	{
		$allowed = !empty($rules[0]) ? $rules[0] : [];
		$denied = !empty($rules[1]) ? $rules[1] : [];
		$current_ip = A::app()->getRequest()->getUserHostAddress();
		$return = true;
		
		if (empty($allowed) && empty($denied)) {
			return $return;
		}
		
		if ($allowed) {
			$actions = !empty($allowed['actions']) ? (array)$allowed['actions'] : [];
			$ips = !empty($allowed['ips']) ? (array)$allowed['ips'] : [];
			
			$access = $this->_filterAccessControl('allowed', $actions, $ips, $current_ip);
			if ($access !== null) {
				$return = $access;
			}
		}
		
		if ($denied) {
			$actions = !empty($denied['actions']) ? (array)$denied['actions'] : [];
			$ips = !empty($denied['ips']) ? (array)$denied['ips'] : [];
			
			$access = $this->_filterAccessControl('denied', $actions, $ips, $current_ip);
			if ($access !== null) {
				$return = $access;
				if (!$access) {
					CDebug::addMessage('warnings', 'access-denied', A::t('core', 'Access to {controller}::{action}() denied for this IP: {ip address}', array('{controller}' => get_class($this), '{action}' => $this->_action, '{ip address}' => $current_ip)));
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Applies rule
	 * @param string $type
	 * @param array $actions
	 * @param array $ips
	 * @return bool
	 */
	protected function _filterAccessControl($type, $actions, $ips, $current_ip)
	{
		$return = null;
		
		if (!empty($actions)) {
			$return = ($type == 'denied' ? true : false);
			
			// Check if action is defined
			foreach ($actions as $action) {
				// Action found - now check it
				if ($action === '*' || (str_ireplace('Action', '', $this->_action) === $action)) {
					$return = ($type == 'denied' ? false : true);
					
					if (!empty($ips)) {
						$return = ($type == 'denied' ? true : false);
						
						// Check if IP address is denied
						foreach ($ips as $ip) {
							if ($type == 'denied') {
								if ($ip !== '*' && $current_ip === $ip) {
									$return = false;
									break(2);
								}
							} else {
								if ($ip === '*' || $current_ip === $ip) {
									$return = true;
									break(2);
								}
							}
						}
					}
				}
			}
		}
		
		return $return;
	}
	
	/**
	 * Returns the name of called class
	 * @return string|bool
	 */
	private function _getCalledClass()
	{
		if (function_exists('get_called_class')) return get_called_class();
		$bt = debug_backtrace();
		if (!isset($bt[1])) {
			// Cannot find called class -> stack level too deep
			return false;
		} elseif (!isset($bt[1]['type'])) {
			// Type not set
			return false;
		} else switch ($bt[1]['type']) {
			case '::':
				$lines = file($bt[1]['file']);
				$i = 0;
				$callerLine = '';
				do {
					$i++;
					$callerLine = $lines[$bt[1]['line'] - $i] . $callerLine;
				} while (stripos($callerLine, $bt[1]['function']) === false);
				preg_match('/([a-zA-Z0-9\_]+)::' . $bt[1]['function'] . '/', $callerLine, $matches);
				if (!isset($matches[1])) {
					// Could not find caller class: originating method call is obscured
					return false;
				}
				return $matches[1];
				break;
			case '->':
				switch ($bt[1]['function']) {
					case '__get':
						// Edge case -> get class of calling object 
						if (!is_object($bt[1]['object'])) {
							// Edge case fail. __get called on non object
							return false;
						}
						return get_class($bt[1]['object']);
					default:
						return $bt[1]['class'];
				}
				break;
			default:
				// Unknown backtrace method type
				return false;
				break;
		}
		return false;
	}
	
}