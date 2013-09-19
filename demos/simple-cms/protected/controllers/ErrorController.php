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
        $this->view->setMetaTags('title', 'Error | '.$settings->metatag_title);
        $this->view->setMetaTags('keywords', $settings->metatag_keywords);
        $this->view->setMetaTags('description', $settings->metatag_description);
        $this->view->cmsName = $settings->site_name;
        $this->view->cmsSlogan = $settings->slogan;
        $this->view->cmsFooter = $settings->footer;

        if(in_array($code, array('404', '500'))){
            $redirectCode = $code;
        }else{
            $redirectCode = 'index';
        }
        $this->view->render('error/'.$redirectCode);                
    }    
   
}