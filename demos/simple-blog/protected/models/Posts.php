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
	protected $_fillable = array();
	/** @var */
	protected $_guarded = array('post_datetime');

	
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
		return array(
			'author_id' => array(self::HAS_ONE, 'authors', 'id', 'condition' => '', 'joinType' => self::LEFT_OUTER_JOIN, 'fields' => array('login' => '')),
			'category_id' => array(self::BELONGS_TO, 'categories', 'id', 'condition' => '', 'joinType' => self::LEFT_OUTER_JOIN, 'fields' => array('name' => 'category_name')),
		);
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
		$totalPosts = self::model()->count('category_id = :category_id', array(':category_id' => $pKey));
		$this->_db->update('categories', array('posts_count' => $totalPosts), 'id = ' . (int)$pKey);
	}
}
