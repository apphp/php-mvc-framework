<?php
/**
 * CRecordEntity base class for classes that represent a single database row.
 * It implements the Record Entity design pattern.
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2021 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ---------------            ---------------               ---------------
 * __construct
 * __set
 * __get
 * __isset
 * __unset
 * set
 * get
 * primaryKey
 * getPrimaryKey
 * setPrimaryKey
 * columns
 * allowedColumns
 * setFillable
 * setGuarded
 * fillFromArray
 *
 */

abstract class CRecordEntity
{
    /** @var */
    protected $_columns = [];
    /** @var */
    protected $_primaryKey = '';
    /** @var */
    protected $_pkValue = 0;

    /** @var fillable fields */
    protected $_fillable = [];
    /** @var guarded fields */
    protected $_guarded = [];

    /**
     * Class constructor
     *
     * @param  int  $pkVal
     */
    public function __construct($pkVal = 0)
    {
        if ( ! empty($pkVal)) {
            $this->_pkValue = $pkVal;
        }
    }

    /**
     * Setter
     *
     * @param  string  $index
     * @param  mixed  $value
     *
     * @return void
     */
    public function __set($index, $value)
    {
        $this->_columns[$index] = $value;
    }

    /**
     * Getter
     *
     * @param  string  $index
     *
     * @return string
     */
    public function __get($index)
    {
        if (array_key_exists($index, $this->_columns)) {
            return $this->_columns[$index];
        } else {
            CDebug::AddMessage(
                'errors',
                'wrong_column'.$index,
                A::t(
                    'core',
                    'Wrong column name: {index} in table {table}',
                    ['{index}' => $index, '{table}' => __CLASS__]
                )
            );

            return '';
        }
    }

    /**
     * Checks if record entity property exists
     *
     * @param  string  $index
     *
     * @return bool
     */
    public function __isset($index)
    {
        return array_key_exists($index, $this->_columns) ? true : false;
    }

    /**
     * Sets a record entity property to be null
     *
     * @param  string  $index
     *
     * @return void
     */
    public function __unset($index)
    {
        if (array_key_exists($index, $this->_columns)) {
            unset($this->_columns[$index]);
        }
    }

    /**
     * Setter
     *
     * @param  string  $index
     * @param  mixed  $value
     *
     * @return void
     */
    public function set($index, $value)
    {
        $this->_columns[$index] = $value;
    }

    /**
     * Getter
     *
     * @param  string  $index
     *
     * @return string
     */
    public function get($index)
    {
        if (array_key_exists($index, $this->_columns)) {
            return $this->_columns[$index];
        } else {
            CDebug::AddMessage(
                'errors',
                'wrong_column'.$index,
                A::t(
                    'core',
                    'Wrong column name: {index} in table {table}',
                    ['{index}' => $index, '{table}' => $this->_table]
                )
            );

            return '';
        }
    }

    /**
     * Returns the primary key of the associated database table
     *
     * @return string
     */
    public function primaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * Returns the primary key value
     *
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->_pkValue;
    }

    /**
     * Returns the primary key value
     *
     * @param  int  $pkVal
     */
    protected function setPrimaryKey($pkVal = 0)
    {
        if ( ! empty($pkVal)) {
            $this->_pkValue = $pkVal;
        }
    }

    /**
     * Return all columns
     *
     * @param  bool  $allowFilters  Return only allowed fields
     *
     * @return array
     */
    public function columns($allowFilters = false)
    {
        $columns = $this->_columns;

        if ($allowFilters) {
            // Validate fillable fields, left only allowed fields
            if (is_array($this->_fillable) && ! empty($this->_fillable)) {
                $columns = array_intersect_key($columns, array_flip($this->_fillable));
            }

            // Validate guarded fields, exclude guarded fields
            if (is_array($this->_guarded) && ! empty($this->_guarded)) {
                $columns = array_diff_key($columns, array_flip($this->_guarded));
            }
        }

        return $columns;
    }

    /**
     * Return all allowed columns
     *
     * @return array
     */
    public function allowedColumns()
    {
        return $this->columns(true);
    }

    /**
     * Set fillable fields
     *
     * @param  array  $fields
     *
     * @return void
     */
    public function setFillable($fields = [])
    {
        if (is_array($fields)) {
            $this->_fillable = [];
            foreach ($fields as $field) {
                $this->_fillable[] = $field;
            }
        }
    }

    /**
     * Set guarded fields
     *
     * @param  array  $fields
     *
     * @return void
     */
    public function setGuarded($fields = [])
    {
        if (is_array($fields)) {
            $this->_guarded = [];
            foreach ($fields as $field) {
                $this->_guarded[] = $field;
            }
        }
    }

    /**
     * Fills data from array
     *
     * @param  array|null  $record
     *
     * @return bool
     */
    public function fillFromArray($record = null)
    {
        // Copy data to CRecordEntity
        if ( ! is_null($record) && is_array($record)) {
            foreach ($record as $key => $val) {
                $this->$key = $val;
            }
        }

        return true;
    }
}