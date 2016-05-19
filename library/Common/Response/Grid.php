<?php
/**
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage response
 */
class Common_Response_Grid extends Common_Response_Abstract {
    /** Stała ustawiająca alias otrzymany na wejściu requesta.
     * Odpowiada za wybór wybranego wyjścia np. @see Common_Response_Grid::OUT_TYPE_JSON
     */
    const REQUEST_OUT_PARAM = 'out';
    /* Wartość okreslajaca typ wyjscia dla metody @see _out
     * Typ wyjścia: json
     */
    const OUT_TYPE_JSON = 'json';
    /* Wartość okreslajaca typ wyjscia dla metody @see _out
     * Typ wyjścia: plik csv
     */
    const OUT_TYPE_CSV = 'csv';
    /* Wartość okreslajaca typ wyjscia dla metody @see _out
     * Typ wyjścia: html
     */
    const OUT_TYPE_HTML = 'html';
    /* Wartość okreslajaca typ wyjscia dla metody @see _out
     * Typ wyjścia: plik pdf
     */
    const OUT_TYPE_PDF = 'pdf';

    private $_sLayout  = '../library/Common/Controller/Action/Helper/views/print_grid_layout.phtml';
    
    public function checkPermissionList( Common_Db_Adapter_ListResult $oListResult ) {
        if ( ($oListResult->total > 0) && (self::$_oConfig->uodo->enabled)) {
            $aKeys = array_keys($oListResult->results[0]);
            $aDelKeys = $this->_checkPermission( $aKeys );
            foreach($oListResult->results as $key => $aRow) {
                foreach($aDelKeys as $delKey) {
                    $oListResult->results[$key][$delKey] = '';
                }
            }
        }
        return $oListResult;
    }

    public static function out( Zend_Controller_Action $oController = null, Common_Db_Adapter_ListResult $oListResult ) {
        $oSelf = parent::getInstance( $oController, __CLASS__ );
        $oSelf->_out( $oSelf->checkPermissionList( $oListResult ) );
    }

 /**
 * Na podstawie wybranego helpera wyjścia, przetwarza dane do ostatecznego formatu i wysyła je.
 * @todo metoda do przerobienia - mało elastyczne rozwiązanie.
 * @param Common_Db_Adapter_ListResult $oResult - wyniki/dane do wysłania
 * @param string $helper - helper wyjścia Zend_Controller_Action_Helper_Abstract
 * @param string $sLayout - sciezka do pliku phtml
 */
    public function _out( Common_Db_Adapter_ListResult $oResult ) {
        Zend_Controller_Action_HelperBroker::addPrefix('Common_Controller_Action_Helper');
        if ( $this->_sHelper == null ) {
            $this->_sHelper = Common_Response_Grid::OUT_TYPE_JSON;
        }
        if ( ($sParam = $this->_oRequest->getParam( Common_Response_Grid::REQUEST_OUT_PARAM, null)) && $sParam != null) {
            switch ($sParam) {
                  case Common_Response_Grid::OUT_TYPE_JSON:
                      $oHelper = $this->_oController->getHelper($sParam);
                      $oHelper->direct( $oResult );
                      break;
                  case Common_Response_Grid::OUT_TYPE_CSV:
                      $oHelper = $this->_oController->getHelper($sParam);
                      $oHelper->direct( $oResult );
                      break;
                  case Common_Response_Grid::OUT_TYPE_HTML:
                      $oHelper = $this->_oController->getHelper($sParam);
                      $oHelper->direct( $oResult, $this->_sLayout );
                      break;
                  case Common_Response_Grid::OUT_TYPE_PDF:
                      $oHelper = $this->_oController->getHelper($sParam);
                      $oHelper->direct( $oResult, $this->_sLayout );
                      break;
                  default:
                      throw new Exception('Brak wyjscie -> ' . $sParam);
                      break;
              }
        } else {
            $oHelper = $this->_oController->getHelper($this->_sHelper);
            $oHelper->direct( $oResult );
        }
    }
}