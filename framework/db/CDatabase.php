<?php
/**
 * CDatabase core class file
 *
 * @project ApPHP Framework
 * @author ApPHP <info@apphp.com>
 * @link http://www.apphpframework.com/
 * @copyright Copyright (c) 2012 - 2013 ApPHP Framework
 * @license http://www.apphpframework.com/license/
 *
 * IMPORTANT:
 * -----------
 * PDO::exec() should be used for queries that do not return a resultset, such as a delete statement or 'set'.
 * PDO::query() should be used when you expect a resultset to be returned.
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct                                          errorLog
 * select                                               interpolateQuery 
 * insert                                               prepareParams
 * update
 * delete
 * customQuery
 * customExec
 * showTables
 * showColumns
 * getVersion
 * 
 * STATIC:
 * ---------------------------------------------------------------
 * init                                                 fatalErrorPageContent
 * getError
 * getErrorMessage 
 * 
 */	  

class CDatabase extends PDO
{    
 
	/** @var object */    
    private static $_instance;
    /** @var string */ 
    private $_dbPrefix;
    /** @var string */ 
    private $_dbType;
    /** @var string */ 
    private $_dbName;
	/**	@var boolean */
	private static $_error;
	/**	@var string */
	private static $_errorMessage;
    /** @var string */
    public static $count = 0;
    
