<?php

/* Klasa bazowa dla kontrollerów obsługujących gridy/listy
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */

abstract class Common_Grid_Controller extends Common_Controller_NoView {

  /** Oddelegowanie operacji logicznych.
   * @return Common_Grid_Operation_Interface - warstwa obsługi operacji grida o zdefniowanych metodach.
   */
  abstract protected function _getOperation();

  /**
   * Akcja pobierająca listę danych dla grida na podstawie parametrów.
   */
  public function getAction() {
    try {
      $aParams = Common_Request_Permission::in2array();
      Common_Response_Grid::out($this, new Common_Db_Adapter_ListResult($this->_get($aParams)));
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  protected function _get(array $aParams) {
    return $this->_getOperation()->getList($aParams);
  }

  public function filtersAction() {
    try {
      Common_Response_Filter::out($this, array($this->_getOperation()->getFilters(), 'success' => 'true'));
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

}
