<?php
/* Heper, który na podstawie danych tworzy plik CSV i wysyła na wyjście.
 * @author Adam Nielski
 * @copyright BAC
 * @version 1.0
 * @package common
 * @subpackage Controller
 */
class Common_Controller_Action_Helper_Csv extends Zend_Controller_Action_Helper_Abstract {
    /**
     * Metoda wołana przez mechanizmy Zend'a
     * @param Common_Db_Adapter_ListResult $oResult - dane wejściowe
     */
	public function direct( Common_Db_Adapter_ListResult $oResult) {
        $this->_generateXLS( $oResult->results );
	}

	/** Metoda generuje plik XLS który można zapisać na dysk komputera
	 *
	 * @param array $aRecords
	 * @param array $aHeaders
	 * @param string $sFilename
	 */
	private function _generateXLS($aRecords, $aHeaders = array(), $sFilename = "raport.csv") {
		header("Expires: 0");
		header("Cache-control: private");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=" . $sFilename);
		if (sizeof($aHeaders) == 0 and count($aRecords)>0) {
			$aHeaders = array_keys($aRecords[0]);
		}
		if (count($aRecords)>0) {
			$this->_write('php://output', ';;', array_merge(array($aHeaders), $aRecords));
		}
	}

	/**
	 * Metoda zapisuje do pliku wygenerowany plik csv z podanych rekordów
	 *
	 * @param string $sFilename
	 * @param string $sDelimiter
	 * @param array $aRecords
	 */
	private function _write($sFilename, $sDelimiter = ';;', $aRecords) {
		$aResults = array();
		foreach ($aRecords as $aValue) {
			$aResults[] = html_entity_decode(preg_replace('/\<br(\s*)?\/?\>/i', "\n", iconv("UTF-8", "windows-1250//IGNORE", implode($sDelimiter, $aValue))));
		}
		$fp = fopen($sFilename, 'w');
		foreach ($aResults as $sResult) {
			fputcsv($fp, explode($sDelimiter, $sResult), ';');
		}
		fclose($fp);
	}
}
