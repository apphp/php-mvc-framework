<?php
/**
 * CActiveRecord base class for classes that represent relational data.
 * It implements the active record design pattern.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		        
 * ----------               ----------                  ----------              
 * __construct              beforeSave                  getRelations
 * __set                    afterSave                   getCustomFields
 * __get                    beforeDelete                addCustomFields
 * __unset                  afterDelete                 removeCustomFields
 * set
 * get
 * getError
 * getErrorMessage
 * primaryKey
 * getPrimaryKey
 * getTableName
 * customFields
 * relations
 * getFieldsAsArray
 * isNewRecord
 * getTranslations
 * saveTranslations
 *
 * createObjectFromTable
 * 
 * find
 * findByPk
 * findByAttributes
 * findAll
 *
 * save
 * clearPkValue
 *
 * delete
 * deleteByPk
 * deleteAll
 *
 * exists
 * count
 * 
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * model
 *
 */	  

abstract class CActiveRecord extends CModel
{	
	/** @var object */    
    private static $_instance;
	/** @var Database */
	protected $db;	
	/**	@var boolean */
	protected $_error;
	/**	@var string */
	protected $_errorMessage;

    /* class name => model */
    private static $_models = array();			

    /**	@var string */
    protected $_table = '';
    /**	@var string */
    protected $_tableTranslation = '';
	/**	@var */ 
	protected $_columns = array();
    
	/**	@var */ 
    private $_columnTypes = array();
	/**	@var */ 
    private $_pkValue = 0;
    /**	@var */ 
	private $_primaryKey;
    /**	@var */ 
    private $_isNewRecord = false;
    
    /**	@var */ 
    private static $_joinTypes = array(
        'INNER JOIN',
        'OUTER JOIN',
        'LEFT JOIN',
        'LEFT OUTER JOIN',
        'RIGHT JOIN',
        'RIGHT OUTER JOIN',
        'JOIN'
    );

	const BELONGS_TO = 1; /* many-to-one */
	const HAS_ONE = 2;    /* one-to-one */
	const HAS_MANY = 3;   /* one-to-many */
	const MANY_MANY = 4;  /* many-to-many */
    
    const INNER_JOIN = 'INNER JOIN';
    const OUTER_JOIN = 'OUTER JOIN';
    const LEFT_JOIN = 'LEFT JOIN';
    const LEFT_OUTER_JOIN = 'LEFT OUTER JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const RIGHT_OUTER_JOIN = 'RIGHT OUTER JOIN';
    const JOIN = 'JOIN';
    
    
	/**
	 * Class constructor
	 * @param array $params
	 */
	public function __construct() 
	{
		$this->db = CDatabase::init();
        
        if(!empty($this->_table)){
            $this->createObjectFromTable();
            $this->_pkValue = 0;
        }
        
        $this->_error = CDatabase::getError();
        $this->_errorMessage = CDatabase::getErrorMessage();
	}

	/**
	 * Initializes the database class
	 * @param array $params
	 */
	public static function init($params = array())
	{
		if(self::$_instance == null) self::$_instance = new self($params);
        return self::$_instance;    		
	}
    
	/**
	 * Returns the static model of the specified AR class
	 * @param string $className
	 * 
	 * EVERY derived AR class must override this method in following way,
	 * <pre>
	 * public static function model($className = __CLASS__)
	 * {
	 *     return parent::model($className);
	 * }
	 * </pre>
	 */
	public static function model($className = __CLASS__)
	{        
		if(isset(self::$_models[$className])){
			return self::$_models[$className];
		}else{
			return self::$_models[$className] = new $className(null);
		}        
    }

	/**	
	 * Setter
	 * @param $index
	 * @param $value
	 */
	public function __set($index, $value)
	{
        $this->_columns[$index] = $value;
	}
 
	/**	
	 * Getter
	 * @param $index
	 */
	public function __get($index)
	{        
        if(isset($this->_columns[$index])){
            return $this->_columns[$index];
        }else{
            CDebug::AddMessage('errors', 'wrong_column'.$index, A::t('core', 'Wrong column name: {index} in table {table}', array('{index}'=>$index, '{table}'=>$this->_table)));
            return '';  
        } 
	}

	/**
	 * Sets a active record property to be null
	 * @param string $name
	 */
	public function __unset($index)
	{
		if(isset($this->_columns[$index])) unset($this->_columns[$index]);
	}

	/**	
	 * Setter
	 * @param $index
	 * @param $value
	 */
	public function set($index, $value)
	{
        $this->_columns[$index] = $value;
	}

