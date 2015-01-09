<?php
/**
 * Pages
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * -----------             ------------------         ------------------
 * __construct             _afterSave
 *                         _relations
 *
 * STATIC:
 * ---------------------------------------------------------------
 * model
 * 
 */
class Pages extends CActiveRecord
{   
    protected $_table = 'pages';
    public $categoryOldId;
	
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
	 * @return array 
	 */
	protected function _relations()
	{
		return array(
			'menu_id' => array(self::BELONGS_TO, 'menus', 'id', 'condition'=>'', 'joinType'=>self::LEFT_OUTER_JOIN, 'fields'=>array('name'=>'menu_name')),
		);
	}

	/**
	 * This method is invoked after saving a record successfully
	 * @param string $pk
	 */
	protected function _afterSave($id = 0)
	{
		$this->isError = false;
			
		// if this page is home page - remove this flag from all other pages
		if($this->is_homepage){
        	if(!$this->_db->update($this->_table, array('is_homepage'=>0), 'id != '.$id)){
        		$this->isError = true;
        	}
		}
	}
  
}
