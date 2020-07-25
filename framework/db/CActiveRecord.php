<?php
/**
 * CActiveRecord base class for classes that represent relational data.
 * It implements the Active Record design pattern.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * NOTES:
 *    if(isset($this->_columns[$index])){...}
 *    Doesn't work if array value is NULL - used in MySQL >= 5.7
 *    Replaced with if(array_key_exists($index, $this->_columns)){...}
 *
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ---------------            ---------------                ---------------
 * __construct              _relations                  _parentModel (static)
 * __set                    _customFields               _createObjectFromTable
 * __get                    _encryptedFields            _getRelations
 * __isset                  _beforeSave                 _getCustomFields
 * __unset                  _afterSave                  _addCustomFields
 * __callStatic             _beforeDelete               _removeCustomFields
 *                          _afterDelete                _prepareLimit
 * init (static)                                        _tableName
 * set                                                  _isEncryptedField
 * get                                                  _getEncryptedFields
 * resultArray                                          _getEncryptedField
 * allowedColumns
 * isColumnExists
 * setSpecialField
 * getSpecialField
 * getError
 * getErrorMessage
 * lastQuery
 * primaryKey
 * getPrimaryKey
 * getTableName
 * getFieldsAsArray
 * isNewRecord
 * getTranslations
 * saveTranslations
 *
 * chunk
 *
 * find
 * findByPk
 * findByAttributes
 * findAll
 * findPk
 *
 * create
 * update
 * save
 * clearPkValue
 * reset
 *
 * updateByPk
 * updateAll
 *
 * delete
 * deleteByPk
 * deleteAll
 *
 * distinct
 * refresh
 * exists
 * count
 * max
 * min
 * sum
 *
 *
 */

class CActiveRecord extends CModel
{
	/** @var object */
	private static $_instance;
	/** @var string */
	private static $_className;
	/** @var Database */
	protected $_db;
	/**    @var */
	protected $_dbDriver = '';
	/**    @var */
	protected $_dbPrefix = '';
	/**    @var boolean */
	protected $_error;
	/**    @var string */
	protected $_errorMessage;
	
	/**    @var string */
	protected $_table = '';
	/**    @var string */
	protected $_tableTranslation = '';
	/**    @var */
	protected $_columns = array();
	/**    @var used to store fields from $_POST or another tables */
	protected $_specialFields = array();
	/** 	@var allowed fields */
	protected $_fillable = array();
	/** 	@var guarded fields */
	protected $_guarded = array();
	/**    @var char */
	private $_backQuote = '`';
	
	/* class name => model */
	private static $_models = array();
	
	/**    @var */
	private $_columnTypes = array();
	/**    @var */
	private $_pkValue = 0;
	/**    @var */
	private $_primaryKey;
	/**    @var */
	private $_isNewRecord = false;
	
