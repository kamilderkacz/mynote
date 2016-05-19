<?php
/* Heper, który na podstawie danych tworzy specyficzny string na wyjściu.
 * String w postaci wierszy=row typu "value, value, value[enter]".
 * @author Adam Nielski
 * @copyright BAC
 * @version 1.0
 * @package common
 * @subpackage Controller
 */
class Common_Controller_Action_Helper_Print extends Zend_Controller_Action_Helper_Abstract {

    private function _array2stream( $aData, $aFields ) {
        foreach($aData as $key => $value) {
            $this->_print( $aRow, $aFields );
        }
    }

    private function _object2stream( $oData, $aFields ) {
        foreach($oData as $key => $aRow) {
            $this->_print( $aRow->toArray(), $aFields );
        }
    }

    private function _print( $aRow, $aFields ) {
        if ( is_array($aFields) and count($aFields)>0) {
            $aResult = array();
            foreach($aFields as $valKey) {
                if (array_key_exists($valKey, $aRow)) {
                    $aResult[] = $aRow[$valKey];
                }
            }
            echo implode(' ',$aResult);
        } else {
            echo implode(' ',$aRow);
        }
        echo "\n";
    }
    /**
     * Metoda wołana przez mechanizmy Zend'a.
     * Wypisuje dane w ustalonym formacie.
     * @param mixed $vData - array lub obiekt typu Common_Db_Row_Abstractset
     * @param array $aFields - pola, które mają być przesyłane na wyjście
     */
    public function direct( $vData, $aFields = array() ) {
        if (is_array($vData)) {
            $this->_array2stream( new ArrayIterator($vData), $aFields );
        } elseif (is_object($vData) and $vData instanceof Common_Db_Row_Abstractset) {
            $this->_object2stream( $vData, $aFields );
        } else {
            throw new Exception("Format nie obsługiwany!");
        }
    }
}
