<?php

class Application_Model_DbTable_Note extends Zend_Db_Table_Abstract
{
    protected $_name = 'note';
    protected $_primary = 'note_id';

    // Spis typów kolorów przekazywanych do helpera
    const INDEX_TABLE_HEADER_BG = 1;
    const SHOW_NOTE_HEADING_BG = 2;
    const INDEX_TABLE_HEADER_TXT = 3;
    const SHOW_NOTE_HEADING_TXT = 4;
    const BUTTON_BG = 5;
    
    // Metoda pobierająca notatkę i sesję
    public function getNoteJoinSection($note_id) { 
        
        $select = $this->select()->setIntegrityCheck(false) // blokada edycji i zapisu!!! PRZY JOIN
                        ->from(array('n'=>'note'))
                        ->join(array('s'=>'section'), 'n.note_section_id = s.section_id')
                        ->where('n.note_id = ' . $note_id);
        $row = $this->fetchRow($select);
        return $row;
    }
}

