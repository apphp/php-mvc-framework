<?php
/**
 * [MODEL_NAME] model 
 *
 * PUBLIC:                 PRIVATE
 * -----------             ------------------
 * __construct
 * save
 *
 */

class [MODEL_NAME] extends CModel
{

    /** @var object */
    private static $_instance;

    /** @var string */
    protected $_table = '[TABLE_NAME]';

    /** Class constructor */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the static model of the current class
     */
    public static function model()
    {
        if(self::$_instance == null){
            self::$_instance = new self();
        }
		
        return self::$_instance;
    }

    /** Example of method that updates data in database */
    public function save($id, $field_1, $field_2)
    {
        $result = $this->_db->update(
            $this->_table,
            array(
                'field_1' => $field_1,
                'field_2' => $field_2,
            ),
            'id = '.(int)$id
        );
        return $result;
    }    
	
}
