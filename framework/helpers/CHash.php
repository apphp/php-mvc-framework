<?php
/**
 * CHash is a helper class file that provides different encryption methods
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * create
 * getRandomString
 * 
 */	  

class CHash
{    
    /**
     * Creates hash for given password 
     * @param string $algorithm (md5, sha1, sha256, whirlpool, etc.)
     * @param string $data 
     * @param string $salt (should be the same throughout the system probably)
     * @return string (hashed/salted data)
     */
    public static function create($algorithm, $data, $salt)
    {        
        $context = hash_init($algorithm, HASH_HMAC, $salt);
        hash_update($context, $data);
        
        return hash_final($context);        
    }

    /**
     * Creates random string
     * @param integer $length
     * @param array $params - 'numeric', 'positiveNumeric' or 'alphanumeric'
     */
    public static function getRandomString($length = 10, $params = array())
    {
        $type = isset($params['type']) ? $params['type'] : '';
        if($type == 'numeric'){
            $template = '1234567890';    
        }else if($type == 'positiveNumeric'){
            $template = '123456789';    
        }else{
            $template = '1234567890abcdefghijklmnopqrstuvwxyz';
        }        
        settype($template, 'string');
        settype($length, 'integer');
        settype($output, 'string');
        settype($a, 'integer');
        settype($b, 'integer');           
        for($a = 0; $a < $length; $a++){
            $b = rand(0, strlen($template) - 1);
            $output .= $template[$b];
        }       
        return $output;       
    }
    
	/**
	 * Encrypt given value
	 * @param $value
	 * @param $secretKey
	 */
	public static function encrypt($value, $secretKey)
    {
		return trim(strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secretKey, $value, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))), '+/=', '-_,'));
    }
	
	/**
	 * Decrypt given value
	 * @param $value
	 * @param $secretKey
	 */
	public static function decrypt($value, $secretKey)
	{
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secretKey, base64_decode(strtr($value, '-_,', '+/=')), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}
    
   
}
