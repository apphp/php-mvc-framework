<?php
/**
 * Post Entity
 *
 * PUBLIC:                 PROTECTED                  PRIVATE
 * -----------             ------------------         ------------------
 * __construct
 *
 */

class PostEntity extends CRecordEntity
{
	
	protected $_primaryKey = 'id';
	protected $_pkValue = 0;
	
	/** @var */
	protected $_fillable = array();
	/** @var */
	protected $_guarded = array('post_datetime');

	/**
	 * Class constructor
	 * @param int $pkVal
	 */
	public function __construct($pkVal = 0)
	{
		parent::__construct($pkVal);
		
		///$this->setPrimaryKey($pkVal);
		
		///$this->_columns[$this->_primaryKey] = 0;
		$this->_columns['header'] = '';
		$this->_columns['category_id'] = '';
		$this->_columns['post_text'] = '';
		$this->_columns['author_id'] = '';
		$this->_columns['post_datetime'] = '';
		$this->_columns['metatag_title'] = '';
		$this->_columns['metatag_keywords'] = '';
		$this->_columns['metatag_description'] = '';
	}
	
}