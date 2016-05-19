<?php
/**
 * Klasa bazowa dla operacji, znajdujacych sie pomiedzy kontrolerami a logiką.
 * Posiada podstawowe metody wspomagające kodowanie operacji. Co ułatwia i przyspiesza programowanie.
 * Klasa jest swojego rodzaju kontenerem dla powtarzających się części.
 * @todo powinna być abstrakcyjna i należy ją rozszerzyć do wzorca BRIDGE
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage operation
 */
class Common_Operation_Abstract {

  protected $_oConfig = null;
  protected $_aSearchColumns = array();

  protected function _init() {
    $oRegistry = Zend_Registry::getInstance();
    $this->_oConfig = $oRegistry->get(REGISTRY_CONFIG);
  }

    protected function setSearchColumns(array $aSC){
          $this->_aSearchColumns = $aSC;
    }

    public function getSearchColumns(){
        return $this->_aSearchColumns;
    }
  
  /**
   * Kontener singletonów typu tabela Zend_Db_Table
   * @var array
   */
  static private $_aObjTab = array();

  
  static public function clearTable() {
      self::$_aObjTab = array();
  }
  
  
  
  /**
   * @param string $sClassName - nazwa klasy typu Zend_Db_Table
   * @return Zend_Db_Table - obiekt typu będący instancją Zend_Db_Table
   */
  protected function _initTable($sClassName) {
    if (!isset($this->_aObjTab[$sClassName]) || ($this->_aObjTab[$sClassName] == null)) {
      $this->_aObjTab[$sClassName] = new $sClassName();
    }
    return $this->_aObjTab[$sClassName];
  }

    /** Metoda magiczna odpowiedzialna za tworzenie nieistniejących obiektów tabel.
     * Ma ona na celu przyspieszenie pisania i standaryzację kodu.
     * @param $name
     * @param $arguments
     * @throws Exception
     * @return \Zend_Db_Table <type>
     */
  public function __call($name, $arguments) {
    if (preg_match('/^_get([A-Z]{1}[a-z]*)([A-Z]{1}(.*))Tab$/', $name, $aArr)) {
      return $this->_initTable($aArr[1] . '_Model_Table_' . $aArr[2]);
    }
    throw new Exception("Nie ma takiej metody -> $name, " . print_r($aArr, 1));
  }

  public function eventCheck($oObj, $sFncName, $aArgs) {
    if (is_null($this->_oConfig)) {
      $this->_init();
    }
    $sClass = get_class($oObj);
    if (isset($this->_oConfig->event->$sClass->$sFncName)) {
      foreach ($this->_oConfig->event->$sClass->$sFncName as $oEvent) {
        $observer = new $oEvent->class;
        call_user_func_array(array($observer, $oEvent->fnc), $aArgs);
      }
    }
  }

  protected function _debug($aIn, $aFields) {
    $aResult = array();
    foreach ($aFields as $key) {
      if (key_exists($key, $aIn)) {
        $aResult[$key] = $aIn[$key];
      }
    }
    return $aResult;
  }

        }