	/**	
	 * Getter
	 * @param $index
	 * @param $value
	 */
	public function get($index)
	{
        if(isset($this->_columns[$index])){
            return $this->_columns[$index];
        }else{
            CDebug::AddMessage('errors', 'wrong_column'.$index, A::t('core', 'Wrong column name: {index} in table {table}', array('{index}'=>$index, '{table}'=>$this->_table)));
            return '';  
        } 
	}

	/**	
	 * Get error status
	 * @return boolean
	 */
	public function getError()
	{
		return $this->_error;
	}
 
	/**	
	 * Get error message
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->_errorMessage;
	}
    
	/**
	 * Returns the primary key of the associated database table
	 * @return string
	 */
	public function primaryKey()
	{
        return $this->_primaryKey;
	}
    
	/**
	 * Returns the primary key value
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
        return $this->_pkValue;
    }
    
	/**
	 * Returns the table name value
	 * @return string
	 */
	public function getTableName()
	{
        return $this->_table;
    }
    
	/**
     * Used to define custom fields
	 * This method should be overridden
	 */
	public function customFields()
	{
		return array();
	}

	/**
     * Used to define relations between different tables in database and current $_table
	 * This method should be overridden
	 */
	public function relations()
	{
		return array();
	}
    
	/**
	 * Returns fields as array 
	 * @return array
	 */
    public function getFieldsAsArray()
    {
        return $this->_columns;   
    }
    
	/**
	 * Returns if current operation is on new record or not
	 * @return bool
	 */
    public function isNewRecord()
    {
        return $this->_isNewRecord;   
    }
    
	/**
	 * Returns array of translation fields 
	 * @param array $params 
	 * @return array
	 */
	public function getTranslations($params = array())
	{
        $key = isset($params['key']) ? $params['key'] : '';
        $value = isset($params['value']) ? $params['value'] : '';
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $resultArray = array();
        
		$result = $this->db->select(
            'SELECT * FROM '.CConfig::get('db.prefix').$this->_tableTranslation.' WHERE '.$key.' = :'.$key,
			array(':'.$key => $value)
		);
		foreach($result as $res){
            foreach($fields as $field){
                $resultArray[$res['language_code']][$field] = $res[$field];    
            }			
		}
		return $resultArray;
	}	

	/**
	 * Saves array of translation fields 
	 * @param array $params
	 * @return array
	 */
	public function saveTranslations($params = array())
	{
        $key = isset($params['key']) ? $params['key'] : '';
        $value = isset($params['value']) ? $params['value'] : '';
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $paramsTranslation = array();
        
        foreach($fields as $lang => $langInfo){                
            foreach($langInfo as $langField => $langFieldValue){
                $paramsTranslation[$langField] = $langFieldValue;
            }
            if($this->isNewRecord()){        
                $paramsTranslation[$key] = $value;
                $paramsTranslation['language_code'] = $lang;
                $this->db->insert($this->_tableTranslation, $paramsTranslation);
            }else{
                $this->db->update($this->_tableTranslation, $paramsTranslation, $key.'="'.$value.'" AND language_code="'.$lang.'"');
            }
        }
    }    
    

    /*****************************************************************
     *  ACTIVE RECORED METHODS
     *****************************************************************/    
	/**
    * Create empty object from table
    * @return bool
    */
    private function createObjectFromTable()
    {
        if(is_null($this->_table)){
            return false;
        }

        $cols = $this->db->showColumns($this->_table);
        if(!is_array($cols)) return false;

        foreach($cols as $array){
            // insert default value $array[4]
            $this->_columns[$array[0]] = ($array[4] != '') ? $array[4] : '';
            $arrayParts = explode('(', $array[1]);
            $this->_columnTypes[$array[0]] = array_shift($arrayParts);
            if($array[3] == 'PRI'){
                $this->_primaryKey = $array[0];
            }
        }       
        $this->addCustomFields();
        
        if($this->_primaryKey == ''){
            $this->_primaryKey = 'id';
        }
        return true;
    }

    /**
    * This method queries your database to find first related object
    * Ex.: find('postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
    * @param mixed $conditions
    * @param array $params 
    */
    public function find($conditions = '', $params = '')
    {
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            $order = isset($conditions['order']) ? $conditions['order'] : '';             
        }else{
            $where = $conditions;
            $order = '';
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
        $orderBy = !empty($order) ? ' ORDER BY '.$order : '';
        $relations = $this->getRelations();
        $customFields = $this->getCustomFields();
    
        $sql = 'SELECT
                    `'.CConfig::get('db.prefix').$this->_table.'`.*
                    '.$relations['fields'].'
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    '.$relations['tables'].'
                '.$whereClause.'
                '.$orderBy.'
                LIMIT 1';
        return $this->db->select($sql, $params);
    }