	/**
	 * Class default constructor
	 * @param array $params
	 */
    public function __construct($params = array())
    {
        if(!empty($params)){
            $dbType = isset($params['dbType']) ? $params['dbType'] : '';
            $dbHost = isset($params['dbHost']) ? $params['dbHost'] : '';
            $dbName = isset($params['dbName']) ? $params['dbName'] : '';
            $dbUser = isset($params['dbUser']) ? $params['dbUser'] : '';
            $dbPassword = isset($params['dbPassword']) ? $params['dbPassword'] : '';
        
            try{
                @parent::__construct($dbType.':host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''));
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(Exception $e){
                self::$_error = true;
                self::$_errorMessage = $e->getMessage();
            }
            $this->_dbType = $dbType;
            $this->_dbName = $dbName;
            $this->_dbPrefix = '';
        }else{            
            try{
                if(CConfig::get('db') != ''){
                    @parent::__construct(CConfig::get('db.type').':host='.CConfig::get('db.host').';dbname='.CConfig::get('db.database'),
                        CConfig::get('db.username'),
                        CConfig::get('db.password'),
						array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\'')
                    );
                    $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 				
                }else{
                    throw new Exception('Missing database configuration file');
                }
            }catch(Exception $e){    
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                $output = self::fatalErrorPageContent();
                if(APPHP_MODE == 'debug'){
                    $output = str_ireplace('{DESCRIPTION}', '<p>'.A::t('core', 'This application is currently experiencing some database difficulties').'</p>', $output);
                    $output = str_ireplace(
                        '{CODE}',
                        '<b>Description:</b> '.$e->getMessage().'<br>
                        <b>File:</b> '.$e->getFile().'<br>
                        <b>Line:</b> '.$e->getLine(),
                        $output
                    );
                }else{
                    $output = str_ireplace('{DESCRIPTION}', '<p>'.A::t('core', 'This application is currently experiencing some database difficulties. Please check back again later').'</p>', $output);
                    $output = str_ireplace('{CODE}', A::t('core', 'For more information turn on debug mode in your application'), $output);
                }
                echo $output;
                exit(1);
            }
            $this->_dbType = CConfig::get('db.type');
            $this->_dbName = CConfig::get('db.database');
            $this->_dbPrefix = CConfig::get('db.prefix');
        }        
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
     * Performs select query
     * @param string $sql SQL string
     * @param array $array parameters to bind
     * @param constant $fetchMode PDO fetch mode
     * @return mixed - an array containing all of the result set rows
     * Ex.: Array([0] => Array([id] => 11, [name] => John), ...)
     */
    public function select($sql, $params = array(), $fetchMode = PDO::FETCH_ASSOC)
    {
        $sth = $this->prepare($sql);
		try{
            if(is_array($params)){
                foreach($params as $key => $value){
                    list($key, $param) = $this->prepareParams($key);
                    $sth->bindValue($key, $value, $param);
                }
            }            
			$sth->execute();
			$result = $sth->fetchAll($fetchMode);
			$countText = count($result);
		}catch(PDOException $e){
            $this->errorLog('select [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$this->interpolateQuery($sql, $params));
			$result = false; 
			$countText = '0 (<b>error</b>)';
		}		
        CDebug::AddMessage('queries', ++self::$count.'. select | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
        return $result;
    }
    
    /**
     * Performs insert query
     * @param string $table name of the table to insert into
     * @param string $data associative array
     * @return boolean
     */
    public function insert($table, $data)
    {
        if(APPHP_MODE == 'demo'){
			self::$_errorMessage = A::t('core', 'This operation is blocked in Demo Mode!');
			return false;
		}

        ksort($data);
        
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':'.implode(', :', array_keys($data));
        
        $sql = 'INSERT INTO `'.$this->_dbPrefix.$table.'` (`'.$fieldNames.'`) VALUES ('.$fieldValues.')';
        $sth = $this->prepare($sql);
        
        foreach($data as $key => $value){
            list($key, $param) = $this->prepareParams($key);
            $sth->bindValue(':'.$key, $value, $param);
        }
        
		try{
			$sth->execute();
			$countText = $result = $this->lastInsertId();
		}catch(PDOException $e){
            $this->errorLog('insert [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$this->interpolateQuery($sql, $data));
			$result = false;
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. insert | <i>ID: '.$countText.'</i>', $sql);
		return $result; 
    }
    
    /**
     * Performs update query
     * @param string $table name of table to update
     * @param string $data an associative array
     * @param string $where the WHERE clause of query
     * @param boolean
     */
    public function update($table, $data, $where = '1')
    {
        if(APPHP_MODE == 'demo'){
			self::$_errorMessage = A::t('core', 'This operation is blocked in Demo Mode!');
			return false;
		} 
		
		ksort($data);
        
        $fieldDetails = NULL;
        foreach($data as $key => $value){
            $fieldDetails .= '`'.$key.'` = :'.$key.',';
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        
        $sql = 'UPDATE `'.$this->_dbPrefix.$table.'` SET '.$fieldDetails.' WHERE '.$where;
        $sth = $this->prepare($sql);
        
        foreach($data as $key => $value){
            list($key, $param) = $this->prepareParams($key);
            $sth->bindValue(':'.$key, $value, $param);
        }
        
		try{
			$sth->execute();
			$result = true; 
			$countText = $sth->rowCount();
		}catch(PDOException $e){
            // Get trace from parent level 
            // $trace = $e->getTrace();
            // echo '<pre>';
            // echo $trace[1]['file'];
            // echo $trace[1]['line'];
            // echo '</pre>';
            $this->errorLog('update [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$this->interpolateQuery($sql, $data));
			$result = false; 
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. update | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result; 
    }
    
    /**
     * Performs delete query
     * @param string $table
     * @param string $where the WHERE clause of query 
     * @param array $params
     * @return integer affected rows
     */
    public function delete($table, $where = '', $params = array())
    {
        if(APPHP_MODE == 'demo'){
			self::$_errorMessage = A::t('core', 'This operation is blocked in Demo Mode!');
			return false;
		} 

        $where_clause = (!empty($where) && !preg_match('/\bwhere\b/i', $where)) ? ' WHERE '.$where : $where;
        $sql = 'DELETE FROM `'.$this->_dbPrefix.$table.'` '.$where_clause;
        
        $sth = $this->prepare($sql);
        if(is_array($params)){
            foreach($params as $key => $value){
                list($key, $param) = $this->prepareParams($key);
                $sth->bindValue($key, $value, $param);
            }
        }

		try{
			//$result = $this->exec($sql);
            $sth->execute(); 
			$countText = $result = $sth->rowCount();
		}catch(PDOException $e){			
            $this->errorLog('delete [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$this->interpolateQuery($sql, $params));            
			$result = false;
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. delete | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result; 
    }
	
    /**
     * Performs a standard query
     * @param string $sql
     * @param constant $fetchMode PDO fetch mode
     * @return mixed
     */
	public function customQuery($sql, $fetchMode = PDO::FETCH_ASSOC)
	{
        if(APPHP_MODE == 'demo'){
			self::$_errorMessage = A::t('core', 'This operation is blocked in Demo Mode!');
			return false;
		} 

		try{
			$sth = $this->query($sql);
			$result = $sth->fetchAll($fetchMode);
			$countText = count($result);
		}catch(PDOException $e){
            $this->errorLog('customQuery [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$sql);
			$result = false;
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. query | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result;
	}
    
    /**
     * Performs a standard exec
     * @param string $sql
     * @return boolean
     */
	public function customExec($sql)
	{
        if(APPHP_MODE == 'demo'){
			self::$_errorMessage = A::t('core', 'This operation is blocked in Demo Mode!');
			return false;
		} 
		
		try{
			$result = $this->exec($sql);
			$countText = $result;
		}catch(PDOException $e){
            $this->errorLog('customExec [database.php, ln.:'.$e->getLine().']', $e->getMessage().' => '.$sql);
			$result = false;
			$countText = '0 (<b>error</b>)';
		}		
        CDebug::AddMessage('queries', ++self::$count.'. query | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result;
    }
    
	/**
     * Performs a show tables query
     * @return mixed
     */
	public function showTables()
	{
        switch($this->_dbType){
			case 'mssql';
            case 'sqlsrv':
				$sql = 'SELECT * FROM sys.all_objects WHERE type = \'U\'';
				break;
            case 'pgsql':
                $sql = 'SELECT tablename FROM pg_tables WHERE tableowner = current_user';
                break;
            case 'sqlite':
                $sql = 'SELECT * FROM sqlite_master WHERE type=\'table\'';
                break;
			case 'oci':
				$sql = 'SELECT * FROM system.tab';
				break;
			case 'ibm':
				$schema = '';
				$sql = 'SELECT TABLE_NAME FROM qsys2.systables'.(($schema != '') ? ' WHERE TABLE_SCHEMA = \''.$schema.'\'' : '');
				break;
			case 'mysql':
			default:
				$sql = 'SHOW TABLES IN `'.$this->_dbName.'`';	
				break;
		}

		try{
			$sth = $this->query($sql);
			$result = $sth->fetchAll();
			$countText = count($result);
		}catch(PDOException $e){
            $this->errorLog('showTables [database.php, ln.:'.$e->getLine().']', $e->getMessage());
			$result = false; 
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. query | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result;
	}


	/**
     * Performs a show column query
     * @param string $table
     * @return mixed
     */
	public function showColumns($table = '')
	{
        switch($this->_dbType){
            case 'ibm':
                $sql = "SELECT COLUMN_NAME FROM qsys2.syscolumns WHERE TABLE_NAME = '".$this->_dbPrefix.$table."'";
                //.(($schema != '') ? " AND TABLE_SCHEMA = '".$schema."'" : ''); 
                break;
            case 'mssql':
                $sql = "SELECT COLUMN_NAME, data_type, character_maximum_length FROM ".$this->_dbName.".information_schema.columns WHERE table_name = '".$this->_dbPrefix.$table."'";
                break;
            default:
                $sql = 'SHOW COLUMNS FROM `'.$this->_dbPrefix.$table.'`';
                break;
        }

		try{
			$sth = $this->query($sql);
			$result = $sth->fetchAll();
			$countText = count($result);
		}catch(PDOException $e){
            $this->errorLog('showColumns [database.php, ln.:'.$e->getLine().']', $e->getMessage());
			$result = false;
			$countText = '0 (<b>error</b>)';
		}
        CDebug::AddMessage('queries', ++self::$count.'. query | <i>'.A::t('core', 'total').': '.$countText.'</i>', $sql);
		return $result;
    }    
    
    /**
     * Returns database engine version
     */
	public function getVersion()
	{
		$version = $this->getAttribute(PDO::ATTR_SERVER_VERSION);
		// clean version number from alphabetic characters
		return preg_replace('/[^0-9,.]/', '', $version);
	}
	
	/**	
	 * Get error status
	 * @return boolean
	 */
	public static function getError()
	{
		return self::$_error;
	}
 
	/**	
	 * Get error message
	 * @return string
	 */
	public static function getErrorMessage()
	{
		return self::$_errorMessage;
	} 

    /**
     * Writes error log
     * @param string $debugMessage
     * @param string $errorMessage
     */
    private function errorLog($debugMessage, $errorMessage)
    {
        self::$_error = true;
        self::$_errorMessage = $errorMessage;
        CDebug::AddMessage('errors', $debugMessage, $errorMessage);
    }
    
    /**
     * Returns fata error page content
     * @return html code
     */    
    private static function fatalErrorPageContent()
    {
        return '<!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Database Fatal Error</title>
        <style type="text/css">
            html{background:#f9f9f9}
            body{background:#fff; color:#333; font-family:sans-serif; margin:2em auto; padding:1em 2em 2em; -webkit-border-radius:3px; border-radius:3px; border:1px solid #dfdfdf; max-width:750px; text-align:left;}
            #error-page{margin-top:50px}
            #error-page h2{border-bottom:1px dotted #ccc;}
            #error-page p{font-size:16px; line-height:1.5; margin:2px 0 15px}
            #error-page .code-wrapper{color:#400; background-color:#f1f2f3; padding:5px; border:1px dashed #ddd}
            #error-page code{font-size:15px; font-family:Consolas,Monaco,monospace;}
            a{color:#21759B; text-decoration:none}
            a:hover{color:#D54E21}
            #footer{font-size:14px; margin-top:50px; color:#555;}
        </style>
        </head>
        <body id="error-page">
            <h2>Database connection error!</h2>
            {DESCRIPTION}
            <div class="code-wrapper">
            <code>{CODE}</code>
            </div>
            <div id="footer">
                If you\'re unsure what this error means you should probably contact your host.
                If you still need a help, you can alway visit <a href="http://apphp.net/forum" target="_new">ApPHP Support Forums</a>.
            </div>
        </body>
        </html>';        
    } 
    
    /**
     * Replaces any parameter placeholders in a query with the value of that parameter
     * @param string $sql 
     * @param array $params 
     * @return string 
     */
    private function interpolateQuery($sql, $params = array())
    {
        $keys = array();        
        if(!is_array($params)) return $sql;
    
        // build regular expression for each parameter
        foreach($params as $key => $value){
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            }else{
                $keys[] = '/[?]/';
            }
        }
    
        return preg_replace($keys, $params, $sql, 1, $count);
    }
    
    /**
     * Prepares/changes keys and parameters
     * @param $key
     * @return array
     */
    private function prepareParams($key)
    {
        $param = 0;
        $prefix = substr($key, 0, 2);
        switch($prefix){
            case 'i:':
                $key = str_replace('i:', ':', $key);    
                $param = PDO::PARAM_INT;
                break;
            case 'b:':
                $key = str_replace('b:', ':', $key);    
                $param = PDO::PARAM_BOOL;
                break;
            case 'f:':
                $key = str_replace('f:', ':', $key);    
                $param = PDO::PARAM_STR;
                break;
            case 's:':
                $key = str_replace('s:', ':', $key);    
                $param = PDO::PARAM_STR;
                break;
            case 'n:':
                $key = str_replace('n:', ':', $key);    
                $param = PDO::PARAM_NULL;
                break;
            default:
                $param = PDO::PARAM_STR;
                break;
        }
        return array($key, $param);       
    }    

}