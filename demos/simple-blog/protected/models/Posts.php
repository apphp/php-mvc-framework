<?php

/**
 * Posts model
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * -----------             ------------------         ------------------
 * __construct             _relations
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
	
	/** @var */
    protected $_fillable = [];
    /** @var */
    protected $_guarded = ['post_datetime'];


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
	 * @return array
	 */
	protected function _relations()
	{
        return [
            'author_id' => [
                self::HAS_ONE,
                'authors',
                'id',
                'condition' => '',
                'joinType'  => self::LEFT_OUTER_JOIN,
                'fields'    => ['login' => '']
            ],
            'category_id' => [
                self::BELONGS_TO,
                'categories',
                'id',
                'condition' => '',
                'joinType'  => self::LEFT_OUTER_JOIN,
                'fields'    => ['name' => 'category_name']
            ],
        ];
    }

    protected function _afterSave($pk = '')
	{
		if ($this->categoryOldId != $this->category_id) {
			$this->_updatePostsCount($this->categoryOldId);
			$this->_updatePostsCount($this->category_id);
		}
	}
	
	protected function _afterDelete($pk = '')
	{
		// use category ID saved on beforeDelete to call _updatePostsCount()
		$this->_updatePostsCount($this->category_id);
	}
	
	private function _updatePostsCount($pKey)
	{
		// update total count of posts in categories table
        $totalPosts = self::model()->count('category_id = :category_id', [':category_id' => $pKey]);
        $this->_db->update('categories', ['posts_count' => $totalPosts], 'id = '.(int)$pKey);
    }
}
