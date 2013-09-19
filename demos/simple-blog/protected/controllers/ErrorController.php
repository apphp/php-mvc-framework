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
        $this->view->blogName = $settings->blog_name;
        $this->view->blogSlogan = $settings->slogan;
        $this->view->blogFooter = $settings->footer;

        if(in_array($code, array('404', '500'))){
            $redirectCode = $code;
        }else{
            $redirectCode = 'index';
        }
        $this->view->render('error/'.$redirectCode);                
    }    
   
}