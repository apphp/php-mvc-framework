<?php

class Login extends CModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function login($username, $password)
	{
		$admin = $this->_db->select('
            SELECT id, role, salt, password
            FROM ' . CConfig::get('db.prefix') . 'accounts
            WHERE username = :username',
			array(':username' => $username)
		);
		
		if (!empty($admin)) {
			$savedPassword = $admin[0]['password'];
			if (CConfig::get('password.encryption')) {
				$checkSalt = CConfig::get('password.encryptSalt') ? $admin[0]['salt'] : '';
				$checkPassword = CHash::create(CConfig::get('password.encryptAlgorithm'), $password, $checkSalt);
			} else {
				$checkPassword = $password;
			}
			
			if (CHash::equals($savedPassword, $checkPassword)) {
				$session = A::app()->getSession();
				$session->set('loggedRole', $admin[0]['role']);
				$session->set('loggedIn', true);
				$session->set('loggedId', $admin[0]['id']);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
