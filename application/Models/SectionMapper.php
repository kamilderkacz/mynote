<?php

class Application_Model_SectionMapper {

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
            $this->setDbTable('Application_Model_DbTable_Section');
        }
        return $this->_dbTable;
    }

    public function save(Application_Model_Section $section) {
        $data = array(
            'section_author_id' => $section->getAuthorId(), // nie mamy author_id!!
            'section_fullname' => $section->getFullname(),
            'section_color' => $section->getColor(),
            'section_visibility' => $section->getVisibility(),
            'section_removed' => $section->getRemoved(),
            'section_order' => $section->getOrder(),
        );
        if (null === ($id = $section->getId())) {
            unset($data['section_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('section_id = ?' => $id));
        }
        return 1;
    }
//    public function delete($sectionId) {
//        $this->getDbTable()->delete('section_id = ' . $sectionId);
//    }
    public function fetchOne($id, Application_Model_Section $section) { // Zwraca obiekt z bazy danych
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current(); // current zmienia Rowset na Row
        $section->setId($row->section_id)
                ->setAuthorId($row->section_author_id)
                ->setFullname($row->section_fullname)
                ->setColor($row->section_color)
                ->setVisibility($row->section_visibility)
                ->setRemoved($row->section_removed)
                ->setOrder($row->section_order)
                ;
        return $section;
    }

    public function fetchAll($where, $order = null) { // Zwraca tablicę obiektów z bazy danych
        $resultSet = $this->getDbTable()->fetchAll($where, $order);
        $sections = array();
        foreach ($resultSet as $row) {
            $section = new Application_Model_Section();
            $section->setId($row->section_id)
                    ->setAuthorId($row->section_author_id)
                    ->setFullname($row->section_fullname)
                    ->setColor($row->section_color)
                    ->setVisibility($row->section_visibility)
                    ->setRemoved($row->section_removed)
                    ->setOrder($row->section_order)
                    ;
            $sections[] = $section;
        }
        return $sections;
    }

}
