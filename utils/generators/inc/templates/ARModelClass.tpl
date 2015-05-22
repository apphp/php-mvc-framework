<?php
/**
 * [MODEL_NAME] model 
 *
 * PUBLIC:                 	PROTECTED:          		PRIVATE:
 * ---------------         	---------------           	---------------
 * __construct             	_relations                 
 * model (static)          	_customFields
 *
 */

class [MODEL_NAME] extends CActiveRecord
{

    /** @var string */    
    protected $_table = '[TABLE_NAME]';

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
     * Defines relations between different tables in database and current $_table
	 */
    protected function _relations()
    {
        return array(
            /*
            'field_name' => array(
                self::BELONGS_TO,
                'table_name',
                'field_name',
                'condition'=>'',
                'joinType'=>self::LEFT_OUTER_JOIN,
                'fields'=>array('name'=>'language_name')
            ), */
        );
    }
    
    /**
     * Used to define custom fields
     * This method should be overridden
     * Usage: 'CONCAT(first_name, " ", last_name)' => 'fullname'
     */
    protected function _customFields()
    {
        return array();
    }    
    
}
