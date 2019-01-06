<?php
/**
 * Setup model
 *
 * PUBLIC:                 	PROTECTED:                 	PRIVATE:
 * ---------------         	---------------				---------------
 * __construct
 * doBeginTransaction
 * doRollBack
 * doCommit
 * install
 *
 */

namespace Modules\Setup\Models;

// Framework
use \A,
	\CModel,
	\CDatabase;


class Setup extends CModel
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }
    
	/**
	 * Begins transaction
	 */
    public function doBeginTransaction()
    {
        /* begin a transaction, turning off autocommit */
        $this->_db->beginTransaction();
    }
    
	/**
	 * Executes rollback
	 */
    public function doRollBack()
    {
        /* recognize mistake and roll back changes */
        $this->_db->rollBack();
    }

	/**
	 * Executes commit 
	 */
    public function doCommit()
    {
        /* commit the changes */
        $this->_db->commit();            
    }
    
	/**
	 * Executes installation of given SQL
	 * @param string $sqlDump
	 * @param bool $transaction
	 * @param bool $ignoreErrors
	 * @return bool
	 */
    public function install($sqlDump = '', $transaction = true, $ignoreErrors = false)
    {
        if(empty($sqlDump)){
            $this->_error = true;
            $this->_errorMessage = A::t('setup', 'No SQL statements found! Please check your data file.');
            return false;
        }else{
            /* begin a transaction, turning off autocommit */
            if($transaction) $this->_db->beginTransaction();
            $query = '';                
            foreach($sqlDump as $sqlLine){
                $tsl = trim(utf8_decode($sqlLine));
                if(($sqlLine != '') && (substr($tsl, 0, 2) != '--') && (substr($tsl, 0, 1) != '?') && (substr($tsl, 0, 1) != '#')) {
                    $query .= $sqlLine;
                    if(preg_match("/;\s*$/", $sqlLine)){
                        if(strlen(trim($query)) > 5){
                            $result = $this->_db->customExec($query);
                            if(CDatabase::getError()){
                                $this->_error = true;
                                $this->_errorMessage = CDatabase::getErrorMessage();
                                if(!$ignoreErrors){
                                    /* recognize mistake and roll back changes */
                                    if($transaction) $this->_db->rollBack();                                
                                    return false;
                                }
                            }
                        }
                        $query = '';
                    }
                }
            }
            /* commit the changes */
            if($transaction) $this->_db->commit();            
            return true;
        }        
    }    

}