    /**
    * This method queries your database to find related objects by PK
    * Ex.: findByPk($pk, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
    * @param string $pk
    * @param mixed $conditions
    * @param array $params 
    * @return bolean
    */
    public function findByPk($pk, $conditions = '', $params = '')
    {
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            $order = isset($conditions['order']) ? $conditions['order'] : '';             
        }else{
            $where = $conditions;
            $order = '';
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
        $orderBy = !empty($order) ? ' ORDER BY '.$order : '';
        $relations = $this->getRelations();
        $customFields = $this->getCustomFields();
    
        $sql = 'SELECT
                    `'.CConfig::get('db.prefix').$this->_table.'`.*
                    '.$customFields.'
                    '.$relations['fields'].'
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    '.$relations['tables'].'
                WHERE `'.CConfig::get('db.prefix').$this->_table.'`.'.$this->_primaryKey.' = '.(int)$pk.'
                '.$whereClause.'
                '.$orderBy.'
                LIMIT 1';
        $result = $this->db->select($sql, $params);
        if(isset($result[0]) && is_array($result[0])){
            foreach($result[0] as $key => $val){
                $this->$key = $val;
            }
            $this->_pkValue = $pk;
            return $this;
        }else{
            return null;   
        }        
    }
    
    /**
    * This method queries your database to find related objects by attributes
    * Ex.: findByAttributes($attributes, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
    * Ex.: findByAttributes($attributes, array('condition'=>'postID = :postID AND isActive = :isActive', 'order'=>'id DESC', 'limit'=>'0, 10'), 'params'=>array(':postID'=>10, 'isActive'=>1)));
    * @param array $attributes
    * @param mixed $conditions
    * @param array $params 
    */
    public function findByAttributes($attributes, $conditions = '', $params = '')
    {
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            $order = isset($conditions['order']) ? $conditions['order'] : '';
            $limit = isset($conditions['limit']) ? $conditions['limit'] : '';
        }else{
            $where = $conditions;
            $order = '';
            $limit = '';
        }
        $whereClause = !empty($where) ? ' AND '.$where : '';
        $orderBy = !empty($order) ? ' ORDER BY '.$order : '';
        $limit = !empty($limit) ? ' LIMIT '.$limit : '';
        
        $relations = $this->getRelations();
        $customFields = $this->getCustomFields();
    
        $attributes_clause = '';
        foreach($attributes as $key => $value){
            $attributes_clause .= ' AND '.$key." = '".$value."'";
        }
    
        $sql = 'SELECT
                    `'.CConfig::get('db.prefix').$this->_table.'`.*
                    '.$relations['fields'].'
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    '.$relations['tables'].'
                WHERE 1 = 1
                    '.$attributes_clause.'
                '.$whereClause.'
                '.$orderBy.'
               '.$limit;
                
