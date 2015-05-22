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
	public static function model()
	{
		return parent::model(__CLASS__);
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
