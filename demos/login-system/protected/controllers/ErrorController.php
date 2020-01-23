<?php

class ErrorController extends CController
{
	
	public function indexAction()
	{
		$this->_view->header = 'Error 404: page not found!';
		$this->_view->text = 'THE PAGE YOU WERE LOOKING FOR COULD NOT BE FOUND
		<br><br>
		This could be the result of the page being removed, the name being changed or the
		page being temporarily unavailable. This could be the result of the page being removed,
		the name being changed or the page being temporarily unavailable.
		
		<br><br>
		TROUBLESHOOTING
		<ul>
			<li>If you spelled the URL manually, double check the spelling</li>
			<li>Go to our website\'s home page, and navigate to the content in question</li>
			<li>Alternatively, you can search our website below</li>
		</ul>
		';
		
		$this->_view->render('error/index');
	}
	
}