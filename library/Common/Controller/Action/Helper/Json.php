<?php
/* Heper, który na podstawie danych tworzy string JSON'a i wysyła na wyjście.
 * Helper ten nadpisuje helper zenda o tej samej nazwie.
 * @author Adam Nielski
 * @copyright BAC
 * @version 1.0
 * @package common
 * @subpackage Controller
 */
class Common_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Abstract {
    /**
     * Metoda wołana przez mechanizmy Zend'a.
     * Wysyła dane na standardowe wyjście.
     * Do wyjścia dopisywany jest callBack używany w ExtJS potrzebny do identyfikacji requestów przy asynchronicznym odpytywaniu
     * servera np. dla tego samego url'a z innymi parametrami.
     * @todo zmienne total i results powinny być stałymi lubod razu powinien być renderowany obiekt wejściowy.
     * @param Common_Db_Adapter_ListResult $oResult - dane wejściowe
     */
	public function direct( Common_Db_Adapter_ListResult $oResult) {
        $oRequest = $this->getRequest();
		$sCallback = (string) $oRequest->getParam('callback');
		echo $sCallback . '({"total":"' . $oResult->total . '","results":' . json_encode($oResult->results) . '})';
	}
}