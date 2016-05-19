<?php

class Application_Model_UserMapper {

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
            $this->setDbTable('Application_Model_DbTable_Users');
        }
        return $this->_dbTable;
    }

    
    public function save(Application_Model_User $user) {
        $data = array( 
            'user_id' => $user->getId(),
            'user_username' => $user->getUsername(),
            'user_password' => $user->getPassword(),
            'user_password_salt' => $user->getPasswordSalt(),
            'user_register_datetime' => date('Y-m-d H:i:s'),
            'user_last_login_datetime' => $user->getLastLoginDatetime(),
            'user_email' => $user->getEmail(),
            'user_name' => $user->getName(),
            'user_last_name' => $user->getLastName(),
            'user_role' => 'member',
            'user_active' => 0
        );
        if (null === ($id = $user->getId())) {
            unset($data['user_id']);
            $this->getDbTable()->insert($data); // array
        } else {
            $this->getDbTable()->update($data, array('user_id = ?' => $id)); // array
        }
    }
    
    public function getByUsername($username) {
        $db = new Common_();
    }


    public function fetchOne($id, Application_Model_User $user) { // Zwraca obiekt z bazy danych
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current(); // current zmienia Rowset na Row
            $user->setId($row->user_id)
                 ->setUsername($row->user_username)
                 ->setPassword($row->user_password)
                 ->setPasswordSalt($row->user_password_salt)
                 ->setRegisterDatetime($row->user_register_datetime)
                 ->setLastLoginDatetime($row->user_last_login_datetime)
                 ->setEmail($row->user_email)
                 ->setName($row->user_name)
                 ->setLastName($row->user_last_name)
                 ->setRole($row->user_role)
                 ->setActive($row->user_active)
            ;
        return $user;
    }

    public function fetchAll($where) { // Zwraca tablicę obiektów z bazy danych
        $resultSet = $this->getDbTable()->fetchAll($where);
        $users = array();
        foreach ($resultSet as $row) {
            $user = new Application_Model_User();
            $user->setId($row->user_id)
                 ->setUsername($row->user_username)
                 ->setPassword($row->user_password)
                 ->setPasswordSalt($row->user_password_salt)
                 ->setRegisterDatetime($row->user_register_datetime)
                 ->setLastLoginDatetime($row->user_last_login_datetime)
                 ->setEmail($row->user_email)
                 ->setName($row->user_name)
                 ->setLastName($row->user_last_name)
                 ->setRole($row->user_role)
                 ->setActive($row->user_active)
            ;
            $users[] = $user;
        }
        return $users;
    }

}
