<?php
/**
 * Menus
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */
class Menus extends CActiveRecord
{
    
    /** @var string */    
    protected $_table = 'menus';
    
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

}
