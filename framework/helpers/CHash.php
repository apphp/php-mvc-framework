<?php
/**
 * CHash is a helper class file that provides different encryption methods
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC (static):			PROTECTED:					PRIVATE (static):
 * ----------               ----------                  ----------
 * create                                               _padKey
 * salt
 * equals
 * getRandomToken
 * getRandomString
 * getSequentialString
 * encrypt
 * decrypt
 * getRandomOrIterationString
 * 
 */

class CHash
{

	/* @var */
	private static $_separator = ':|:';


	/**
	 * Creates hash for given password
	 * @param string $algorithm (md5, sha1, sha256, whirlpool, etc.)
	 * @param string $data
	 * @param string $salt
	 * @return string (hashed/salted data)
	 */
	public static function create($algorithm, $data, $salt = '')
	{
		if (!empty($salt)) {
			$context = hash_init($algorithm, HASH_HMAC, $salt);
		} else {
			$context = hash_init($algorithm);
		}
		
		hash_update($context, $data);
		
		return hash_final($context);
	}
	
	/**
	 * Generates salt
	 * @return string
	 */
	public static function salt()
	{
		if (version_compare(phpversion(), '7.0.0', '<')) {
			return base64_encode(mcrypt_create_iv(24, MCRYPT_DEV_URANDOM));
		} else {
			return substr(strtr(base64_encode(hex2bin(self::getRandomToken(32))), '+', '.'), 0, 44);
		}
	}
	
	/**
	 * Compares two strings $a and $b in length-constant time
	 * @param string $a
	 * @param string $b
	 * @return bool
	 */
	public static function equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}
	
	/**
	 * Creates random token
	 * @param int $length
	 * @return string
	 */
	public static function getRandomToken($length = 32)
	{
		$token = '';
		
		if (!isset($length) || intval($length) <= 8) {
			$length = 32;
		}
		
		if (version_compare(phpversion(), '7.0.0', '<') && function_exists('mcrypt_create_iv')) {
			$token = bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
		} elseif (function_exists('random_bytes')) {
			$token = bin2hex(random_bytes($length));
		} elseif (function_exists('openssl_random_pseudo_bytes')) {
			$token = bin2hex(openssl_random_pseudo_bytes($length));
		}
		
		return $token;
	}
	
	/**
	 * Creates random string
	 * @param integer $length
	 * @param array $params
	 * type: 'numeric', 'positiveNumeric', 'alphanumeric', 'alpha'
	 * case: 'upper', 'lower' (default)
	 * @return string
	 */
    public static function getRandomString($length = 10, $params = [])
    {
        $type = isset($params['type']) ? $params['type'] : '';
		$case = isset($params['case']) ? $params['case'] : '';
		
		if ($type == 'numeric') {
			$template = '1234567890';
		} elseif ($type == 'positiveNumeric') {
			$template = '123456789';
		} elseif ($type == 'alpha') {
			$template = 'abcdefghijklmnopqrstuvwxyz';
		} else {
			$template = '1234567890abcdefghijklmnopqrstuvwxyz';
		}
		
		if ($case == 'upper') $template = strtoupper($template);
		settype($template, 'string');
		settype($length, 'integer');
		settype($output, 'string');
		settype($a, 'integer');
		settype($b, 'integer');
		
		for ($a = 0; $a < $length; $a++) {
			$b = rand(0, strlen($template) - 1);
			$output .= $template[$b];
		}
		
		return $output;
	}
	
	/**
	 * Creates random string
	 * @param integer $length
	 * @param boolean $isRandom
	 * @param integer $id
	 * @return string
	 */
	public static function getRandomOrIterationString($length = 10, $isRandom = true, $id = 0)
	{
		if ($isRandom) {
			$result = self::getRandomString($length);
		} else {
			$result = sprintf('%0' . (int)$length . 'd', $id);
		}
		
		return $result;
	}
	
	/**
	 * Creates sequential string
	 * @param int $numeric
	 * @param int $length
	 * @return string
	 */
	public static function getSequentialString($number = '', $length = 10)
	{
		return str_pad($number, $length, '0', STR_PAD_LEFT);
	}
	
	/**
	 * Encrypt given value
	 * @param mixed $value
	 * @param string $secretKey
	 * @param string $algorithm ('aes-256-cbc', 'aes-128-cbc', etc..)
	 * @return string
	 */
	public static function encrypt($value, $secretKey, $algorithm = 'aes-256-cbc')
	{
		$secretKey = self::_padKey($secretKey);
		
		if (version_compare(phpversion(), '7.0.0', '<')) {
			$return = strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secretKey, $value, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))), '+/=', '-_,');
		} else {
			// Generate an initialization vector
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algorithm));
			// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector
			$encrypted = openssl_encrypt($value, $algorithm, $secretKey, 0, $iv);
			// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (self::$_separator)
			$return = base64_encode($encrypted . self::$_separator . base64_encode($iv));
		}
		
		return trim($return);
	}
	
	/**
	 * Decrypt given value
	 * @param $value
	 * @param $secretKey
	 * @param string $algorithm ('aes-256-cbc', 'aes-128-cbc', etc..)
	 * @return string
	 */
	public static function decrypt($value, $secretKey, $algorithm = 'aes-256-cbc')
	{
		$secretKey = self::_padKey($secretKey);
		$return = '';
		
		if (!empty($value)) {
			if (version_compare(phpversion(), '7.0.0', '<')) {
				$return = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secretKey, base64_decode(strtr($value, '-_,', '+/=')), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND));
			} else {
				// To decrypt, split the encrypted data from our IV - our unique separator used was "self::$_separator"
				list($encrypted_data, $iv) = array_pad(explode(self::$_separator, base64_decode($value), 2), 2, '');

				// Validate IV value
				$iv = base64_decode($iv);
				if ( strlen($iv) == 16) {
					$return = openssl_decrypt($encrypted_data, $algorithm, $secretKey, 0, $iv);
				}
			}
		}
		
		return trim($return);
	}
	
	/**
	 * Pad key
	 * @param string $key
	 * @return string
	 */
	private static function _padKey($key)
	{
		// Key is too large
		if (strlen($key) > 32) return false;
		
		// Set sizes
        $sizes = [16, 24, 32];

        // Loop through sizes and pad key
		foreach ($sizes as $s) {
			if ($s > strlen($key)) {
				$key = str_pad($key, $s, "\0");
				break;
			}
		}
		
		return $key;
	}
}
