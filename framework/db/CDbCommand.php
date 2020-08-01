<?php
/**
 * CDbCommand core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2020 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * Starting from version 1.0.0, CDbCommand can be used as a query builder
 * <pre>
 * $accounts = CDatabase::init()->createCommand()
 *     ->select('*')
 *     ->from(CConfig::get('db.prefix').'accounts')
 *     ->order('id ASC')
 *     ->queryAll();
 *
 * $account = CDatabase::init()->createCommand()
 *     ->select('id, username, password')
 *     ->from(CConfig::get('db.prefix').'accounts')
 *     ->where('id=:id', [':id'=>1])
 *     ->queryRow();
 * </pre>
 *
 *
 * PUBLIC:                    PROTECTED:                    PRIVATE:
 * ---------------            ---------------                ---------------
 * __construct                                            _queryInternal
 * reset                                                _quotes
 * cancel                                                _processConditions
 * setText                                                _joinInternal
 * getText                                                _applyLimit
 * queryAll
 * queryRow
 * queryScalar
 * queryColumn
 * buildQuery
 * select
 * getSelect
 * selectDistinct
 * from
 * getFrom
 * where
 * getWhere
 * andWhere
 * orWhere
 * join
 * getJoin
 * leftJoin
 * rightJoin
 * crossJoin
 * innerJoin
 * naturalJoin
 * naturalLeftJoin
 * naturalRightJoin
 * group
 * getGroup
 * having
 * getHaving
 * order
 * getOrder
 * limit
 * getLimit
 * offset
 * getOffset
 * union
 * getUnion
 *
 */

class CDbCommand
{
    /** @var CDatabase */
    protected $_db;
    /** @var */
    protected $_dbDriver = '';
    /** @var array */
    public $_params = [];

    /** @var string */
    private $_text;
    /** @var string */
    private $_statement;
    /** @var string */
    private $_query;
    /** @var char */
    private $_backQuote = '`';
    /** @var  int */
    private $_fetchMode = PDO::FETCH_ASSOC;


    /**
     * Class constructor
     *
     * @param  CDatabase  $dbConnection
     */
    public function __construct($dbConnection = null)
    {
        $this->_db       = $dbConnection;
        $this->_dbDriver = CConfig::get('db.driver');

        // Set back quote according to database driver
        if (preg_match('/mssql|sqlsrv/i', CConfig::get('db.driver'))) {
            $this->_backQuote = '';
        }
    }

    /**
     * Cleans up the command for building a new query
     *
     * @return CDbCommand command instance
     */
    public function reset()
    {
        $this->_text      = null;
        $this->_query     = null;
        $this->_statement = null;
        $this->_params    = [];

        return $this;
    }

    /**
     * Cancels execution of the SQL statement
     */
    public function cancel()
    {
        $this->_statement = null;
    }

    /**
     * Defines SQL statement to be executed
     *
     * @return CDbCommand command instance
     */
    public function setText($value)
    {
        $this->_text = $value;
        $this->cancel();

        return $this;
    }

    /**
     * Returns SQL to be executed
     *
     * @return string
     */
    public function getText()
    {
        if ($this->_text == '' && ! empty($this->_query)) {
            $this->setText($this->buildQuery($this->_query));
        }

        return $this->_text;
    }

    /**
     * Executes the SQL statement and returns all rows
     *
     * @param  boolean  $fetchAssociative
     * @param  array  $params
     *
     * @return array
     */
    public function queryAll($fetchAssociative = true, $params = [])
    {
        return $this->_queryInternal('fetchAll', $fetchAssociative ? $this->_fetchMode : PDO::FETCH_NUM, $params);
    }

    /**
     * Executes the SQL statement and returns the first row of the result.
     *
     * @param  boolean  $fetchAssociative
     * @param  array  $params
     *
     * @return array
     */
    public function queryRow($fetchAssociative = true, $params = [])
    {
        return $this->_queryInternal('fetch', $fetchAssociative ? $this->_fetchMode : PDO::FETCH_NUM, $params);
    }

    /**
     * Executes the SQL statement and returns the value of the first column in the first row of data
     *
     * @param  array  $params
     *
     * @return array
     */
    public function queryScalar($params = [])
    {
        return $this->_queryInternal('fetchColumn', 0, $params);
    }

    /**
     * Executes the SQL statement and returns the first column of the result
     *
     * @param  array  $params
     *
     * @return array
     */
    public function queryColumn($params = [])
    {
        return $this->_queryInternal('fetchAll', PDO::FETCH_COLUMN, $params);
    }