	/**    @var */
	private static $_joinTypes = array(
		'INNER JOIN',
		'OUTER JOIN',
		'LEFT JOIN',
		'LEFT OUTER JOIN',
		'RIGHT JOIN',
		'RIGHT OUTER JOIN',
		'JOIN',
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
	 * @param bool $createObject
	 */
	public function __construct($createObject = true)
	{
		$this->_db = CDatabase::init();
		
		if ($createObject && !empty($this->_table)) {
			$this->_createObjectFromTable();
			$this->_pkValue = 0;
		}
		
		$this->_dbDriver = CConfig::get('db.driver');
		$this->_dbPrefix = CConfig::get('db.prefix');
		
		$this->_error = CDatabase::getError();
		$this->_errorMessage = CDatabase::getErrorMessage();
		
		// Set back quote according to database driver
		if (preg_match('/mssql|sqlsrv/i', $this->_dbDriver)) {
			$this->_backQuote = '';
		}
	}
	
	/**
	 * Setter
	 * @param string $index
	 * @param mixed $value
	 * @return void
	 */
	public function __set($index, $value)
	{
		$this->_columns[$index] = $value;
	}
	
	/**
	 * Getter
	 * @param string $index
	 * @return string
	 */
	public function __get($index)
	{
		if (array_key_exists($index, $this->_columns)) {
			return $this->_columns[$index];
		} else {
			CDebug::AddMessage('errors', 'wrong_column' . $index, A::t('core', 'Wrong column name: {index} in table {table}', array('{index}' => $index, '{table}' => $this->_table)));
			return '';
		}
	}

    /**
     * Checks if active record property exists
     * @param string $index
     * @return bool
     */
    public function __isset($index)
    {
        return array_key_exists($index, $this->_columns) ? true : false;
    }

	/**
	 * Sets a active record property to be null
	 * @param string $index
	 * @return void
	 */
	public function __unset($index)
	{
		if (array_key_exists($index, $this->_columns)) {
			unset($this->_columns[$index]);
		}
	}
	
	/**
	 * Triggered when invoking inaccessible methods in an object context
	 * We use this method to avoid calling model($className = __CLASS__) in derived class
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		if (strtolower($method) == 'model') {
			if (count($args) == 1) {
				return self::_parentModel($args[0]);
			}
		}
	}
	
	/**
	 * Initializes the database class
	 * @param array $params
	 */
	public static function init($params = array())
	{
		if (self::$_instance == null) self::$_instance = new self($params);
		return self::$_instance;
	}
	
	/**
	 * Setter
	 * @param string $index
	 * @param mixed $value
	 * @return void
	 */
	public function set($index, $value)
	{
		$this->_columns[$index] = $value;
	}
	
	/**
	 * Getter
	 * @param string $index
	 * @return string
	 */
	public function get($index)
	{
		if (array_key_exists($index, $this->_columns)) {
			return $this->_columns[$index];
		} else {
			CDebug::AddMessage('errors', 'wrong_column' . $index, A::t('core', 'Wrong column name: {index} in table {table}', array('{index}' => $index, '{table}' => $this->_table)));
			return '';
		}
	}
	
	/**
	 * Convert current object to array
	 * @param bool $allowFilters	Return only allowed fields
	 * @return array
	 */
	public function resultArray($allowFilters = false)
	{
		if (is_object($this)) {
			$columns = $this->_columns;
			
			if ($allowFilters) {
				// Validate fillable fields, left only allowed fields
				if (is_array($this->_fillable) && !empty($this->_fillable)) {
					$columns = array_intersect_key($columns, array_flip($this->_fillable));
				}
				
				// Validate guarded fields, exclude guarded fields
				if (is_array($this->_guarded) && !empty($this->_guarded)) {
					$columns = array_diff_key($columns, array_flip($this->_guarded));
				}
			}
			
			return $columns;
		}
	}
	
	/**
	 * Return all allowed columns
	 * @return array
	 */
	public function allowedColumns()
	{
		return $this->resultArray(true);
	}
	
	/**
	 * Checks if a given column exists
	 * @param string $index
	 * @return bool
	 */
	public function isColumnExists($index)
	{
		return (array_key_exists($index, $this->_columns)) ? true : false;
	}
	
	/**
	 * Setter for special fields
	 * @param string $index
	 * @param mixed $value
	 */
	public function setSpecialField($index, $value)
	{
		$this->_specialFields[$index] = $value;
	}
	
	/**
	 * Getter
	 * @param string $index
	 */
	public function getSpecialField($index)
	{
		return isset($this->_specialFields[$index]) ? $this->_specialFields[$index] : '';
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
	 * Returns last query
	 * @return string
	 */
	public function lastQuery()
	{
		return $this->_db->lastQuery();
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
	 * @param bool $usePrefix
	 * @return string
	 */
	public function getTableName($usePrefix = false)
	{
		return ($usePrefix ? $this->_dbPrefix : '') . $this->_table;
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
		
		if ($this->_tableTranslation == '') {
			CDebug::AddMessage('errors', 'get-translations', A::t('core', 'Property "{class}.{name}" is not defined.', array('{class}' => self::$_className, '{name}' => '_tableTranslation')));
		}
		
		$result = $this->_db->select(
			'SELECT * FROM ' . $this->_tableName($this->_tableTranslation) . ' WHERE ' . $key . ' = :' . $key,
			array(':' . $key => $value)
		);
		foreach ($result as $res) {
			foreach ($fields as $field) {
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
		
		foreach ($fields as $lang => $langInfo) {
			foreach ($langInfo as $langField => $langFieldValue) {
				$paramsTranslation[$langField] = $langFieldValue;
			}
			if ($this->isNewRecord()) {
				$paramsTranslation[$key] = $value;
				$paramsTranslation['language_code'] = $lang;
				$this->_db->insert($this->_tableTranslation, $paramsTranslation);
			} else {
				$this->_db->update($this->_tableTranslation, $paramsTranslation, $key . '=:key AND language_code=:language_code', array(':key' => $value, ':language_code' => $lang));
			}
		}
	}
	
	
	/*****************************************************************
	 *  ACTIVE RECORD METHODS
	 *****************************************************************/
	/**
	 * Returns the static model of the specified AR class
	 * @param string $className
	 *
	 * EVERY derived AR class must define model() method in the following way,
	 * <pre>
	 * public static function model()
	 * {
	 *     return parent::model(__CLASS__);
	 * }
	 * </pre>
	 */
	private static function _parentModel($className = __CLASS__)
	{
		self::$_className = $className;
		
		if (isset(self::$_models[$className])) {
			return self::$_models[$className];
		} else {
			return self::$_models[$className] = new $className(null);
		}
	}
	
	/**
	 * Create empty object from table
	 * @return bool
	 */
	private function _createObjectFromTable()
	{
		if (is_null($this->_table)) {
			return false;
		}
		
		$cols = $this->_db->showColumns($this->_table);
		if (!is_array($cols)) return false;
		
		switch ($this->_dbDriver) {
			case 'mssql':
			case 'sqlsrv':
				// Handle MSSQL or SQLSRV drivers
				$constraintStatement = "SELECT KU.TABLE_NAME, KU.COLUMN_NAME, KU.ORDINAL_POSITION, KU.CONSTRAINT_NAME
						FROM [INFORMATION_SCHEMA].[TABLE_CONSTRAINTS] TC, [INFORMATION_SCHEMA].[KEY_COLUMN_USAGE] KU    
						WHERE TC.TABLE_CATALOG = KU.TABLE_CATALOG AND 
						TC.CONSTRAINT_SCHEMA = KU.CONSTRAINT_SCHEMA AND
						TC.CONSTRAINT_NAME = KU.CONSTRAINT_NAME  AND
						TC.CONSTRAINT_TYPE='PRIMARY KEY' AND 
						LOWER(KU.TABLE_NAME)='" . strtolower($this->_table) . "' ";
				
				$primaryInfos = $this->_db->select($constraintStatement);
				
				$isPrimaryKey = false;
				foreach ($cols as $array) {
					// If NULL is allowed and NULL is default value, use null otherwise insert default value $array[4]
					// In mssql, sqlsrv the results contain are COLUMN_NAME, data_type, character_maximum_length
					
					$columnField = $array[0];
					$columnType = $array[1];
					$columnNullable = $array[2];
					$columnKey = $array[3];
					$columnDefault = $array[4];
					$columnExtra = $array[5];
					
					$isPrimaryKey = ($columnKey == 'PRI') ? true : false;
					if (!empty($primaryInfos)) {
						$found = false;
						foreach ($primaryInfos as $info) {
							if ((!$found) && (strcasecmp($info['COLUMN_NAME'], $columnField) == 0)) {
								$found = true;
								$isPrimaryKey = true;
							}
						}
					}
					
					if ($columnNullable === 'YES') {
						$this->_columns[$columnField] = null;
					} else {
						$this->_columns[$columnField] = ($columnDefault != '') ? $columnDefault : '';
					}
					
					$arrayParts = explode('(', $columnType);
					$this->_columnTypes[$columnField] = array_shift($arrayParts);
					if ($isPrimaryKey) {
						$this->_primaryKey = $columnField;
					}
				}
				break;
			
			default:
				// Handle all other db drivers
				// In mysql the results are Field, Type, Null, Key, Default, Extra
				foreach ($cols as $array) {
					// If NULL is allowed and NULL is default value, use null
					// otherwise insert default value $array[4]
					if ($array[2] === 'YES') {
						$this->_columns[$array[0]] = null;
					} else {
						$this->_columns[$array[0]] = ($array[4] != '') ? $array[4] : '';
					}
					
					$arrayParts = explode('(', $array[1]);
					$this->_columnTypes[$array[0]] = array_shift($arrayParts);
					if ($array[3] == 'PRI') {
						$this->_primaryKey = $array[0];
					}
				}
				break;
		}
		
		$this->_addCustomFields();
		if ($this->_primaryKey == '') $this->_primaryKey = 'id';
		
		return true;
	}

    /**
     * Split AR result into parts (chunks)
     * @param  int  $size
     * @param  null  $callback
     */
    public function chunk(int $size, callable $callback = null)
    {
        if (is_int($size) && $size > 0 && !empty($callback)) {
            $from = 0;
//            echo('limit'."$from, $size");
            while ($result = $this->findAll(array('limit'=>"$from, $size"))){
                $callback($result);
                $from += $size;
//                echo('limit'."$from, $size");
//                if ($from > 10){
//                    return;
//                }
            }
        } else {
            CDebug::AddMessage('errors', 'chunk', A::t('core', 'Wrong params for chunk: {size} or callback method is callable.', array('{size}' => $size)));
        }

        return null;
    }
	
	/**
	 * This method queries your database to find first related object
	 * Ex.: find('postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: find(array('condition'=>'postID = :postID AND isActive = :isActive', 'order|orderBy'=>'id DESC'), 'params'=>array(':postID'=>10, ':isActive'=>1));
	 * @param mixed $conditions
	 * @param array $params
	 * @param bool|string $cacheId
	 * @return object
	 */
	public function find($conditions = '', $params = array(), $cacheId = false)
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			if (isset($conditions['order'])) {
				$order = isset($conditions['order']) ? $conditions['order'] : '';
			} elseif (isset($conditions['orderBy'])) {
				$order = isset($conditions['orderBy']) ? $conditions['orderBy'] : '';
			}
		} else {
			$where = $conditions;
			$order = '';
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$orderBy = !empty($order) ? ' ORDER BY ' . $order : '';
		$relations = $this->_getRelations();
		$customFields = $this->_getCustomFields();
		$encryptedField = $this->_getEncryptedFields();
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
					' . $limits['before'] . '
					' . $this->_tableName() . '.*
					' . $relations['fields'] . '
					' . $customFields . '
					' . $encryptedField . '
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
				' . $whereClause . '
				' . $orderBy . '
				' . $limits['after'];
		
		$result = $this->_db->select($sql, $params, 'fetchAll', PDO::FETCH_ASSOC, $cacheId);
		if (isset($result[0]) && is_array($result[0])) {
			foreach ($result[0] as $key => $val) {
				$this->$key = $val;
				if ($key == $this->_primaryKey) $this->_pkValue = $val;
			}
			return $this;
		} else {
			return null;
		}
	}
	
	/**
	 * This method queries your database to find related objects by PK
	 * Ex.: findByPk($pk, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: findByPk($pk, array('condition'=>'postID = :postID AND isActive = :isActive', 'order|orderBy'=>'id DESC'), 'params'=>array(':postID'=>10, ':isActive'=>1));
	 * @param string $pk
	 * @param mixed $conditions
	 * @param array $params
	 * @param bool|string $cacheId
	 * @return object
	 */
	public function findByPk($pk, $conditions = '', $params = array(), $cacheId = false)
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			if (isset($conditions['order'])) {
				$order = isset($conditions['order']) ? $conditions['order'] : '';
			} elseif (isset($conditions['orderBy'])) {
				$order = isset($conditions['orderBy']) ? $conditions['orderBy'] : '';
			}
		} else {
			$where = $conditions;
			$order = '';
		}
		
		$whereClause = !empty($where) ? ' AND (' . $where . ')' : '';
		$orderBy = !empty($order) ? ' ORDER BY ' . $order : '';
		$relations = $this->_getRelations();
		$customFields = $this->_getCustomFields();
		$encryptedField = $this->_getEncryptedFields();
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
					' . $limits['before'] . '
					' . $this->_tableName() . '.*
					' . $relations['fields'] . '
					' . $customFields . '
					' . $encryptedField . '
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
				WHERE ' . $this->_tableName() . '.' . $this->_primaryKey . ' = ' . (int)$pk . '
					' . $whereClause . '
				' . $orderBy . '
				' . $limits['after'];
		
		$result = $this->_db->select($sql, $params, 'fetchAll', PDO::FETCH_ASSOC, $cacheId);
		if (isset($result[0]) && is_array($result[0])) {
			foreach ($result[0] as $key => $val) {
				$this->$key = $val;
			}
			$this->_pkValue = $pk;
			return $this;
		} else {
			return null;
		}
	}
	
	/**
	 * This method queries your database to find related objects by attributes
	 * Ex.: findByAttributes($attributes, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: findByAttributes($attributes, array('condition'=>'postID = :postID AND isActive = :isActive', 'order|orderBy'=>'id DESC', 'limit'=>'0, 10'), 'params'=>array(':postID'=>10, ':isActive'=>1));
	 * Ex.: $attributes = array('first_name'=>$firstName, 'last_name'=>$lastName);
	 * @param array $attributes
	 * @param mixed $conditions
	 * @param array $params
	 */
	public function findByAttributes($attributes, $conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			if (isset($conditions['order'])) {
				$order = isset($conditions['order']) ? $conditions['order'] : '';
			} elseif (isset($conditions['orderBy'])) {
				$order = isset($conditions['orderBy']) ? $conditions['orderBy'] : '';
			}
			$limit = isset($conditions['limit']) ? $conditions['limit'] : '';
		} else {
			$where = $conditions;
			$order = '';
			$limit = '';
		}
		
		$whereClause = !empty($where) ? ' AND ' . $where : '';
		$orderBy = !empty($order) ? ' ORDER BY ' . $order : '';
		$limits = $this->_prepareLimit($limit);
		
		$relations = $this->_getRelations();
		$customFields = $this->_getCustomFields();
		$encryptedField = $this->_getEncryptedFields();
		
		$attributes_clause = '';
		foreach ($attributes as $key => $value) {
			$attributes_clause .= ' AND ' . $key . " = '" . $value . "'";
		}
		
		$sql = 'SELECT
					' . $limits['before'] . '
					' . $this->_tableName() . '.*
					' . $relations['fields'] . '
					' . $customFields . '
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
					' . $encryptedField . '
				WHERE 1 = 1
					' . $attributes_clause . '
					' . $whereClause . '
				' . $orderBy . '
				' . $limits['after'];
		
		return $this->_db->select($sql, $params);
	}
	
	/**
	 * This method queries your database to find all related objects
	 * Ex.: findAll('post_id = :postID AND is_active = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: findAll(array('condition'=>'post_id = :postID AND is_active = :isActive', 'select'=>'', 'group|groupBy'=>'', 'order|orderBy'=>'id DESC', 'limit'=>'0, 10'), array(':postID'=>10, ':isActive'=>1));
	 * Ex.: findAll(CConfig::get('db.prefix').$this->_tableTranslation.'.news_text LIKE :keywords', array(':keywords'=>'%'.$keywords.'%'));
	 * @param mixed $conditions
	 * @param array $params 'select': MAX(date), name or CConfig::get('db.prefix').table.field_name etc. - actually for ONLY_FULL_GROUP_BY mode
	 *                                    'groupBy': table.field_name or field_name
	 * @param bool|string $cacheId
	 * @param int $fetchMode
	 * @return array
	 */
	public function findAll($conditions = '', $params = array(), $cacheId = false, $fetchMode = PDO::FETCH_ASSOC)
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			if (isset($conditions['group'])) {
				$group = isset($conditions['group']) ? $conditions['group'] : '';
			} elseif (isset($conditions['groupBy'])) {
				$group = isset($conditions['groupBy']) ? $conditions['groupBy'] : '';
			}
			if (isset($conditions['order'])) {
				$order = isset($conditions['order']) ? $conditions['order'] : '';
			} elseif (isset($conditions['orderBy'])) {
				$order = isset($conditions['orderBy']) ? $conditions['orderBy'] : '';
			}
			$limit = isset($conditions['limit']) ? $conditions['limit'] : '';
			$select = isset($conditions['select']) ? $conditions['select'] : '';
		} else {
			$where = $conditions;
			$group = '';
			$order = '';
			$limit = '';
			$select = '';
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$groupBy = !empty($group) ? ' GROUP BY ' . $group : '';
		$orderBy = !empty($order) ? ' ORDER BY ' . $order : '';
		$limits = $this->_prepareLimit($limit);
		$selectList = !empty($select) ? $select : $this->_tableName() . '.*';
		
		$relations = $this->_getRelations();
		$customFields = $this->_getCustomFields();
		$encryptedField = $this->_getEncryptedFields();
		
		$sql = 'SELECT
					' . $limits['before'] . '
					' . $selectList . '
					' . $relations['fields'] . '
					' . $customFields . '
					' . $encryptedField . '
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
				' . $whereClause . '
				' . $groupBy . '
				' . $orderBy . '
				' . $limits['after'];
		
		return $this->_db->select($sql, $params, 'fetchAll', $fetchMode, $cacheId);
	}
	
