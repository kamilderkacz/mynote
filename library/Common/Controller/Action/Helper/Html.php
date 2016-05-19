<?php
/* Heper, który na podstawie danych tworzy HTML i wysyła na wyjście.
 * Dane wejściowe są przesyłane do widoku, który jest renderowany na wyjście.
 * @author Adam Nielski
 * @copyright BAC
 * @version 1.0
 * @package common
 * @subpackage Controller
 */
class Common_Controller_Action_Helper_Html extends Zend_Controller_Action_Helper_Abstract {
    /**
     * Metoda wołana przez mechanizmy Zend'a.
     * Od razu tworzy widok na podstawie podanego layoutu - pliku phtml i łączy z danymi.
     * @param Common_Db_Adapter_ListResult $oResult - dane wejściowe
     * @param string $sLayout - oprogramowany plik phtml
     */
    public function direct( Common_Db_Adapter_ListResult $oResult, $sLayout) {
        $oView = new Zend_View();
        $sLayout = $sLayout;
        $oView->oData = $oResult;
        $sFile = basename($sLayout);
        $sPath = dirname($sLayout);
        $oView->setScriptPath( $sPath );
        echo $oView->render( $sFile );
    }
}
