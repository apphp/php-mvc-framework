<?php

/**
 * ErrorController
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * indexAction
 *
 */
class ErrorController extends CController
{
    
	public function indexAction($code = '404')
	{
        $settings = Settings::model()->findByPk(1);
        $this->_view->setMetaTags('title', 'Error | '.$settings->metatag_title);
        $this->_view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->_view->setMetaTags('description', $settings->metatag_description);
        $this->_view->cmsName = $settings->site_name;
        $this->_view->cmsSlogan = $settings->slogan;
        $this->_view->cmsFooter = $settings->footer;

        if(in_array($code, array('404', '500'))){
            $redirectCode = $code;
        }else{
            $redirectCode = 'index';
        }
        $this->_view->render('error/'.$redirectCode);                
    }    
   
}