    /**
     * Executes the SQL statement
     *
     * @param  string  $method
     * @param  mixed  $mode
     * @param  array  $params
     *
     * @return mixed
     */
    private function _queryInternal($method, $mode, $params = [])
    {
        $params = array_merge($this->_params, $params);

        return $this->_db->select($this->getText(), $params, $method, $mode);
    }

    /**
     * Builds a SQL SELECT statement from the given query specification
     *
     * @param  array  $query
     *
     * @return string the SQL statement
     */
    public function buildQuery($query)
    {
        $sql = ! empty($query['distinct']) ? 'SELECT DISTINCT' : 'SELECT';
        $sql .= ' '.(! empty($query['select']) ? $query['select'] : '*');

        $limit  = isset($query['limit']) ? (int)$query['limit'] : '';
        $offset = isset($query['offset']) ? (int)$query['offset'] : '';
        $limits = $this->_applyLimit($limit, $offset);

        if ( ! empty($limits['before'])) {
            $sql .= "\n ".$limits['before'];
        }
        if ( ! empty($query['from'])) {
            $sql .= "\nFROM ".$query['from'];
        }
        if ( ! empty($query['join'])) {
            $sql .= "\n".(is_array($query['join']) ? implode("\n", $query['join']) : $query['join']);
        }
        if ( ! empty($query['where'])) {
            $sql .= "\nWHERE ".$query['where'];
        }
        if ( ! empty($query['group'])) {
            $sql .= "\nGROUP BY ".$query['group'];
        }
        if ( ! empty($query['having'])) {
            $sql .= "\nHAVING ".$query['having'];
        }
        if ( ! empty($query['union'])) {
            $sql .= "\nUNION (\n".(is_array($query['union']) ? implode("\n) UNION (\n", $query['union'])
                    : $query['union']).')';
        }
        if ( ! empty($query['order'])) {
            $sql .= "\nORDER BY ".$query['order'];
        }
        if ( ! empty($limits['after'])) {
            $sql .= "\n ".$limits['after'];
        }

        return $sql;
    }

    /**
     * Sets SELECT part of the query
     *
     * @param  mixed  $columns  The columns to be selected (default '*' - all columns, or as array (e.g. ['id', 'name'] )
     * @param  string  $option  additional option that should be usaed, for example: SQL_CALC_FOUND_ROWS
     *
     * @return CDbCommand command instance
     */
    public function select($columns = '*', $option = '')
    {
        if (is_string($columns) && strpos($columns, '(') !== false) {
            $this->_query['select'] = $columns;
        } else {
            if ( ! is_array($columns)) {
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
            }

            foreach ($columns as $key => $column) {
                if (is_object($column)) {
                    $columns[$key] = (string)$column;
                } elseif (strpos($column, '(') === false) {
                    // With alias
                    if (preg_match('/^(.*?)(?i:\s+as\s+|\s+)(.*)$/', $column, $matches)) {
                        $columns[$key] = $this->_quotes($matches[1]).' AS '.$this->_quotes($matches[2]);
                    } else {
                        $columns[$key] = $column !== '*' ? $this->_quotes($column) : '*';
                    }
                }
            }

            $this->_query['select'] = implode(', ', $columns);
        }

        if ($option != '') {
            $this->_query['select'] = $option.' '.$this->_query['select'];
        }

        return $this;
    }

    /**
     * Returns the SELECT part of the query
     *
     * @return string
     */
    public function getSelect()
    {
        return isset($this->_query['select']) ? $this->_query['select'] : '';
    }

    /**
     * Sets a SELECT part of the query with the DISTINCT flag turned ON
     *
     * @param  mixed  $columns
     *
     * @return CDbCommand command instance
     */
    public function selectDistinct($columns = '*')
    {
        $this->_query['distinct'] = true;

        return $this->select($columns);
    }

    /**
     * Sets a FROM part of the query
     *
     * @param  mixed string|array
     *
     * @return CDbCommand command instance
     */
    public function from($tables)
    {
        if (is_string($tables) && strpos($tables, '(') !== false) {
            $this->_query['from'] = $tables;
        } else {
            if ( ! is_array($tables)) {
                $tables = preg_split('/\s*,\s*/', trim($tables), -1, PREG_SPLIT_NO_EMPTY);
            }

            foreach ($tables as $key => $table) {
                if (strpos($table, '(') === false) {
                    // With alias
                    if (preg_match('/^(.*?)(?i:\s+as|)\s+([^ ]+)$/', $table, $matches)) {
                        $tables[$key] = $this->_quotes($matches[1]).' '.$this->_quotes($matches[2]);
                    } else {
                        $tables[$key] = $this->_quotes($table);
                    }
                }
            }

            $this->_query['from'] = implode(', ', $tables);
        }

        return $this;
    }

