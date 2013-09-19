<?php
/**
 * CMailer is a helper class file that provides basic maler functionality
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 *
 * USAGE:
 * ----------
 * 1. Standard call CMailer::config() + CMailer::send()
 * 2. Direct call CMailer::phpMail() or CMailer::phpMailer() or CMailer::smtpMailer()
 * 
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * config
 * send
 * phpMail
 * smtpMailer
 * phpMailer
 * 
 */	  

include(dirname(__FILE__).'/../vendors/phpmailer/class.phpmailer.php');

class CMailer
{
	private static $_mailer = 'phpMail';
	private static $_error = '';

	private static $_smtp_secure = '';
	private static $_smtp_host = '';
	private static $_smtp_port = '';
	private static $_smtp_username = '';
	private static $_smtp_password = '';
	
	/**
	 * Sets a basic configuration 
	 * @param string $params
	 */
    public static function config($params)
    {
		self::$_mailer        = isset($params['mailer']) ? $params['mailer'] : 'phpMail';
		self::$_smtp_secure   = isset($params['smtp_secure']) ? $params['smtp_secure'] : (CConfig::get('email.smtp.secure') ? CConfig::get('email.smtp.secure') : '');
		self::$_smtp_host     = isset($params['smtp_host']) ? $params['smtp_host'] : CConfig::get('email.smtp.host');
		self::$_smtp_port     = isset($params['smtp_port']) ? $params['smtp_port'] : CConfig::get('email.smtp.port');
		self::$_smtp_username = isset($params['smtp_username']) ? $params['smtp_username'] : CConfig::get('email.smtp.username');
		self::$_smtp_password = isset($params['smtp_password']) ? $params['smtp_password'] : CConfig::get('email.smtp.password');
	}

	/**
	 * Sends emails using pre-defined mailer type
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @return boolean
	 */
    public static function send($to, $subject, $message, $params = '')
    {
		if(!strcasecmp(self::$_mailer, 'smtpMailer')){
			return self::smtpMailer($to, $subject, $message, $params);
		}else if(!strcasecmp(self::$_mailer, 'phpMailer')){
			return self::phpMailer($to, $subject, $message, $params);
		}else{
			return self::phpMail($to, $subject, $message, $params);
		}		
	}

	/**
	 * Sends email using php mail() function
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @return boolean
	 */
    public static function phpMail($to, $subject, $message, $params = '')
    {
		$charset = 'UTF-8';
		$xMailer = 'PHP-EMAIL-HELPER'; // 'APPHP-EMAIL-HELPER';
        $from = isset($params['from']) ? $params['from'] : '';
		$emailType = 'text/'.(CConfig::get('email.isHtml') ? 'html' : 'plain'); 

		$additionalParameters = '-f '.$from;	

		$headers = 'From: '.$from."\r\n".
				   'Reply-To: '.$from."\r\n".
				   'Return-Path: '.$from."\r\n".
				   'MIME-Version: 1.0'."\r\n".
				   'Content-Type: '.$emailType.'; charset='.$charset."\r\n".				   
				   'X-Mailer: '.$xMailer.'/'.phpversion();
        
        
		$result = @mail($to, $subject, $message, $headers, $additionalParameters);

		if(!$result){
			if(version_compare(PHP_VERSION, '5.2.0', '>=')){	
				$err = error_get_last();
				if(!empty($err)){
					self::$_error = isset($err['message']) ? $err['message'] : '';	
				}
			}else{
				self::$_error = A::t('core', 'PHPMail Error: an error occurred while sending email.');
			}
		}		
		return $result;
    }
	
	/**
	 * Returns error message
	 * @return string 
	 */
    public static function getError()
    {
		return self::$_error;
	}	

	/**
	 * Sends email using php PHPMailer class (SMTP)
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @return boolean
	 */
    public static function smtpMailer($to, $subject, $message, $params = '')
    {
		$from = isset($params['from']) ? $params['from'] : '';

		$mail = PHPMailer::Instance();

		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPDebug  = 0;      	// enables SMTP debug information (for testing)
										// 1 = errors and messages
										// 2 = messages only
		$mail->SMTPAuth   = true;   	// enable SMTP authentication
		$mail->SMTPSecure = self::$_smtp_secure;      // sets the prefix to the server
		$mail->Host       = self::$_smtp_host;
		$mail->Port       = self::$_smtp_port;
		$mail->Username   = self::$_smtp_username;
		$mail->Password   = self::$_smtp_password;

		$mail->SetFrom($from);        	// $mail->SetFrom($mail_from, 'First Last');
		$mail->AddReplyTo($from);   	// $mail->AddReplyTo($mail_to, 'First Last');
		
		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->AddAddress($key);  	// $mail->AddAddress($mail_to, 'John Doe'); 	
		}
		
		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->MsgHTML(nl2br($message));
		
		//$mail->AddAttachment("images/test_file.gif");      // attachment
		//$mail->AddAttachment("images/test_file_thumb.gif"); // attachment

		$result = $mail->Send();
		if(!$result){
			self::$_error = $mail->ErrorInfo;
		}

		$mail->ClearAddresses(); // clear previously added 'To' addresses
		$mail->ClearReplyTos();  // clear previously added 'ReplyTo' addresses
		
		return $result;		
	}

	/**
	 * Sends email using php PHPMailer class (php mail() function)
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @return boolean
	 */
    public static function phpMailer($to, $subject, $message, $params = '')
    {
		$from = isset($params['from']) ? $params['from'] : '';

		$mail = PHPMailer::Instance();
		
		$mail->SetFrom($from);        	// $mail->SetFrom($mail_from, 'First Last');
		$mail->AddReplyTo($from);     	// $mail->AddReplyTo($mail_to, 'First Last');

		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->AddAddress($key);	// $mail->AddAddress($mail_to, 'John Doe'); 	
		}

		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->MsgHTML(nl2br($message));

		//$mail->AddAttachment("images/test_file.gif");      // attachment
		//$mail->AddAttachment("images/test_file_thumb.gif"); // attachment

		$result = $mail->Send();
		if(!$result){
			self::$_error = A::t('core', 'PHPMailer Error:').' '.$mail->ErrorInfo;
		}

		$mail->ClearAddresses(); // clear previously added 'To' addresses
		$mail->ClearReplyTos();  // clear previously added 'ReplyTo' addresses
		
		return $result;    	
	}
	
}