	/**
	 * This method queries your database to find first related record primary key
	 * Ex.: findPk('postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * @param mixed $conditions
	 * @param array $params
	 * @return int
	 */
	public function findPk($conditions = '', $params = array())
	{
		if ($result = $this->find($conditions, $params)) {
			$key = $this->_primaryKey;
			return $result->$key;
		}
		
		return '';
	}
	
	/**
	 * Create new record
	 * @param array $data
	 * @param bool $preOperations
	 * @return bool
	 */
	public function create($data = array(), $preOperations = true)
	{
		$allowOperation = true;
		if ($preOperations) {
			if (!$this->_beforeSave($this->_pkValue)) {
				$allowOperation = false;
				CDebug::AddMessage('errors', 'before-save', A::t('core', 'AR before operation on table: {table}', array('{table}' => $this->_table)));
			}
		}
		
		if ($allowOperation) {
			$result = $this->_db->insert($this->_table, $data);
			$this->_isNewRecord = true;
			// Save last inset ID
			$this->_pkValue = (int)$result;
			
			if ($result) {
				if ($preOperations) {
					$this->_afterSave($this->_pkValue);
				}
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Update existing record
	 * @param int $id
	 * @param array $data
	 * @param bool $preOperations
	 * @param bool $forceSave
	 * @return boolean
	 */
	public function update($id, $data = array(), $preOperations = true, $forceSave = false)
	{
		$allowOperation = true;
		if ($preOperations) {
			if (!$this->_beforeSave($id)) {
				$allowOperation = false;
				CDebug::AddMessage('errors', 'before-save', A::t('core', 'AR before operation on table: {table}', array('{table}' => $this->_table)));
			}
		}
		
		if ($allowOperation) {
			$result = $this->_db->update($this->_table, $data, $this->_primaryKey . ' = :primary_key', array(':primary_key' => (int)$id), $forceSave);
			$this->_isNewRecord = false;
			
			if ($result) {
				if ($preOperations) {
					$this->_afterSave($id);
				}
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Save data
	 * @param CRecordEntity $entity
	 * @param bool $forceSave
	 * @return boolean
	 */
	public function save($entity = null, $forceSave = false)
	{
		$data = array();
		$this->_removeCustomFields();
		
		if (!is_null($entity) && ($entity instanceof CRecordEntity)) {
			// ---------------------------------------
			//  Handle Entity
			// ---------------------------------------
			$columns = $entity->allowedColumns();
			$primaryKey = $entity->primaryKey();
			$pkValue = $entity->getPrimaryKey();
			
			foreach ($columns as $column => $val) {
				if ($column != 'id' && $column != $primaryKey) {
					$data[$column] = $entity->$column;
				}
			}
			
			if ($pkValue > 0) {
				$result = $this->_db->update($this->_table, $data, $primaryKey . ' = :primary_key', array(':primary_key' => (int)$pkValue), $forceSave);
			} else {
				$result = $this->_db->insert($this->_table, $data, $forceSave);
				// Save last inset ID
				$pkValue = (int)$result;
			}
			
			if ($result) {
				$this->_afterSave($pkValue);
				return true;
			}
		} else {
			// ---------------------------------------
			//  Handle Model
			// ---------------------------------------
			if ($this->_beforeSave($this->_pkValue)) {
				$columns = $this->allowedColumns();
				foreach ($columns as $column => $val) {
					$relations = $this->_getRelations();
					if ($column != 'id' && $column != $this->_primaryKey && !in_array($column, $relations['fieldsArray'])) {  //  && ($column != 'created_at') && !$NEW)
						//$value = $this->$column;
						//if(array_search($this->_columnTypes[$column], array('int', 'float', 'decimal'))){
						//    $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
						//}
						if ($this->_isEncryptedField($column)) {
							$encryptedField = $this->_getEncryptedField($column);
							$data[$column] = array('param_key' => $encryptedField['encrypt'] . '(' . $column . ',"' . $encryptedField['key'] . '")', 'param_value' => $this->$column);
						} else {
							$data[$column] = $this->$column;
						}
					}
				}
				
				if ($this->_pkValue > 0) {
					$result = $this->_db->update($this->_table, $data, $this->_primaryKey . ' = :primary_key', array(':primary_key' => (int)$this->_pkValue), $forceSave);
					$this->_isNewRecord = false;
				} else {
					$result = $this->_db->insert($this->_table, $data, $forceSave);
					$this->_isNewRecord = true;
					// Save last inset ID
					$this->_pkValue = (int)$result;
				}
				
				if ($result) {
					$this->_afterSave($this->_pkValue);
					return true;
				}
			} else {
				CDebug::AddMessage('errors', 'before-save', A::t('core', 'AR before operation on table: {table}', array('{table}' => $this->_table)));
			}
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
		
		return true;
	}
	
	/**
	 * Reset the object with fields
	 * @return boolean
	 */
	public function reset()
	{
		$this->_columns = array();
		$this->_specialFields = array();
		
		if (!empty($this->_table)) {
			$this->_createObjectFromTable();
			$this->_pkValue = 0;
		}
		
		return true;
	}
	
	/**
	 * Updates records with the specified primary key
	 * See {@link find()} for detailed explanation about $conditions
	 * Ex.: updateByPk($pk, array('name'=>$value), 'postID = 10 AND isActive = 1');
	 * @param string $pk
	 * @param array $data
	 * @param mixed $conditions
	 * @param array $params
	 * @return bool
	 */
	public function updateByPk($pk, $data = array(), $conditions = '', $params = array())
	{
		if ($this->_beforeSave($pk)) {
			if (is_array($conditions)) {
				$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			} else {
				$where = $conditions;
			}
			$whereClause = !empty($where) ? ' AND ' . $where : '';
			$params[':primary_key'] = (int)$pk;
			
			$result = $this->_db->update($this->_table, $data, $this->_primaryKey . ' = :primary_key' . $whereClause, $params);
			if ($result) {
				$this->_afterSave($pk);
				return true;
			} else {
				return false;
			}
		} else {
			CDebug::AddMessage('errors', 'before-update', A::t('core', 'AR before operation on table: {table}', array('{table}' => $this->_table)));
		}
	}
	
	/**
	 * Updates the rows matching the specified condition
	 * Ex.: updateAll(array('name'=>$value), 'postID = 10 AND isActive = 1');
	 * Ex.: updateAll(array('name'=>$value), 'postID = 10 AND isActive = :isActive', array(':isActiv'=>1));
	 * @param array $data
	 * @param mixed $conditions
	 * @param array $params
	 * @return bool
	 */
	public function updateAll($data = array(), $conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
		} else {
			$where = $conditions;
		}
		$whereClause = !empty($where) ? $where : '1';
		
		$result = $this->_db->update($this->_table, $data, $whereClause, $params);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Remove the row from database if AR instance has been populated with this row
	 * Ex.: $post = PostModel::model()->findByPk(10);
	 *      $post->delete();
	 * @return boolean
	 */
	public function delete()
	{
		if (!empty($this->_pkValue) && $this->deleteByPk($this->_pkValue)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Remove the rows matching the specified condition and primary key(s)
	 * Ex.: deleteByPk(10, 'postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * @param string $pk
	 * @param mixed $conditions
	 * @param array $params
	 * @return boolean
	 */
	public function deleteByPk($pk, $conditions = '', $params = array())
	{
		if ($this->_beforeDelete($pk)) {
			if (is_array($conditions)) {
				$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			} else {
				$where = $conditions;
			}
			$whereClause = !empty($where) ? ' WHERE ' . $where : '';
			$params[':primary_key'] = (int)$pk;
			
			$result = $this->_db->delete($this->_table, $this->_primaryKey . ' = :primary_key' . $whereClause, $params);
			if ($result) {
				$this->_afterDelete($pk);
				return true;
			}
		} else {
			CDebug::AddMessage('errors', 'before-delete', A::t('core', 'AR before delete operation on table: {table}', array('{table}' => $this->_table)));
		}
		
		return false;
	}
	
	/**
	 * Remove the rows matching the specified condition
	 * Ex.: deleteAll('postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: deleteAll(array('condition'=>'postID = :postID AND isActive = :isActive'), array(':postID'=>10, ':isActive'=>1));
	 * @param mixed $conditions
	 * @param array $params
	 * @return boolean
	 */
	public function deleteAll($conditions = '', $params = array())
	{
		if ($this->_beforeDelete()) {
			if (is_array($conditions)) {
				$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			} else {
				$where = $conditions;
			}
			$whereClause = !empty($where) ? ' WHERE ' . $where : '';
			
			$result = $this->_db->delete($this->_table, $whereClause, $params);
			if ($result) {
				$this->_afterDelete();
				return true;
			}
		} else {
			CDebug::AddMessage('errors', 'before-delete', A::t('core', 'AR before delete operation on table: {table}', array('{table}' => $this->_table)));
		}
		
		return false;
	}
	
	/**
	 * This method selects distinct value
	 * @param string $field
	 * @return array
	 */
	public function distinct($field = '')
	{
		return $this->findAll(array('group' => $this->_tableName() . '.' . $field));
	}
	
	/**
	 * This method reloads model data according to the current primary key
	 * @return object
	 */
	public function refresh()
	{
		return $this->findByPk($this->_pkValue);
	}
	
	/**
	 * This method check if there is at least one row satisfying the specified condition
	 * Ex.: exists('postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * @param mixed $conditions
	 * @param array $params
	 * @return bolean
	 */
	public function exists($conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
		} else {
			$where = $conditions;
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
				' . $limits['before'] . '
				' . $this->_tableName() . '.*
			FROM ' . $this->_tableName() . '
			' . $whereClause . '
			' . $limits['after'];
		
		$result = $this->_db->select($sql, $params);
		
		return ($result) ? true : false;
	}
	
	/**
	 * Finds the number of rows satisfying the specified query condition
	 * Ex.: count('postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * Ex.: count(array('condition'=>'post_id = :postID AND is_active = :isActive', 'select'=>'', 'count'=>'*', 'group|groupBy'=>'', 'order|orderBy'=>'', 'allRows'=>false), array(':postID'=>10, ':isActive'=>1));
	 * @param mixed $conditions
	 * @param array $params
	 * @return integer
	 */
	public function count($conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
			if (isset($conditions['group'])) {
				$group = isset($conditions['group']) ? $conditions['group'] : '';
			} elseif (isset($conditions['groupBy'])) {
				$group = isset($conditions['groupBy']) ? $conditions['groupBy'] : '';
			}
			if (!empty($group)) {
				if (isset($conditions['order'])) {
					$order = isset($conditions['order']) ? $conditions['order'] : '';
				} elseif (isset($conditions['orderBy'])) {
					$order = isset($conditions['orderBy']) ? $conditions['orderBy'] : '';
				}
			}
			$count = isset($conditions['count']) ? $conditions['count'] : '*';
			$select = isset($conditions['select']) ? $conditions['select'] : '';
			$allRows = isset($conditions['allRows']) ? (bool)$conditions['allRows'] : false;
		} else {
			$where = $conditions;
			$group = '';
			$order = '';
			$count = '*';
			$select = '';
			$allRows = false;
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$groupBy = !empty($group) ? ' GROUP BY ' . $group : '';
		$orderBy = !empty($order) ? ' ORDER BY ' . $order : '';
		$limits = $this->_prepareLimit(($allRows ? '' : '1'));
		$relations = $this->_getRelations();
		
		$sql = 'SELECT
					' . $limits['before'] . '
					COUNT(' . $count . ') as cnt
					' . ($select ? ', ' . $select : '') . '
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
				' . $whereClause . '
				' . $groupBy . '
				' . $orderBy . '
				' . $limits['after'];
		
		$result = $this->_db->select($sql, $params);
		
		if ($allRows) {
			return (isset($result)) ? $result : null;
		} else {
			return (isset($result[0]['cnt'])) ? $result[0]['cnt'] : 0;
		}
	}
	
	/**
	 * Finds a maximum value of the specified column
	 * Ex.: max('id', 'postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * @param string $column
	 * @param mixed $conditions
	 * @param array $params
	 * @return integer
	 */
	public function max($column = '', $conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
		} else {
			$where = $conditions;
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$column = !empty($column) ? $this->_tableName() . '.' . $column : $this->_primaryKey;
		$relations = $this->_getRelations();
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
					' . $limits['before'] . '
					MAX(' . $column . ') as column_max
				FROM ' . $this->_tableName() . '
					' . $relations['tables'] . '
				' . $whereClause . '
				' . $limits['after'];
		
		$result = $this->_db->select($sql, $params);
		
		return (isset($result[0]['column_max'])) ? $result[0]['column_max'] : 0;
	}
	
	/**
	 * Finds a minimum value of the specified column
	 * Ex.: min('id', 'postID = :postID AND isActive = :isActive', array(':postID'=>10, 'isActive'=>1));
	 * @param string $column
	 * @param mixed $conditions
	 * @param array $params
	 * @return integer
	 */
	public function min($column = '', $conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
		} else {
			$where = $conditions;
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$column = !empty($column) ? $this->_tableName() . '.' . $column : $this->_primaryKey;
		$relations = $this->_getRelations();
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
				' . $limits['before'] . '
				MIN(' . $column . ') as column_min
			FROM ' . $this->_tableName() . '
				' . $relations['tables'] . '
			' . $whereClause . '
			' . $limits['after'];
		
		$result = $this->_db->select($sql, $params);
		
		return (isset($result[0]['column_min'])) ? $result[0]['column_min'] : 0;
	}
	
	/**
	 * Finds a sum value of the specified column
	 * Ex.: sum('id', 'postID = :postID AND isActive = :isActive', array(':postID'=>10, ':isActive'=>1));
	 * @param string $column
	 * @param mixed $conditions
	 * @param array $params
	 * @return integer
	 */
	public function sum($column = '', $conditions = '', $params = array())
	{
		if (is_array($conditions)) {
			$where = isset($conditions['condition']) ? $conditions['condition'] : '';
		} else {
			$where = $conditions;
		}
		
		$whereClause = !empty($where) ? ' WHERE ' . $where : '';
		$column = !empty($column) ? $column : '';
		$relations = $this->_getRelations();
		$limits = $this->_prepareLimit('1');
		
		$sql = 'SELECT
				' . $limits['before'] . '
				SUM(' . $column . ') as column_sum
			FROM ' . $this->_tableName() . '
				' . $relations['tables'] . '
			' . $whereClause . '
			' . $limits['after'];
		
		$result = $this->_db->select($sql, $params);
		
		return (isset($result[0]['column_sum'])) ? $result[0]['column_sum'] : 0;
	}
	
	/**
	 * Used to define relations between different tables in database and current $_table
	 * This method should be overridden
	 */
	protected function _relations()
	{
		return array();
	}
	
	/**
	 * Used to define custom fields
	 * This method should be overridden
	 * Usage: 'CONCAT('.CConfig::get('db.prefix').$this->_table.'.last_name, " ", '.CConfig::get('db.prefix').$this->_table.'.first_name)' => 'fullname'
	 *        '(SELECT COUNT(*) FROM '.CConfig::get('db.prefix').$this->_tableTranslation.')' => 'records_count'
	 */
	protected function _customFields()
	{
		return array();
	}
	
	/**
	 * Used to define encrypted fields
	 * This method should be overridden
	 * Usage: 'field_name_1' => array('encrypt'=>'AES_ENCRYPT', 'decrypt'=>'AES_DECRYPT', 'key'=>'encryptKey')
	 *        'field_name_2' => array('encrypt'=>'AES_ENCRYPT', 'decrypt'=>'AES_DECRYPT', 'key'=>CConfig::get('text.encryptKey'))
	 */
	protected function _encryptedFields()
	{
		return array();
	}
	
	/**
	 * This method is invoked before saving a record (after validation, if any)
	 * You may override this method
	 * @param int $pk
	 * @return boolean
	 */
	protected function _beforeSave($pk = 0)
	{
		// $pk - key used for saving operation
		return true;
	}
	
	/**
	 * This method is invoked after saving a record successfully
	 * @param int $pk
	 * You may override this method
	 */
	protected function _afterSave($pk = 0)
	{
		// $pk - key used for saving operation
		// $this->_columns - array of columns, e.g. $this->_columns['is_active']
		// code here
	}
	
	/**
	 * This method is invoked before deleting a record (after validation, if any)
	 * You may override this method
	 * @param int $pk
	 * @return boolean
	 */
	protected function _beforeDelete($pk = 0)
	{
		// $pk - key used for deleting operation
		return true;
	}
	
	/**
	 * This method is invoked after deleting a record successfully
	 * @param int $pk
	 * You may override this method
	 */
	protected function _afterDelete($pk = 0)
	{
		// $pk - key used for deleting operation
		// code here
	}
	
	/**
	 * Prepares custom fields for query
	 * @return string
	 */
	private function _getCustomFields()
	{
		$result = '';
		$fields = $this->_customFields();
		if (is_array($fields)) {
			foreach ($fields as $key => $val) {
				$result .= ', ' . $key . ' as ' . $val;
			}
		}
		
		return $result;
	}
	
	/**
	 * Add custom fields for query
	 */
	private function _addCustomFields()
	{
		$fields = $this->_customFields();
		if (is_array($fields)) {
			foreach ($fields as $key => $val) {
				$this->_columns[$val] = '';
				$this->_columnTypes[$val] = 'varchar';
			}
		}
	}
	
	/**
	 * Remove custom fields for query
	 */
	private function _removeCustomFields()
	{
		$fields = $this->_customFields();
		if (is_array($fields)) {
			foreach ($fields as $key => $val) {
				unset($this->_columns[$val]);
				unset($this->_columnTypes[$val]);
			}
		}
	}
	
	/**
	 * Prepares relations for query
	 * @return string
	 */
	private function _getRelations()
	{
		$result = array('fields' => '', 'tables' => '', 'fieldsArray' => array());
		$rel = $this->_relations();
		if (!is_array($rel)) return $result;
		$defaultJoinType = self::LEFT_OUTER_JOIN;
		$nl = "\n";
		
		foreach ($rel as $key => $val) {
			$key = isset($val['parent_key']) ? $val['parent_key'] : $key;
			$relationType = isset($val[0]) ? $val[0] : '';
			$relatedTable = isset($val[1]) ? $val[1] : '';
			$relatedTableKey = isset($val[2]) ? $val[2] : '';
			$joinType = (isset($val['joinType']) && in_array($val['joinType'], self::$_joinTypes)) ? $val['joinType'] : $defaultJoinType;
			$condition = isset($val['condition']) ? $val['condition'] : '';
			
			if (
				$relationType == self::HAS_ONE ||
				$relationType == self::BELONGS_TO ||
				$relationType == self::HAS_MANY ||
				$relationType == self::MANY_MANY
			) {
				if (isset($val['fields']) && is_array($val['fields'])) {
					foreach ($val['fields'] as $field => $fieldAlias) {
						if (is_numeric($field)) {
							$field = $fieldAlias;
							$fieldAlias = '';
						}
						$result['fields'] .= ', ' . $this->_tableName($relatedTable) . '.' . $field . (!empty($fieldAlias) ? ' as ' . $fieldAlias : '');
						$result['fieldsArray'][] = (!empty($fieldAlias) ? $fieldAlias : $field);
					}
				} else {
					$result['fields'] .= ', ' . $this->_tableName($relatedTable) . '.*';
				}
				$result['tables'] .= $joinType . ' ' . $this->_tableName($relatedTable) . ' ON ' . $this->_tableName() . '.' . $key . ' = ' . $this->_tableName($relatedTable) . '.' . $relatedTableKey;
				$result['tables'] .= (($condition != '') ? ' AND ' . $condition : '') . $nl;
			}
		}
		
		return $result;
	}
	
	/**
	 * Prepare LIMIT clause for SQL statement
	 * @param string $limit
	 * @retun array
	 */
	private function _prepareLimit($limit = '')
	{
		$limits = array('before' => '', 'after' => '');
		
		if (!empty($limit)) {
			if (preg_match('/mssql|sqlsrv/i', $this->_dbDriver)) {
				$limits['before'] = !empty($limit) ? ' TOP ' . $limit : '';
			} else {
				$limits['after'] = !empty($limit) ? ' LIMIT ' . $limit : '';
			}
		}
		
		return $limits;
	}
	
	/**
	 * Escapes table name with backquotes and adds db prefix
	 * Prepares table name for using in SQL statements
	 * @param string $table
	 * @return string
	 */
	private function _tableName($table = '')
	{
		if (empty($table)) {
			$table = $this->_table;
		}
		
		return $this->_backQuote . $this->_dbPrefix . $table . $this->_backQuote;
	}
	
	/**
	 * Checks if a given field is encrypted field
	 * @param string $column
	 * @return bool
	 */
	private function _isEncryptedField($column = '')
	{
		$encryptedFields = $this->_encryptedFields();
		return isset($encryptedFields[$column]) ? true : false;
	}
	
	/**
	 * Prepares encrypted fields for query
	 * @return string
	 */
	private function _getEncryptedFields()
	{
		$result = '';
		$fields = $this->_encryptedFields();
		if (is_array($fields)) {
			foreach ($fields as $key => $val) {
				$encryptedField = $this->_getEncryptedField($key);
				$result .= ', ' . $encryptedField['decrypt'] . '(' . $key . ',"' . $encryptedField['key'] . '") as ' . $key;
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns encrypted field info
	 * @param string $column
	 * @return array
	 */
	private function _getEncryptedField($column = '')
	{
		$encryptedFields = $this->_encryptedFields();
		return isset($encryptedFields[$column]) ? $encryptedFields[$column] : array();
	}
}
