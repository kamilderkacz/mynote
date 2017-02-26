<?php

class Application_Model_NoteMapper {

    protected $_dbTable;

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Note');
        }
        return $this->_dbTable;
    }

    public function save( Application_Model_Note $note ) {
        $data = array(
            'note_author_id' => $note->getAuthorId(),
            'note_section_id' => $note->getSectionId(),
            'note_title' => $note->getTitle(),
            'note_content' => $note->getContent(),
            'note_author' => 'gość',
            'note_creation_datetime' => date('Y-m-d H:i:s'),
            'note_removed' => $note->getRemoved(),
        );
        if (null === ($id = $note->getId())) {
            unset($data['note_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('note_id = ?' => $id));
        }
    }
//    public function delete( $noteId ) {
//        $this->getDbTable()->delete('note_id = '.$noteId);
//    }
    public function deleteAllSectionNotes($sectionId) {
        if($sectionId === null) {
            throw new Exception('Nie przekazano ID sekcji!');
        } 
        
        $notes = $this->fetchAll('note_section_id ='.$sectionId);
//        var_dump($dupa); die();
        foreach ($notes as $note) {
            $note->setRemoved(1);
            $this->save($note);
        }
            
    }

    public function fetchOne($id, Application_Model_Note $note) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return; // NIC NIE ZNALEZIONO
        }
        $row = $result->current(); // zmienia Rowset na Row
        $note->setId($row->note_id)
                ->setAuthorId($row->note_author_id)
                ->setSectionId($row->note_section_id)
                ->setTitle($row->note_title)
                ->setContent($row->note_content)
                ->setCreationDatetime($row->note_creation_datetime)
                ->setAuthor($row->note_author)
                ->setRemoved($row->note_removed)
                ;
        return $note;
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
        $resultSet = $this->getDbTable()->fetchAll($where,$order,$count,$offset);
        $notes = array();
        foreach ($resultSet as $row) {
            $note = new Application_Model_Note();
            $note->setId($row->note_id)
                    ->setAuthorId($row->note_author_id)
                    ->setSectionId($row->note_section_id)
                    ->setTitle($row->note_title)
                    ->setContent($row->note_content)
                    ->setCreationDatetime($row->note_creation_datetime)
                    ->setAuthor($row->note_author)
                    ->setRemoved($row->note_removed)
                    ;
            $notes[] = $note;
        }
        return $notes;
    }

}
