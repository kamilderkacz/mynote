<?php

class Application_Model_DbTable_Section extends Zend_Db_Table_Abstract
{
    protected $_name = 'section';
    protected $_primary = 'section_id';

    
        
    public function getAllSectionsCount() {
        
        $select = $this->select();
        // Zend_Db_Select
        $statement = $select->from($this, "COUNT(section_id) as count");
        return $this->fetchRow($statement)['count'];
        
    }
}

