<?php
/**
 * CMailer is a helper class file that provides basic mailer functionality
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2019 ApPHP Framework
 * @license http://www.apphpframework.com/license/ 
 *
 * USAGE:
 * ----------
 * 1. Standard call CMailer::config() + CMailer::send()
 * 2. Direct call CMailer::phpMail() or CMailer::phpMailer() or CMailer::smtpMailer()
 * 
 * PUBLIC (static):			PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * config
 * send
 * getError
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

    private static $_smtpAuth = '';
	private static $_smtpSecure = '';
	private static $_smtpHost = '';
	private static $_smtpPort = '';
	private static $_smtpUsername = '';
	private static $_smtpPassword = '';
	
	/**
	 * Sets a basic configuration 
	 * @param array $params
	 */
    public static function config($params)
    {
		self::$_mailer       = isset($params['mailer']) ? $params['mailer'] : 'phpMail';
        self::$_smtpAuth     = isset($params['smtp_auth']) ? $params['smtp_auth'] : (CConfig::get('email.smtp.auth') ? CConfig::get('email.smtp.auth') : '');
		self::$_smtpSecure   = isset($params['smtp_secure']) ? $params['smtp_secure'] : (CConfig::get('email.smtp.secure') ? CConfig::get('email.smtp.secure') : '');
		self::$_smtpHost     = isset($params['smtp_host']) ? $params['smtp_host'] : CConfig::get('email.smtp.host');
		self::$_smtpPort     = isset($params['smtp_port']) ? $params['smtp_port'] : CConfig::get('email.smtp.port');
		self::$_smtpUsername = isset($params['smtp_username']) ? $params['smtp_username'] : CConfig::get('email.smtp.username');
		self::$_smtpPassword = isset($params['smtp_password']) ? $params['smtp_password'] : CConfig::get('email.smtp.password');
	}

	/**
	 * Sends emails using predefined mailer type
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @param array $attachments	Must be a relative path to this file or absolute path to file. e.x.: array('images/flags/en.gif', 'test.zip')
	 * @return boolean
	 */
    public static function send($to, $subject, $message, $params = '', $attachments = array())
    {
		if(!strcasecmp(self::$_mailer, 'smtpMailer')){
			$result = self::smtpMailer($to, $subject, $message, $params, $attachments);
		}elseif(!strcasecmp(self::$_mailer, 'phpMailer')){
			$result = self::phpMailer($to, $subject, $message, $params, $attachments);
		}else{
			$result = self::phpMail($to, $subject, $message, $params, $attachments);
		}
		
		// Write to error log
		if(!$result){
			if(CConfig::get('log.enable')){
				CLog::addMessage('error', self::getError());
			}
		}
		
		$settings = Bootstrap::init()->getSettings();
		if($settings->mailing_log && class_exists('MailingLog')){
			$mailingLog = new MailingLog();
			$mailingLog->email_from = isset($params['from']) ? $params['from'] : '';
			$mailingLog->email_to = $to;
			$mailingLog->email_subject = $subject;
			$mailingLog->email_content = $message;
			$mailingLog->sent_at = date('Y-m-d H:i:s');
			$mailingLog->status = (int)$result;
			$mailingLog->status_description = !$result ? self::getError() : '';
			$mailingLog->save();
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
	 * Sends email using php mail() function
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @param array $attachments	Must be a relative path to this file or absolute path to file
	 * @return boolean
	 */
    public static function phpMail($to, $subject, $message, $params = array(), $attachments = array())
    {
		$charset = 'UTF-8';
		$xMailer = 'PHP-EMAIL-HELPER'; // 'APPHP-EMAIL-HELPER';
		$eol = "\r\n";
		// A random hash will be necessary to send mixed content
		$separator = md5(time());
		 
        $from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';
        $fromHeader = ($fromName) ? $fromName.' <'.$from.'>' : $from;
		$emailType = 'text/'.(CConfig::get('email.isHtml') ? 'html' : 'plain');
		
		// Add headers
		$headers = 'From: '.$fromHeader.$eol.
				   'Reply-To: '.$from.$eol.
				   'Return-Path: '.$from.$eol.
				   'MIME-Version: 1.0'.$eol.
				   'Content-Type: '.$emailType.'; charset='.$charset.$eol.				   
				   'X-Mailer: '.$xMailer.'/'.phpversion();
        
		// Message body
        if(CConfig::get('email.isHtml')) $message = nl2br($message);

		// Add attachments
		if(!empty($attachments)){
			$attachments = (array)$attachments;
			foreach($attachments as $attachment){
				$content = file_get_contents($attachment);
				$message .= '--'.$separator.$eol;
				$message .= 'Content-Type: application/octet-stream; name="'.$attachment.'"'.$eol.$eol;
				$message .= 'Content-Transfer-Encoding: base64'.$eol;
				$message .= 'Content-Disposition: attachment'.$eol;
				$message .= 'Content-Disposition: attachment; filename="'.$attachment.'"'.$eol.$eol;
				$message .= $content.$eol.$eol;
				$message .= '--'.$separator.'--';
			}
		}

		// Don't use additional parameters id there safe mode is enabled
        $additionalParameters = ini_get('safe_mode') ? '' : '-f '.$from;

		$result = @mail($to, $subject, $message, $headers, $additionalParameters);

		if(!$result){
			$err = error_get_last();
			if(isset($err['message']) && $err['message'] != ''){
				self::$_error = $err['message'].' | file: '.$err['file'].' | line: '.$err['line'];
				@trigger_error('');
			}
		}		
		return $result;
    }
	
	/**
	 * Sends email using php PHPMailer class (SMTP)
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @param array $attachments	Must be a relative path to this file or absolute path to file
	 * @return boolean
	 */
    public static function smtpMailer($to, $subject, $message, $params = array(), $attachments = array())
    {
		$from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';

		$mail = PHPMailer::Instance();

		// Set language
		$mail->setLanguage(A::app()->getLanguage());

		// Telling the class to use SMTP
		$mail->isSMTP(); 
		// Enables SMTP debug information (for testing)
		// 0 = off (for production use)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPDebug  = 0;      	
		// Ask for HTML-friendly debug output
		// $mail->Debugoutput = 'html';
		$mail->SMTPAuth   = self::$_smtpAuth == 1 ? true : false; // Enable SMTP authentication
		$mail->SMTPSecure = self::$_smtpSecure; // Sets the prefix to the server
		$mail->Host       = self::$_smtpHost;
		$mail->Port       = self::$_smtpPort;
		$mail->Username   = self::$_smtpUsername;
		$mail->Password   = self::$_smtpPassword;

		// Set 'from' parameters
		// Ex.: $mail->setFrom($mail_from, 'First Last');
		// Ex.: $mail->addReplyTo($mail_to, 'First Last');
		$mail->setFrom($from, $fromName);    
		$mail->addReplyTo($from, $fromName); 
		
		// Add 'to' addresses
		// Ex.: $mail->addAddress($mail_to, 'John Doe'); 	
		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->addAddress($key);  	     
		}
		
		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->msgHTML(nl2br($message));
		
		// Add attachments
		// Ex.: $mail->AddAttachment('images/test_file.gif');      
		if(!empty($attachments)){
			$attachments = (array)$attachments;
			foreach($attachments as $attachment){
				$mail->addAttachment($attachment);
			}
		}

		$result = $mail->send();
		if(!$result){
			self::$_error = $mail->ErrorInfo;
		}

		$mail->clearAddresses(); // Clear previously added 'To' addresses
		$mail->clearReplyTos();  // Clear previously added 'ReplyTo' addresses
		
		return $result;		
	}

	/**
	 * Sends email using php PHPMailer class (php mail() function)
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param array $params
	 * @param array $attachments	Must be a relative path to this file or absolute path to file
	 * @return boolean
	 */
    public static function phpMailer($to, $subject, $message, $params = array(), $attachments = array())
    {
		$from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';

		$mail = PHPMailer::Instance();
		
		// Set language
		$mail->setLanguage(A::app()->getLanguage());

		// Ex.: $mail->setFrom($mail_from, 'First Last');
		$mail->setFrom($from, $fromName);
		// Ex.: $mail->addReplyTo($mail_to, 'First Last');
		$mail->addReplyTo($from, $fromName); 

		// Ex.: $mail->addAddress($mail_to, 'John Doe'); 	
		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->addAddress($key);	     
		}

		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->msgHTML(nl2br($message));

		// Add attachments
		// Ex.: $mail->addAttachment('images/test_file.gif');      
		if(!empty($attachments)){
			$attachments = (array)$attachments;
			foreach($attachments as $attachment){
				$mail->addAttachment($attachment);
			}
		}

		$result = $mail->send();
		if(!$result){
			self::$_error = A::t('core', 'PHPMailer Error:').' '.$mail->ErrorInfo;
		}

		$mail->clearAddresses(); // Clear previously added 'To' addresses
		$mail->clearReplyTos();  // Clear previously added 'ReplyTo' addresses
		
		return $result;    	
	}
	
}