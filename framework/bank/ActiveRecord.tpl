<?php
/**
 * Template of ClassName model 
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * -----------             ------------------         ------------------
 * __construct             _relations
 * model (static)
 *
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
	public static function model()
	{
		return parent::model(__CLASS__);
	}
	
	/**
     * Used to define relations between different tables in database and current $_table
	 */
	protected function _relations()
	{
		return array(
			'field_name' => array(self::BELONGS_TO, 'table_name', 'field_name', 'condition'=>'', 'joinType'=>self::LEFT_OUTER_JOIN, 'fields'=>array('name'=>'language_name')),
		);
	}
	
}