    /**
     * Returns a FROM part in the query
     *
     * @return string
     */
    public function getFrom()
    {
        return isset($this->_query['from']) ? $this->_query['from'] : '';
    }

    /**
     * Sets the WHERE part of the query
     *
     * @param  mixed  $conditions  Ex.: ['and', 'id=1', 'id=2']
     * @param  array  $params
     *
     * @return CDbCommand the command object itself
     */
    public function where($conditions, $params = [])
    {
        $this->_query['where'] = $this->_processConditions($conditions);
        foreach ($params as $name => $value) {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns the WHERE part in the query
     *
     * @return string
     */
    public function getWhere()
    {
        return isset($this->_query['where']) ? $this->_query['where'] : '';
    }

    /**
     * Sets the WHERE part of the query with AND
     *
     * @param  mixed  $conditions  Ex.: ['id=1', 'id=2']
     * @param  array  $params
     *
     * @return CDbCommand the command object itself
     */
    public function andWhere($conditions, $params = [])
    {
        if (isset($this->_query['where'])) {
            $this->_query['where'] = $this->_processConditions(['AND', $this->_query['where'], $conditions]);
        } else {
            $this->_query['where'] = $this->_processConditions($conditions);
        }

        foreach ($params as $name => $value) {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    /**
     * Sets the WHERE part of the query with OR
     *
     * @param  mixed  $conditions  Ex.: array('id=1', 'id=2')
     * @param  array  $params
     *
     * @return CDbCommand the command object itself
     */
    public function orWhere($conditions, $params = [])
    {
        if (isset($this->_query['where'])) {
            $this->_query['where'] = $this->_processConditions(['OR', $this->_query['where'], $conditions]);
        } else {
            $this->_query['where'] = $this->_processConditions($conditions);
        }

        foreach ($params as $name => $value) {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    /**
     * Appends an INNER JOIN part to the query
     * Ex.: join('table2', 'table1.id = table2.t_id')
     *
     * @param  string  $table
     * @param  mixed  $conditions  join condition that should appear in the ON part
     * @param  array  $params  format: (name=>value) to be bound to the query
     *
     * @return CDbCommand the command object itself
     */
    public function join($table, $conditions, $params = [])
    {
        return $this->_joinInternal('join', $table, $conditions, $params);
    }

    /**
     * Returns the join part in the query
     *
     * @return mixed
     */
    public function getJoin()
    {
        return isset($this->_query['join']) ? $this->_query['join'] : '';
    }

    /**
     * Appends a LEFT OUTER JOIN part to the query
     *
     * @param  string  $table
     * @param  mixed  $conditions  join condition that should appear in the ON part
     * @param  array  $params  format: (name=>value) to be bound to the query
     *
     * @return CDbCommand the command object itself
     */
    public function leftJoin($table, $conditions, $params = [])
    {
        return $this->_joinInternal('left join', $table, $conditions, $params);
    }

    /**
     * Appends a RIGHT OUTER JOIN part to the query
     *
     * @param  string  $table
     * @param  mixed  $conditions  join condition that should appear in the ON part
     * @param  array  $params  format: (name=>value) to be bound to the query
     *
     * @return CDbCommand the command object itself
     */
    public function rightJoin($table, $conditions, $params = [])
    {
        return $this->_joinInternal('right join', $table, $conditions, $params);
    }

    /**
     * Appends a CROSS (INNER) JOIN part to the query
     *
     * @param  string  $table
     *
     * @return CDbCommand the command object itself
     */
    public function crossJoin($table)
    {
        return $this->_joinInternal('cross join', $table);
    }

    /**
     * Alias to crossJoin method
     */
    public function innerJoin($table)
    {
        return $this->_joinInternal('inner join', $table);
    }

    /**
     * Appends a NATURAL JOIN part to the query
     *
     * @param  string  $table
     *
     * @return CDbCommand the command object itself
     */
    public function naturalJoin($table)
    {
        return $this->_joinInternal('natural join', $table);
    }

    /**
     * Appends a NATURAL LEFT JOIN part to the query
     *
     * @param  string  $table
     *
     * @return CDbCommand the command object itself
     */
    public function naturalLeftJoin($table)
    {
        return $this->_joinInternal('natural left join', $table);
    }

    /**
     * Appends a NATURAL RIGHT JOIN part to the query
     *
     * @param  string  $table
     *
     * @return CDbCommand the command object itself
     */
    public function naturalRightJoin($table)
    {
        return $this->_joinInternal('natural right join', $table);
    }

    /**
     * Sets the GROUP BY part of the query
     * Ex.: columns specified in either a string (e.g. 'id', 'name') or an array (e.g. array('id', 'name'))
     *
     * @return CDbCommand the command object itself
     */
    public function group($columns)
    {
        if (is_string($columns) && strpos($columns, '(') !== false) {
            $this->_query['group'] = $columns;
        } else {
            if ( ! is_array($columns)) {
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
            }

            foreach ($columns as $i => $column) {
                if (is_object($column)) {
                    $columns[$i] = (string)$column;
                } elseif (strpos($column, '(') === false) {
                    $columns[$i] = $this->_quotes($column);
                }
            }

            $this->_query['group'] = implode(', ', $columns);
        }

        return $this;
    }

    /**
     * Returns the GROUP BY part in the query
     *
     * @return string (without 'GROUP BY')
     */
    public function getGroup()
    {
        return isset($this->_query['group']) ? $this->_query['group'] : '';
    }

    /**
     * Sets the HAVING part of the query
     *
     * @param  mixed  $conditions
     * @param  array  $params
     *
     * @return CDbCommand the command object itself
     */
    public function having($conditions, $params = [])
    {
        $this->_query['having'] = $this->_processConditions($conditions);
        foreach ($params as $name => $value) {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns the HAVING part in the query
     *
     * @return string (without 'HAVING')
     */
    public function getHaving()
    {
        return isset($this->_query['having']) ? $this->_query['having'] : '';
    }

    /**
     * Sets the ORDER BY part of the query.
     *
     * @param  mixed  $columns  (e.g. order(array('id ASC', 'name DESC')) or order('(1)'))
     *
     * @return CDbCommand the command object itself
     */
    public function order($columns)
    {
        if (is_string($columns) && strpos($columns, '(') !== false) {
            $this->_query['order'] = $columns;
        } else {
            if ( ! is_array($columns)) {
                $columns = preg_split('/\s*,\s*/', trim($columns), -1, PREG_SPLIT_NO_EMPTY);
            }

            foreach ($columns as $i => $column) {
                if (is_object($column)) {
                    $columns[$i] = (string)$column;
                } elseif (strpos($column, '(') === false) {
                    if (preg_match('/^(.*?)\s+(asc|desc)$/i', $column, $matches)) {
                        $columns[$i] = $this->_quotes($matches[1]).' '.strtoupper($matches[2]);
                    } else {
                        $columns[$i] = $this->_quotes($column);
                    }
                }
            }

            $this->_query['order'] = implode(', ', $columns);
        }

        return $this;
    }

    /**
     * Returns the ORDER BY part in the query
     *
     * @return string (without 'ORDER BY')
     */
    public function getOrder()
    {
        return isset($this->_query['order']) ? $this->_query['order'] : '';
    }

    /**
     * Sets the LIMIT part of the query
     *
     * @param  int  $limit
     * @param  int  $offset
     *
     * @return CDbCommand the command object itself
     */
    public function limit($limit, $offset = null)
    {
        $this->_query['limit'] = (int)$limit;
        if ($offset !== null) {
            $this->offset($offset);
        }

        return $this;
    }

    /**
     * Returns the LIMIT part in the query
     *
     * @return string (without 'LIMIT')
     */
    public function getLimit()
    {
        return isset($this->_query['limit']) ? $this->_query['limit'] : -1;
    }

    /**
     * Sets the OFFSET part of the query
     *
     * @param  int  $offset
     *
     * @return CDbCommand the command object itself
     */
    public function offset($offset)
    {
        $this->_query['offset'] = (int)$offset;

        return $this;
    }

    /**
     * Returns the OFFSET part in the query
     *
     * @return string (without 'OFFSET')
     */
    public function getOffset()
    {
        return isset($this->_query['offset']) ? $this->_query['offset'] : -1;
    }

    /**
     * Appends a SQL statement using UNION operator
     *
     * @param  string  $sql
     *
     * @return CDbCommand the command object itself
     */
    public function union($sql)
    {
        if (isset($this->_query['union']) && is_string($this->_query['union'])) {
            $this->_query['union'] = [$this->_query['union']];
        }

        $this->_query['union'][] = $sql;

        return $this;
    }

    /**
     * Returns the UNION part in the query
     *
     * @return mixed (without 'UNION')
     */
    public function getUnion()
    {
        return isset($this->_query['union']) ? $this->_query['union'] : '';
    }

    /**
     * Creates condition string that will be put in the WHERE part od the SQL statement
     *
     * @param  mixed  $conditions
     *
     * @return string
     */
    private function _processConditions($conditions)
    {
        if (empty($conditions)) {
            return '';
        } elseif ( ! is_array($conditions)) {
            return $conditions;
        }

        $conditionsCount = count($conditions);
        $operator        = strtoupper($conditions[0]);
        if (in_array($operator, ['OR', 'AND'])) {
            $parts = [];
            for ($i = 1; $i < $conditionsCount; $i++) {
                $condition = $this->_processConditions($conditions[$i]);
                if ($condition !== '') {
                    $parts[] = '('.$condition.')';
                }
            }

            return ! empty($parts) ? implode(' '.$operator.' ', $parts) : '';
        }

        if ( ! isset($conditions[1], $conditions[2])) {
            return '';
        }

        $column = $conditions[1];
        if (strpos($column, '(') === false) {
            $column = $this->_quotes($column);
        }

        $values = $conditions[2];
        if ( ! is_array($values)) {
            $values = [$values];
        }

        if (in_array($operator, ['IN', 'NOT IN'])) {
            if ($values === []) {
                return $operator === 'IN' ? '0=1' : '';
            }

            foreach ($values as $i => $value) {
                $values[$i] = is_string($value) ? $this->_quotes($value) : (string)$value;
            }

            return $column.' '.$operator.' ('.implode(', ', $values).')';
        }

        if (in_array($operator, ['LIKE', 'NOT LIKE', 'OR LIKE', 'OR NOT LIKE'])) {
            if (empty($values)) {
                return $operator === 'LIKE' || $operator === 'OR LIKE' ? '0=1' : '';
            }

            if ($operator === 'LIKE' || $operator === 'NOT LIKE') {
                $andor = ' AND ';
            } else {
                $andor    = ' OR ';
                $operator = $operator === 'OR LIKE' ? 'LIKE' : 'NOT LIKE';
            }

            $expressions = [];
            foreach ($values as $value) {
                $expressions[] = $column.' '.$operator.' '.$this->_quotes($value);
            }

            return implode($andor, $expressions);
        }

        CDebug::addMessage(
            'errors',
            'wrong operator in condition ',
            A::t('core', 'Unknown operator "{operator}".', ['{operator}' => $operator])
        );
    }

    /**
     * Appends an JOIN part to the query
     *
     * @param  string  $type  Ex.:('join', 'left join', 'right join', 'cross join', 'natural join')
     * @param  string  $table
     * @param  mixed  $conditions
     * @param  array  $params
     *
     * @return CDbCommand the command object itself
     */
    private function _joinInternal($type, $table, $conditions = '', $params = [])
    {
        if (strpos($table, '(') === false) {
            if (preg_match('/^(.*?)(?i:\s+as|)\s+([^ ]+)$/', $table, $matches)) {
                // With alias
                $table = $this->_connection->quoteTableName($matches[1]).' '.$this->_connection->quoteTableName(
                        $matches[2]
                    );
            } else {
                $table = $this->_connection->quoteTableName($table);
            }
        }

        $conditions = $this->_processConditions($conditions);
        if ($conditions != '') {
            $conditions = ' ON '.$conditions;
        }

        if (isset($this->_query['join']) && is_string($this->_query['join'])) {
            $this->_query['join'] = [$this->_query['join']];
        }

        $this->_query['join'][] = strtoupper($type).' '.$table.$conditions;
        foreach ($params as $name => $value) {
            $this->_params[$name] = $value;
        }

        return $this;
    }

    /**
     * Escapes given string with backquotes
     * Prepares table name for using in SQL statements
     *
     * @param  string  $string
     *
     * @return string
     */
    private function _quotes($string = '')
    {
        return $this->_backQuote.$string.$this->_backQuote;
    }

    /**
     * Prepare LIMIT clause for SQL statement
     *
     * @param  string  $limit
     * @param  string  $offset
     *
     * @return array
     * @retun array
     */
    private function _applyLimit($limit, $offset = '')
    {
        $limits = ['before' => '', 'after' => ''];

        if ( ! empty($limit)) {
            if (preg_match('/mssql|sqlsrv/i', $this->_dbDriver)) {
                $limits['before'] = ! empty($limit) ? ' TOP '.$limit : '';
            } else {
                $limits['after'] = ! empty($limit) ? ' LIMIT '.(! empty($offset) ? ', ' : '').' '.$limit : '';
            }
        }

        return $limits;
    }
}

