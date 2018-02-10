<?php
/**
 * Bootstrap - bootstrap component class for application
 *
 * PUBLIC:         			PRIVATE:
 * -----------              ------------------
 * __construct
 * init (static)
 * setTimeZone
 * setWebsiteInfo
 * setDefaultLanguage
 * setDefaultCurrency
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
        $this->_settings = Settings::model()->findByPk(1);
		
		// Check if site is offline
		if($this->_settings->is_offline){
            if(CAuth::isLoggedInAsAdmin() || stripos(A::app()->getRequest()->getRequestUri(), 'backend/login')){
                // Allow viewing
            }else{
                $siteInfo = SiteInfo::model()->find('language_code = :lang', array(':lang'=>A::app()->getLanguage()));
                A::app()->view->sitePhone = $siteInfo ? $siteInfo->site_phone : '';
                A::app()->view->siteFax = $siteInfo ? $siteInfo->site_fax : '';
                A::app()->view->siteEmail = $siteInfo ? $siteInfo->site_email : '';
				A::app()->view->siteAddress = $siteInfo ? $siteInfo->site_address : '';
                A::app()->view->siteTitle = $siteInfo ? $siteInfo->header : '';
                A::app()->view->slogan = $siteInfo ? $siteInfo->slogan : '';
                A::app()->view->footer = $siteInfo ? $siteInfo->footer : '';
                A::app()->view->offlineMessage = $this->_settings->offline_message;
                A::app()->view->renderContent('offline');
                exit;                
            }
		}		
        
        A::app()->attachEventHandler('_onBeginRequest', array($this, 'setTimeZone'));
		//A::app()->attachEventHandler('_onBeginRequest', array($this, 'setWebsiteInfo'));
		//A::app()->attachEventHandler('_onBeginRequest', array($this, 'setDefaultLanguage'));
		//A::app()->attachEventHandler('_onBeginRequest', array($this, 'setDefaultCurrency'));
        A::app()->attachEventHandler('_onBeginRequest', array($this, 'setSslMode'));
		A::app()->attachEventHandler('_onBeginRequest', array($this, 'setCron'));
		
		A::app()->attachEventHandler('_onEndRequest', array($this, 'setLastVisitedPage'));
	}

	/**
     *	Returns the instance of object
     *	@return current class
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
		if($this->_settings->time_zone != ''){
			$timeZone = $this->_settings->time_zone;
		}else{
			$timeZone = CConfig::get('defaultTimeZone', 'UTC');
		}
		A::app()->getLocalTime()->setTimeZone($timeZone);
	}
    
	/**
	 * Sets site info
	 */	
	public function setWebsiteInfo()
	{
		//Website::setInfo();
	}
	
	/**
	 * Sets default language
	 * @param bool $force
	 */	
	public function setDefaultLanguage($force = false)
	{
//        if(A::app()->getLanguage('', false) == '' || $force){
//            if($defaultLang = Languages::model()->find('is_default = 1')){
//                $params = array(
//					'name' => $defaultLang->name,
//					'name_native' => $defaultLang->name_native,
//                    'locale' => $defaultLang->lc_time_name,
//                    'direction' => $defaultLang->direction,
//					'icon' => $defaultLang->icon,
//                );
//                A::app()->setLanguage($defaultLang->code, $params);    
//            }			
//        }
	}

	/**
	 * Sets default currency
	 */	
	public function setDefaultCurrency()
	{
//		if(A::app()->getSession()->get('currency_code') == ''){
//			if($defaultCurrency = Currencies::model()->find('is_default = 1')){
//                $params = array(
//					'name' => $defaultCurrency->name,
//                    'symbol' => $defaultCurrency->symbol,
//                    'symbol_place' => $defaultCurrency->symbol_place,                    
//                    'decimals' => $defaultCurrency->decimals,
//                    'rate' => $defaultCurrency->rate
//                );
//				A::app()->setCurrency($defaultCurrency->code, $params);
//			}
//		}
	}

	/**
	 * Sets (forces) ssl mode (if requred)
	 */	
	public function setSslMode()
	{
        $sslEnabled = false;
        
        if($this->_settings->ssl_mode == 1){
            $sslEnabled = true; 
        }elseif($this->_settings->ssl_mode == 2 && CAuth::isLoggedInAsAdmin()){
            $sslEnabled = true; 
        }elseif($this->_settings->ssl_mode == 3 && CAuth::isLoggedInAs('user','customer','client')){
            $sslEnabled = true;            
        //}elseif($this->_settings->ssl_mode == 4){
        //$sslEnabled = true; 
        }

        if($sslEnabled && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')){
            header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
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
		//Website::setLastVisitedPage();
	}
	
 	/**
	 * Returns site settings
	 * Helps to prevent multiple call of Settings::model()->findByPk(1);
	 */	
	public function getSettings($param = '')
	{
        $settings = $this->_settings->getFieldsAsArray();
		if($param !== ''){
            return isset($settings[$param]) ? $settings[$param] : '';
        }else{
            return $this->_settings;
        }
    }   

}