<?php
/**
 * Filtr - wykonuje where na builderze.
 * Możliwość podania pola, operacji, wartości
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
class Common_Grid_Filter_Filter_Value implements Common_Grid_Filter_Filter_Interface {
    //parametry wejściowe
    const VALUE = 'value';//wartość porównywana
    const OPERATION = 'operation';//wybrana operacja
    //parametry definiowane
    const NAME = 'name';//nazwa filtru
    const OPERATIONS = 'operations';//możliwe operacje
    const FIELD = 'field';//naza pola
    //możliwe operacje dla tego filtra
    const EQUAL = '1';//równe
    const GREATER = '2';//mniejsze
    const SLIGHTER = '3';//większe
    const CONTAIN = '4';//zawiera
    const MISCELLANEOUS_AND_NULL = '5';//różne
    //wybór komponentu po stronie UI
    const COMPONENT = 'component';

    static protected $_oPermission;
    static protected $_oConfig = null;
    
    private $_aParams;

    public function __construct( array $aParams = null ) {
        $this->_aParams = $aParams;
        if (!is_object(self::$_oPermission)) {
            $oRegistry = Zend_Registry::getInstance();
            self::$_oConfig = $oRegistry->get(REGISTRY_CONFIG);
            if (self::$_oConfig->uodo->enable) {
                self::$_oPermission = new self::$_oConfig->permission->response;//@todo zastanawiam się na filter choć to rownoznaczne z response
            }
        }
    }

    public function setBuilder( $oObj ) {
        $this->_build( $oObj );
    }

    protected function _build( Common_Grid_Interface $oObj ) {
        $sField = $this->_aParams[self::FIELD];
        $sValue = $this->_aParams[self::VALUE];
        $sOperation = $this->_aParams[self::OPERATION];
        if (!empty($sValue)) {
            $oObj->addSelectMethod( array( array( 'where' => array("{$this->_getOperation($sField,$sOperation,$sValue)}") ) ) );
        }
    }

    private function _getOperation( $sField,$sOperation, $sValue ) {
        //@todo tu moze byc problem z apostrofami na PG bo nie znamy typu i nie wiadomo czy uzyc apostrofow czy nie!
        switch ($sOperation) {
            case self::EQUAL:
                    return "$sField = '$sValue'";
                break;
            case self::GREATER:
                    return "$sField < '$sValue'";
                break;
            case self::SLIGHTER:
                    return "$sField > '$sValue'";
                break;
            case self::CONTAIN:
                    return "$sField LIKE '%$sValue%'";
                break;
            case self::MISCELLANEOUS_AND_NULL:
                    return "$sField <>'$sValue' or $sField is null";
                break;
            default:
                throw new Exception("Niedozwolona operacja!");
                break;
        }
    }

    public function setField( $vVal ) {
        $this->_aParams[self::FIELD] = $vVal;
        return $this;
    }

    public function setName( $vVal ) {
        $this->_aParams[self::NAME] = $vVal;
        return $this;
    }

    public function setComponent( $vVal ) {
        $this->_aParams[self::COMPONENT] = $vVal;
        return $this;
    }

    public function addOperation( $sName, $iOperation ) {
        $this->_aParams[self::OPERATIONS][$iOperation] = $sName;
        return $this;
    }

    public function toArray() {
        return $this->_aParams;
    }

    public function setSchema( &$aResult ) {
        if ($this->_checkParmission()) {
            $aResult[] = array( 'name' => get_class($this), 'values' => $this->toArray() );
        }
    }
    private function _checkParmission() {
        if ((self::$_oConfig->uodo->enable) ||
            (self::$_oPermission->checkPermission($this->_aParams[self::FIELD], Acl_Model_Table_ZasobTyp::FIELD_READ))) {
            return true;
        }
        return false;
    }
}