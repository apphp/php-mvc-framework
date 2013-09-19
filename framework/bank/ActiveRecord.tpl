<?php
/**
 * Template of ClassName model 
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct
 * relations
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 *
 */
class ClassName extends CActiveRecord
{

    /** @var string */    
    protected $_table = 'table';

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
	
	public function relations()
	{
		return array(
			'field_name' => array(self::BELONGS_TO, 'table_name', 'field_name', 'condition'=>'', 'joinType'=>self::LEFT_OUTER_JOIN, 'fields'=>array('name'=>'language_name')),
		);
	}
	
}