        return $this->db->select($sql, $params);
    }
    
    /** 
    * This method queries your database to find all related objects
    * Ex.: findAll('post_id = :postID AND is_active = :isActive', array(':postID'=>10, ':isActive'=>1));
    * Ex.: findAll(array('condition'=>'post_id = :postID AND is_active = :isActive', 'order'=>'id DESC', 'limit'=>'0, 10'), array(':postID'=>10, ':isActive'=>1));
    * @param mixed $conditions
    * @param array $params 
    */
	public function findAll($conditions = '', $params = '', $fetchMode = PDO::FETCH_ASSOC)
    {
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            $order = isset($conditions['order']) ? $conditions['order'] : '';
            $limit = isset($conditions['limit']) ? $conditions['limit'] : '';
        }else{
            $where = $conditions;
            $order = '';
            $limit = '';
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
        $orderBy = !empty($order) ? ' ORDER BY '.$order : '';
        $limit = !empty($limit) ? ' LIMIT '.$limit : '';
        
        $relations = $this->getRelations();
        $customFields = $this->getCustomFields();
        
        $sql = 'SELECT
                    `'.CConfig::get('db.prefix').$this->_table.'`.*
                    '.$customFields.'
                    '.$relations['fields'].'
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    '.$relations['tables'].'
                '.$whereClause.'
                '.$orderBy.'
                '.$limit;
                
        return $this->db->select($sql, $params);
    }

    /**
     * Save data
     * @return boolean
     */
    public function save()
    {
        $values = array();
        $data = array();
        
        $this->removeCustomFields();

		if($this->beforeSave($this->_pkValue)){
            foreach($this->_columns as $column => $val){
                $relations = $this->getRelations();                
                if($column != 'id' && $column != $this->_primaryKey && !in_array($column, $relations['fieldsArray'])){  //  && ($column != 'created_at') && !$NEW)

                    $value = $this->$column;
                    //if(array_search($this->_columnTypes[$column], array('int', 'float', 'decimal'))){
                    //    $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    //}                    
                    $data[$column] = $value;
                }
            }
            
            if($this->_pkValue > 0){
                $result = $this->db->update($this->_table, $data, $this->_primaryKey.' = '.(int)$this->_pkValue);
            }else{
                $result = $this->db->insert($this->_table, $data);
                $this->_isNewRecord = true;
                // save last inset ID
                $this->_pkValue = (int)$result;
            }
            
            if($result){
                $this->afterSave($this->_pkValue);
                return true;                
            } 
        }else{
            CDebug::AddMessage('errors', 'before-save', A::t('core', 'AR before operation on table: {table}', array('{table}'=>$this->_table)));
        }
        return false;
    }
    
    /**
     * Clear primary key
     * @return boolean
     */
    public function clearPkValue()
    {
        $this->_pkValue = 0;
    }    
    
    /**
     * Remove the row from database if AR instance has been populated with this row
     * Ex.: $post = PostModel::model()->findByPk(10);
     *      $post->delete();
     * @return boolean
	 */
    public function delete()
    {
		if($this->beforeDelete($this->_pkValue)){
            if(!empty($this->_pkValue) && $this->deleteByPk($this->_pkValue)){
                return true;                
            }
        }else{
            CDebug::AddMessage('errors', 'before-delete', A::t('core', 'AR before delete operation on table: {table}', array('{table}'=>$this->_table)));
        }
        return false;
    }

    /**
     * Remove the rows matching the specified condition and primary key(s)
     * Ex.: deleteByPk(10, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
     * @param string $pk
     * @param mixed $conditions
     * @param array $params 
     * @return boolean
	 */
    public function deleteByPk($pk, $conditions = '', $params = '')
    {
		if($this->beforeDelete($pk)){
            if(is_array($conditions)){
                $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            }else{
                $where = $conditions;
            }
            $whereClause = !empty($where) ? ' WHERE '.$where : '';
    
            $result = $this->db->delete($this->_table, $this->_primaryKey.' = '.(int)$pk.$whereClause, $params);
            if($result){
                $this->afterDelete($pk);
                return true;                
            }
        }else{
            CDebug::AddMessage('errors', 'before-delete', A::t('core', 'AR before delete operation on table: {table}', array('{table}'=>$this->_table)));
        }
        return false;
    }
    
    /**
     * Remove the rows matching the specified condition
     * Ex.: deleteAll('postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
     * @param mixed $conditions
     * @param array $params 
     * @return boolean
	 */
    public function deleteAll($conditions = '', $params = '')
    {
		if($this->beforeDelete()){
            if(is_array($conditions)){
                $where = isset($conditions['condition']) ? $conditions['condition'] : '';
            }else{
                $where = $conditions;
            }
            $whereClause = !empty($where) ? ' WHERE '.$where : '';
    
            $result = $this->db->delete($this->_table, $whereClause, $params);
            if($result){
                $this->afterDelete();
                return true;                
            }
        }else{
            CDebug::AddMessage('errors', 'before-delete', A::t('core', 'AR before delete operation on table: {table}', array('{table}'=>$this->_table)));
        }
        return false;
    }
    
    /**
    * This method check if there is at least one row satisfying the specified condition
    * Ex.: exists('postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
    * @param mixed $conditions
    * @param array $params 
    * @return bolean
    */
    public function exists($conditions = '', $params = '')
    {
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
        }else{
            $where = $conditions;
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
    
        $sql = 'SELECT * FROM `'.CConfig::get('db.prefix').$this->_table.'` '.$whereClause.' LIMIT 1';
        $result = $this->db->select($sql, $params);
        return ($result) ? true : false;
    }

	/**
	 * Finds the number of rows satisfying the specified query condition
	 * @param mixed $conditions
	 * @param array $params 
	 * @return integer 
	 */
	public function count($conditions = '', $params = '')
	{
        if(is_array($conditions)){
            $where = isset($conditions['condition']) ? $conditions['condition'] : '';
        }else{
            $where = $conditions;
        }
        $whereClause = !empty($where) ? ' WHERE '.$where : '';
        $relations = $this->getRelations();

        $sql = 'SELECT 
                    COUNT(*) as cnt
                FROM `'.CConfig::get('db.prefix').$this->_table.'`
                    '.$relations['tables'].'
                '.$whereClause.'
                LIMIT 1';
        $result = $this->db->select($sql, $params);
        return (isset($result[0]['cnt'])) ? $result[0]['cnt'] : 0;
    }

	/**
	 * This method is invoked before saving a record (after validation, if any)
	 * You may override this method
	 * @param string $pk
	 * @return boolean
	 */
	protected function beforeSave($pk = '')
	{
        // $pk - key used for saving operation
		return true;
	}
 
	/**
	 * This method is invoked after saving a record successfully
	 * @param string $pk
	 * You may override this method
	 */
	protected function afterSave($pk = '')
	{
        // $pk - key used for saving operation
		// code here
	}

	/**
	 * This method is invoked before deleting a record (after validation, if any)
	 * You may override this method
	 * @param string $pk
	 * @return boolean
	 */
	protected function beforeDelete($pk = '')
	{
        // $pk - key used for deleting operation
		return true;
	}
 
	/**
	 * This method is invoked after deleting a record successfully
	 * @param string $pk
	 * You may override this method
	 */
	protected function afterDelete($pk = '')
	{
        // $pk - key used for deleting operation
		// code here
	}
    
	/**
	 * Prepares custom fields for query
	 * @return string 
	 */
	private function getCustomFields()
	{
        $result = '';
        $fields = $this->customFields();
        if(is_array($fields)){
            foreach($fields as $key => $val){
                $result .= ', '.$key.' as '.$val;
            }
        }
        return $result;
    }

	/**
	 * Add custom fields for query
	 */
	private function addCustomFields()
	{
        $fields = $this->customFields();
        if(is_array($fields)){
            foreach($fields as $key => $val){
                $this->_columns[$val] = '';
                $this->_columnTypes[$val] = 'varchar';
            }
        }
    }

	/**
	 * Remove custom fields for query
	 */
	private function removeCustomFields()
	{
        $fields = $this->customFields();
        if(is_array($fields)){
            foreach($fields as $key => $val){
                unset($this->_columns[$val]);
                unset($this->_columnTypes[$val]);
            }
        }
    }
    
	/**
	 * Prepares relations for query
	 * @return string 
	 */
	private function getRelations()
	{
        $rel = $this->relations();
        $defaultJoinType = self::LEFT_OUTER_JOIN;
        $result = array('fields'=>'', 'tables'=>'', 'fieldsArray'=>array());
        $nl = "\n";        
       
        if(is_array($rel)){
            foreach($rel as $key => $val){
                
                $relationType = isset($val[0]) ? $val[0] : '';
                $relatedTable = isset($val[1]) ? $val[1] : '';
                $relatedTableKey = isset($val[2]) ? $val[2] : '';
                $joinType = (isset($val['joinType']) && in_array($val['joinType'], self::$_joinTypes)) ? $val['joinType'] : $defaultJoinType;
                $condition = isset($val['condition']) ? $val['condition'] : '';
               
                if(
                    $relationType == self::HAS_ONE ||
                    $relationType == self::BELONGS_TO ||
                    $relationType == self::HAS_MANY || 
                    $relationType == self::MANY_MANY
                ){
                    if(isset($val['fields']) && is_array($val['fields'])){
                        foreach($val['fields'] as $field => $fieldAlias){
                            if(is_numeric($field)){
                                $field = $fieldAlias;
                                $fieldAlias = '';
                            } 
                            $result['fields'] .= ', `'.CConfig::get('db.prefix').$relatedTable.'`.'.$field.(!empty($fieldAlias) ? ' as '.$fieldAlias : '');
                            $result['fieldsArray'][] = (!empty($fieldAlias) ? $fieldAlias : $field);
                        }
                    }else{                        
                        $result['fields'] .= ', `'.CConfig::get('db.prefix').$relatedTable.'`.*';    
                    }                
                    $result['tables'] .= $joinType.' `'.CConfig::get('db.prefix').$relatedTable.'` ON `'.CConfig::get('db.prefix').$this->_table.'`.'.$key.' = `'.CConfig::get('db.prefix').$relatedTable.'`.'.$relatedTableKey;
                    $result['tables'] .= (($condition != '') ? ' AND '.$condition : '').$nl;
                }
            }            
        }
        
        return $result;
    }
    
}