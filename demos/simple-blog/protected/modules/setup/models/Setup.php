<?php

class Setup extends CModel
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }
    
    public function doBeginTransaction()
    {
        /* begin a transaction, turning off autocommit */
        $this->_db->beginTransaction();
    }
    
    public function doRollBack()
    {
        /* recognize mistake and roll back changes */
        $this->_db->rollBack();
    }

    public function doCommit()
    {
        /* commit the changes */
        $this->_db->commit();            
    }
    
    public function install($sqlDump = '', $transaction = true)
    {
        if(empty($sqlDump)){
            $this->_error = true;
            $this->_errorMessage = 'No SQL statements found! Please check your data file.';
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
                                /* recognize mistake and roll back changes */
                                if($transaction) $this->_db->rollBack();                                
                                return false;
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