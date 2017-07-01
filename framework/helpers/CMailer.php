<?php
/**
 * CMailer is a helper class file that provides basic mailer functionality
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2016 ApPHP Framework
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
	 * @param array $attachments
	 * @return boolean
	 */
    public static function send($to, $subject, $message, $params = '', $attachments = array())
    {
		if(!strcasecmp(self::$_mailer, 'smtpMailer')){
			$result = self::smtpMailer($to, $subject, $message, $params, $attachments);
		}elseif(!strcasecmp(self::$_mailer, 'phpMailer')){
			$result = self::phpMailer($to, $subject, $message, $params, $attachments);
		}else{
			$result = self::phpMail($to, $subject, $message, $params);
		}
		
		// Write to error log
		if(!$result){
			if(CConfig::get('log.enable')){
				CLog::addMessage('error', self::getError());
			}
		}
		
		if(class_exists('MailingLog')){
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
	 * @return boolean
	 */
    public static function phpMail($to, $subject, $message, $params = '')
    {
		$charset = 'UTF-8';
		$xMailer = 'PHP-EMAIL-HELPER'; // 'APPHP-EMAIL-HELPER';
        $from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';
        $fromHeader = ($fromName) ? $fromName.' <'.$from.'>' : $from;
		$emailType = 'text/'.(CConfig::get('email.isHtml') ? 'html' : 'plain');
        if(CConfig::get('email.isHtml')) $message = nl2br($message);

		// Don't use additional parameters id there safe mode is enabled
        $additionalParameters = ini_get('safe_mode') ? '' : '-f '.$from;

		$headers = 'From: '.$fromHeader."\r\n".
				   'Reply-To: '.$from."\r\n".
				   'Return-Path: '.$from."\r\n".
				   'MIME-Version: 1.0'."\r\n".
				   'Content-Type: '.$emailType.'; charset='.$charset."\r\n".				   
				   'X-Mailer: '.$xMailer.'/'.phpversion();
        
        
		$result = @mail($to, $subject, $message, $headers, $additionalParameters);

		if(!$result){
			if(version_compare(phpversion(), '5.2.0', '>=')){
                $err = error_get_last();
				if(isset($err['message']) && $err['message'] != ''){
					self::$_error = $err['message'].' | file: '.$err['file'].' | line: '.$err['line'];
                    @trigger_error('');
				}
			}else{
				self::$_error = A::t('core', 'PHPMail Error: an error occurred while sending email.');
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
    public static function smtpMailer($to, $subject, $message, $params = '', $attachments = array())
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

		// Set "from" parameters
		// Ex.: $mail->setFrom($mail_from, 'First Last');
		// Ex.: $mail->addReplyTo($mail_to, 'First Last');
		$mail->setFrom($from, $fromName);    
		$mail->addReplyTo($from, $fromName); 
		
		// Add "to" addresses
		// Ex.: $mail->addAddress($mail_to, 'John Doe'); 	
		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->addAddress($key);  	     
		}
		
		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->msgHTML(nl2br($message));
		
		// Add attachments
		// Ex.: $mail->AddAttachment("images/test_file.gif");      
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
    public static function phpMailer($to, $subject, $message, $params = '', $attachments = array())
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
		// Ex.: $mail->addAttachment("images/test_file.gif");      
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