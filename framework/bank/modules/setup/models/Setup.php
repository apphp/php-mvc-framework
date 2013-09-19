<?php

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
        $this->db->beginTransaction();
    }
    
	/**
	 * Executes rollback
	 */
    public function doRollBack()
    {
        /* recognize mistake and roll back changes */
        $this->db->rollBack();
    }

	/**
	 * Executes commit 
	 */
    public function doCommit()
    {
        /* commit the changes */
        $this->db->commit();            
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
            $this->_errorMessage = 'No SQL statements found! Please check your data file.';
            return false;
        }else{
            /* begin a transaction, turning off autocommit */
            if($transaction) $this->db->beginTransaction();
            $query = '';                
            foreach($sqlDump as $sqlLine){
                $tsl = trim(utf8_decode($sqlLine));
                if(($sqlLine != '') && (substr($tsl, 0, 2) != '--') && (substr($tsl, 0, 1) != '?') && (substr($tsl, 0, 1) != '#')) {
                    $query .= $sqlLine;
                    if(preg_match("/;\s*$/", $sqlLine)){
                        if(strlen(trim($query)) > 5){
                            $result = $this->db->customExec($query);
                            if(CDatabase::getError()){
                                $this->_error = true;
                                $this->_errorMessage = CDatabase::getErrorMessage();
                                if(!$ignoreErrors){
                                    /* recognize mistake and roll back changes */
                                    if($transaction) $this->db->rollBack();                                
                                    return false;
                                }
                            }
                        }
                        $query = '';
                    }
                }
            }
            /* commit the changes */
            if($transaction) $this->db->commit();            
            return true;
        }        
    }    

}