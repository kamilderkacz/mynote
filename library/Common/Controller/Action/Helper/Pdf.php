<?php
class Common_Controller_Action_Helper_Pdf extends Zend_Controller_Action_Helper_Abstract {

    public function direct( Common_Db_Adapter_ListResult $oResult, $sLayout) {
        $oDomPdf = new Other_Print_Dompdf();
        //$this->_oDomPdf->set_paper('letter','portrait');
        $oDomPdf->set_paper('letter','landscape');
		$oDomPdf->load_html( iconv("UTF-8","CP1250", $this->_generateContent( $oResult, $sLayout ) ));
		$oDomPdf->render();
		$oDomPdf->stream("raport_".time().".pdf");
    }

    private function _generateContent( $oResult, $sLayout) {
        $oView = new Zend_View();
        $sLayout = $sLayout;
        $oView->oData = $oResult;
        $sFile = basename($sLayout);
        $sPath = dirname($sLayout);
        $oView->setScriptPath( $sPath );
        return $oView->render( $sFile );
    }
}
