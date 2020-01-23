<?php

class PageController extends CController
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_view->setMetaTags('keywords', 'apphp framework, static site, apphp');
		$this->_view->setMetaTags('description', 'This is a static web site, consists from the few pages.');
		
		$this->_view->header = '';
		$this->_view->text = '';
		$this->_view->comments = '';
		$this->_view->actionMessage = '';
		$this->_view->activeLink = '';
	}
	
	public function indexAction()
	{
		$this->redirect('index/index');
	}
	
	public function errorAction()
	{
		$this->_view->header = 'Error 404';
		$this->_view->text = CDebug::getMessage('errors', 'action') . '<br>Please check carefully the URL you\'ve typed.';
		$this->_view->render('error/index');
	}
	
	public function servicesAction()
	{
		$this->_view->header = 'Our Services';
		$this->_view->text = '
			Mauris quis nisl sit amet augue adipiscing consectetur. Praesent lacus augue,
			vulputate ullamcorper pharetra in, ultricies et justo. Nulla id sem tortor.
			Ut hendrerit sagittis erat nec tristique. Aenean placerat massa a magna condimentum scelerisque.
			<br><br>
			Ut varius bibendum urna ac convallis. Sed non porta elit. Cum sociis natoque penatibus et magnis
			dis parturient montes, nascetur ridiculus mus. Vestibulum a nunc tincidunt velit egestas pulvinar
			ut a tellus. Cras porta suscipit lorem eget mattis. Donec fringilla, leo in vulputate interdum,
			dolor justo dapibus justo, eget vehicula massa velit ornare justo. Sed et mi in nunc interdum
			malesuada ac eu magna. Mauris ornare vestibulum congue. In scelerisque orci vel velit condimentum
			hendrerit.
		';
		
		$this->_view->render('page/index');
	}
	
	public function stuffAction()
	{
		$this->_view->header = 'Free Stuff';
		$this->_view->text = '
			Mauris quis nisl sit amet augue adipiscing consectetur. Praesent lacus augue,
			vulputate ullamcorper pharetra in, ultricies et justo. Nulla id sem tortor.
			Ut hendrerit sagittis erat nec tristique. Aenean placerat massa a magna condimentum scelerisque.
			<br><br>
			Ut varius bibendum urna ac convallis. Sed non porta elit. Cum sociis natoque penatibus et magnis
			dis parturient montes, nascetur ridiculus mus. Vestibulum a nunc tincidunt velit egestas pulvinar
			ut a tellus. Cras porta suscipit lorem eget mattis. Donec fringilla, leo in vulputate interdum,
			dolor justo dapibus justo, eget vehicula massa velit ornare justo. Sed et mi in nunc interdum
			malesuada ac eu magna. Mauris ornare vestibulum congue. In scelerisque orci vel velit condimentum
			hendrerit.
		';
		
		$this->_view->comments = '
			Nullam imperdiet varius sem a consequat. Duis lacinia congue purus. Nunc pharetra vestibulum
			nisi quis placerat. Integer quis neque urna. Sed quis risus enim, eu egestas justo. Vestibulum
			eu turpis odio. Mauris tincidunt quam non nisl ornare ultricies. Proin vel venenatis metus.
			Pellentesque eu metus et odio scelerisque vulputate. Morbi eget enim eros, gravida rhoncus mi.
		';
		
		$this->_view->render('page/index');
	}
	
	public function contactAction($act = '')
	{
		$cRequest = A::app()->getRequest();
		$this->_view->errorField = '';
		$this->_view->firstName = $cRequest->getPost('first_name');
		$this->_view->lastName = $cRequest->getPost('last_name');
		$this->_view->email = $cRequest->getPost('email');
		$this->_view->message = $cRequest->getPost('message');
		$msg = '';
		
		if ($cRequest->getPost('act') == 'send') {
			
			$result = CWidget::create('CFormValidation', array(
				'fields' => array(
					'first_name' => array('title' => 'First Name', 'validation' => array('required' => true, 'type' => 'mixed')),
					'last_name' => array('title' => 'Last Name', 'validation' => array('required' => true, 'type' => 'mixed')),
					'email' => array('title' => 'Email', 'validation' => array('required' => true, 'type' => 'email')),
					'message' => array('title' => 'Message', 'validation' => array('required' => true, 'type' => 'any')),
				),
			));
			
			if ($result['error']) {
				$msg = $result['errorMessage'];
				$this->_view->errorField = $result['errorField'];
			} else {
				// send email
				$body = 'From: ' . $cRequest->getPost('first_name') . ' ' . $cRequest->getPost('last_name') . "\n";
				$body .= 'Email: ' . $cRequest->getPost('email') . "\n";
				$body .= 'Message: ' . $cRequest->getPost('message');
				
				CMailer::config(array('mailer' => CConfig::get('email.mailer')));
				if (APPHP_MODE == 'demo') {
					$this->_view->actionMessage = CWidget::create('CMessage', array('warning', '<b>:(</b> Sorry, but sending emails is blocked in DEMO version!'));
				} else {
					if (CMailer::send($this->_view->email, 'Contact Us - message', $body, array('from' => CConfig::get('email.from')))) {
						$this->redirect('page/contact?act=success');
					} else {
						if (APPHP_MODE == 'debug') $this->_view->actionMessage = CWidget::create('CMessage', array('error', CMailer::getError()));
						else $this->_view->actionMessage = CWidget::create('CMessage', array('error', 'An error occurred while sending your message! Please try again later.'));
					}
				}
			}
			
			if (!empty($msg)) {
				$this->_view->firstName = $cRequest->getPost('first_name', 'string');
				$this->_view->lastName = $cRequest->getPost('last_name', 'string');
				$this->_view->email = $cRequest->getPost('email', array('string', 'email'));
				$this->_view->message = $cRequest->getPost('message', 'string');
				
				$this->_view->actionMessage = CWidget::create('CMessage', array('validation', $msg));
			}
		} elseif ($cRequest->getQuery('act') == 'success') {
			$this->_view->actionMessage = CWidget::create('CMessage', array('success', 'Your message has been successfully sent!'));
		}
		
		$this->_view->header = 'Contact Us';
		$this->_view->text = '
			Etiam ornare ultricies nulla. Mauris porta, lacus et accumsan commodo, quam nisl semper arcu,
			vitae pretium nibh leo vel nibh. Phasellus eu aliquam massa. Curabitur venenatis, augue ut laoreet
			placerat, augue quam semper justo, in euismod nisl arcu in enim. Morbi tincidunt dolor a tortor
			mattis adipiscing. Mauris eros purus, sollicitudin eget porta ut, pretium vitae mi. Etiam malesuada
			feugiat orci non volutpat. Praesent eget eros blandit ante tincidunt cursus. Nullam cursus neque eu
			massa lobortis imperdiet porta nibh commodo. Cum sociis natoque penatibus et magnis dis parturient
			montes, nascetur ridiculus mus. Sed viverra congue lobortis. Donec rhoncus, leo sed posuere
			scelerisque, velit leo tempor risus, vitae dapibus sapien quam eu urna. Cras id nisl eu dui tempus
			pulvinar.';
		
		$this->_view->render('page/index');
	}
	
	public function aboutAction()
	{
		$this->_view->header = 'About Us';
		$this->_view->text = '
			Vivamus faucibus magna vel metus pharetra eu laoreet sem congue. Sed gravida condimentum ipsum quis
			fringilla. Maecenas rutrum convallis ullamcorper. Praesent ante orci, eleifend nec hendrerit quis,
			suscipit quis quam. Ut fringilla posuere eros, eu mollis arcu porta eu. Curabitur tempus, metus quis
			bibendum adipiscing, urna felis pellentesque nunc, at adipiscing lacus diam et ipsum. Quisque in
			mauris at metus laoreet lobortis. Maecenas nec tortor enim, sit amet accumsan tortor.
		';
		$this->_view->activeLink = 'about_us';
		$this->_view->render('page/info');
	}
	
	public function ourHistoryAction()
	{
		$this->_view->header = 'Our History';
		$this->_view->text = '
			Maecenas rutrum convallis ullamcorper. Praesent ante orci, eleifend nec hendrerit quis,
			suscipit quis quam. Ut fringilla posuere eros, eu mollis arcu porta eu. Curabitur tempus, metus quis
			bibendum adipiscing, urna felis pellentesque nunc, at adipiscing lacus diam et ipsum.
		';
		$this->_view->activeLink = 'our_history';
		$this->_view->render('page/info');
	}
	
	public function ourPartnersAction()
	{
		$this->_view->header = 'Our Partners';
		$this->_view->text = '
			Cum sociis natoque penatibus et magnis dis parturient
			montes, nascetur ridiculus mus. Sed viverra congue lobortis. Donec rhoncus, leo sed posuere
			scelerisque, velit leo tempor risus, vitae dapibus sapien quam eu urna. Cras id nisl eu dui tempus
			pulvinar.
		';
		$this->_view->activeLink = 'our_partners';
		$this->_view->render('page/info');
	}
	
	public function moreInfoAction()
	{
		$this->_view->header = 'More Info';
		$this->_view->text = '
			Ut varius bibendum urna ac convallis. Sed non porta elit. Cum sociis natoque penatibus et magnis
			dis parturient montes, nascetur ridiculus mus. Vestibulum a nunc tincidunt velit egestas pulvinar
			ut a tellus. Cras porta suscipit lorem eget mattis. Donec fringilla, leo in vulputate interdum,
			dolor justo dapibus justo, eget vehicula massa velit ornare justo. Sed et mi in nunc interdum
			malesuada ac eu magna. Mauris ornare vestibulum congue. In scelerisque orci vel velit condimentum
			hendrerit.
		';
		$this->_view->activeLink = 'more_info';
		$this->_view->render('page/info');
	}
	
}