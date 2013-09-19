<?php

/**
 * Posts
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
class Posts extends CActiveRecord
{   
    protected $_table = 'posts';
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
	public function relations()
	{
		return array(
			'author_id'   => array(self::HAS_ONE, 'authors', 'id', 'condition'=>'', 'joinType'=>self::LEFT_OUTER_JOIN, 'fields'=>array('login'=>'')),
			'category_id' => array(self::BELONGS_TO, 'categories', 'id', 'condition'=>'', 'joinType'=>self::LEFT_OUTER_JOIN, 'fields'=>array('name'=>'category_name')),
		);
	}
 
	protected function afterSave($pk = '')
	{
        if($this->categoryOldId != $this->category_id){
            $this->updatePostsCount($this->categoryOldId);
            $this->updatePostsCount($this->category_id);        
        }
	}

	protected function afterDelete($pk = '')
	{
        // use category ID saved on beforeDelete to call updatePostsCount()
        $this->updatePostsCount($this->category_id);
	}
    
    private function updatePostsCount($pKey)
    {
        // update total count of posts in categories table
        $totalPosts = self::model()->count('category_id = :category_id', array(':category_id' => $pKey));
        $this->db->update('categories', array('posts_count' => $totalPosts), 'id = '.(int)$pKey);
    }
}
