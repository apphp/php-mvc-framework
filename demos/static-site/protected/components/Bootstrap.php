<?php
/**
 * Bootstrap - bootstrap component class for application
 *
 * PUBLIC:                    PRIVATE:
 * -----------              ------------------
 * __construct
 * init (static)
 * setTimeZone
 * setSslMode
 * setCron
 * setLastVisitedPage
 * getSettings
 *
 */

class Bootstrap extends CComponent
{
	
	private $_settings;
	
	/**
	 * Class default constructor
	 */
	function __construct()
	{
		A::app()->attachEventHandler('_onBeginRequest', array($this, 'setTimeZone'));
		A::app()->attachEventHandler('_onBeginRequest', array($this, 'setSslMode'));
		A::app()->attachEventHandler('_onBeginRequest', array($this, 'setCron'));
		
		A::app()->attachEventHandler('_onEndRequest', array($this, 'setLastVisitedPage'));
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
	 * Sets timezone according to database settings
	 */
	public function setTimeZone()
	{
	}
	
	/**
	 * Sets (forces) ssl mode (if requred)
	 */
	public function setSslMode()
	{
		$sslEnabled = false;
		
		if ($sslEnabled && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')) {
			header('location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			exit;
		}
	}
	
	/**
	 * Sets cron job
	 */
	public function setCron()
	{
		// Un-comment if 'non-batch' cron job type is used
		//$cron = new Cron();
		//$cron->run();
	}
	
	/**
	 * Sets last visited page
	 */
	public function setLastVisitedPage()
	{
	}
	
	/**
	 * Returns site settings
	 * Helps to prevent multiple call of Settings::model()->findByPk(1);
	 */
	public function getSettings($param = '')
	{
	}
	
}