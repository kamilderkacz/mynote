<?php

/**
 * Walidator - zrobiony po to, aby poprawnie wyświetlał błąd pod inputem
 */
require_once 'Zend/Validate/Abstract.php';


class My_Validator_multipleEntry extends Zend_Validate_Abstract
{
    const USERNAME = 'usernameDuplication';
    const EMAIL = 'emailDuplication';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::USERNAME   => "Ten użytkownik już istnieje.",
        self::EMAIL => "W systemie istnieje już konto o tym e-mailu. Wybierz inny" //"'%value%' does not appear to be a float",
    );

    protected $toCheck;
    
    /**
     * Constructor for the validator
     */
    public function __construct($toCheck) {
        $this->setToCheck($toCheck);
    }
    public function setToCheck($value) {
        $this->toCheck = (string) $value;
        return;
    }
    public function getToCheck() {
        return $this->toCheck;
    }

    // Tu walidacja.
    public function isValid($value)
    {
        $toCheck = $this->getToCheck();
        switch ($toCheck) {
            
            case 'username' :
//                try {
                $userMapper = new Application_Model_UserMapper();
                $tUser = $userMapper->getDbTable();
                $select = $tUser->select()
                                ->from(array('u'=>'users'))
                                ->where('u.user_username = ?', $value); // ! tak piszemy warunki !
                $row = $tUser->fetchRow($select);
//                } catch (Zend_Db_Exception $e) {
//                    echo "<pre>";print_r($e);
//                 }
                if($row == null) {
                    return true;
                } else {
                    $this->_error(self::USERNAME);
                    return false;
                }
            break;
            case 'email' :
                $userMapper = new Application_Model_UserMapper();
                $tUser = $userMapper->getDbTable();
                $select = $tUser->select()
                                ->from(array('u'=>'users'))
                                ->where('u.user_email = ?', $value); // ! tak piszemy warunki !
                $row = $tUser->fetchRow($select);
                if($row == null) {
                    return true;
                } else {
                    $this->_error(self::EMAIL);
                    return false;
                }
            break;
            default: 
                return false;
        }
        
        

        $this->_setValue($value);
//        try {
//            if (!Zend_Locale_Format::isFloat($value, array('locale' => $this->_locale))) {
//                $this->_error(self::NOT_FLOAT);
//                return false;
//            }
//        } catch (Zend_Locale_Exception $e) {
//            $this->_error(self::NOT_FLOAT);
//            return false;
//        }

        return true;
    }
}
