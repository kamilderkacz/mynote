<?php

/**
 * Klasa odpowiedzialna za budowanie zapytań SQL na podstawie konfiguracji.
 * Konfiguracja odbywa się za pomocą obiektu, który posiada metody konfigurujące podstawowe zapytanie SQL.
 * A także za pomocą pluginów, które można dodać do obiektu.
 * Uruchomienie obiektu odbywa sięza pomocą metody execute zwracającą wyniki zapytania.
 * Metoda ta najpier uruchamia dodane pluginy do obiektu - które wykonują różne operacje na nim samym, a następnie składa i wykonuje zapytanie SQL.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
class Common_Grid_Builder implements Common_Grid_Interface {
  const METHOD_COUNT = 'count';

  private $_oDelegate;
  private $_aPlugins = array();
  private $_aSQL = array();
  private $_oSelect = null;
  private $_oSelectCount = null;

  /**
   * Inicjalizacja poprzez obiekt.
   * @param Common_Grid_Delegate_Interface $oDelegateMethod
   */
  public function __construct(Common_Grid_Delegate_Interface $oDelegateMethod) {
    $this->_oDelegate = $oDelegateMethod;
    $this->addSelectMethod($this->_oDelegate->getMainSelect());
  }

  private function _buildSelects() {
    $this->_executePlugins();
    if (is_null($this->_oSelect)) {
      $oAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
      $oSelect = new Zend_Db_Select($oAdapter);
      $oCountSelect = new Zend_Db_Select($oAdapter);

      $bCount = false;
      foreach ($this->_aSQL as $aSQL) {
        foreach ($aSQL as $sMethod => &$val) {
          if (isset($val[self::METHOD_COUNT])) {
            $bCount = true;
            $count = array($val[self::METHOD_COUNT]);
            unset($val[self::METHOD_COUNT]);
          } else {
            $count = null;
          }
          //select
          call_user_func_array(array($oSelect, $sMethod), $val);
          //counter
          if ($sMethod != 'order' AND $sMethod != 'limitPage') {
            if ($sMethod == 'from') {
              $valFrom = $val;
              if ($count) {
                $valFrom['fields'] = $count;
                $oCountSelect->distinct(false);
              }
              //$valFrom['fields'] = array('COUNT(*)');
              call_user_func_array(array($oCountSelect, $sMethod), $valFrom);
            } else {
              $valFrom = $val;
              $valFrom['fields'] = array(array());
              call_user_func_array(array($oCountSelect, $sMethod), $valFrom);
            }
          }
        }
      }
      if ($bCount) {
        $this->_oSelect = $oSelect;
        $this->_oSelectCount = $oCountSelect;
      } else {
        $oCountSelectSec = new Zend_Db_Select($oAdapter);
        $oCountSelectSec->from($oCountSelect, array( new Zend_Db_Expr('COUNT(*) AS ' . Zend_Paginator_Adapter_DbSelect::ROW_COUNT_COLUMN)));
        $this->_oSelect = $oSelect;
        $this->_oSelectCount = $oCountSelectSec;
      }
    }
  }

  public function getSelect() {
    $this->_buildSelects();
    //echo $this->_oSelect->assemble(); exit;
    return $this->_oSelect;
  }

  public function getSelectCount() {
    $this->_buildSelects();
    return $this->_oSelectCount;
  }

  /**
   * Dodaje specjalną tablicę konfiguracyjną do zapytania.
   * Jej specyfikacja to array( <metoda SELECTA> => array( kolejne parametry wybranej metody ) )
   * @param array $aSQLData
   */
  public function addSelectMethod(array $aSQLData) {
    $this->_aSQL = array_merge($this->_aSQL, $aSQLData);
  }

  /**
   * Dodaje pluginy realizujące odpowiedni interfejs.
   * @param Common_Grid_Plugin_Interface $oObj
   */
  public function addPlugin(Common_Grid_Plugin_Interface $oObj) {
    $this->_aPlugins[] = $oObj;
  }

  private function _executePlugins() {
    foreach ($this->_aPlugins as $oPlugin) {
      $oPlugin->execute($this);
    }
  }

  private function _execute() {
    $this->_buildSelects();
    return $this->_makeListResult($this->_oSelect, $this->_oSelectCount);
  }

  /**
   * Uruchomienie pluginów i głównej metody.
   * START zapytania SQL
   * @return Common_Db_ListResult - wyniki zapytania
   */
  public function execute() {
    return $this->_execute();
  }

  protected function _makeListResult($oListSelect, $oCountSelect) {
    //echo $oListSelect->assemble(); exit;
    //throw new Exception($oListSelect->assemble());
    $result = new Common_Db_ListResult();
    $result->aList = $oListSelect->query(Zend_Db::FETCH_ASSOC)->fetchAll();
    $result->iCount = $oCountSelect->query(Zend_Db::FETCH_ASSOC)->fetchColumn();
    return $result;
  }

}