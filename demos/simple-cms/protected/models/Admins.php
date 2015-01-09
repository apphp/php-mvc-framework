<?php
/**
 * Admins
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * -----------             ------------------         ------------------
 * __construct             _customFields
 * 
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */
class Admins extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'admins';

    public function __construct()
    {
        parent::__construct();
    }

	/**
	 * Returns the static model of the specified AR class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	/**
     * Used to define custom fields
	 * This method should be overridden
	 */
	protected function _customFields()
	{
		return array(
			'CONCAT(first_name, " ", last_name)' => 'fullname'
		);
	}
	
 
}
