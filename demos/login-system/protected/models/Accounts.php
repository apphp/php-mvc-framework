<?php

class Accounts extends CModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getInfo($id)
	{
		$result = $this->_db->select('
            SELECT id, username, password
            FROM ' . CConfig::get('db.prefix') . 'accounts
            WHERE id = :id',
			array(':id' => (int)$id)
		);
		
		if (count($result) > 0) {
			return $result[0];
		} else {
			return array('username' => '', 'password' => '');
		}
	}
	
	public function save($username, $password)
	{
		$salt = CConfig::get('password.encryption') && (CConfig::get('password.encryptSalt')) ? CHash::salt() : '';
		$result = $this->_db->update(
			'accounts',
			array(
				'username' => $username,
				'salt' => $salt,
				'password' => (CConfig::get('password.encryption') ? CHash::create(CConfig::get('password.encryptAlgorithm'), $password, $salt) : $password)
			),
			'id = ' . (int)CAuth::getLoggedId()
		);
		return $result;
	}
}