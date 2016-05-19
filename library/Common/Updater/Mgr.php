<?php
/** Menadzer aktualizacji.
 *
 * Wykonuje aktualizacje dla wybranego modułu.
 * Moduł powinien mieć zdefiniowany katalog aktualizacji i posiadać zdefiniowane klasy upgradu.
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage updater
*/
abstract class Common_Updater_Mgr {
	protected $_aUpdateCases = array();
	protected $_oVersionTable;
	protected $_sModule;
    /**
     *
     * @param string $sModule nazwa modułu
     */
	public function  __construct( $sModule ) {
		$this->_sModule = $sModule;
		$this->_oVersionTable = new Common_Updater_Table_Version();
	}

	/**
	 * Sortuje klasy update'ujące bazę danych oraz konwertuje
	 * tablicę Common_Updater_Controller::_aUpdateCases z tablicy
	 * zawierającej nazwy klas do tablicy zawierającej klucz, będący
	 * numeryczną reprezentacją numeru wersji i wartość w postaci
	 * obiektu. Przy sortowaniu aktualizacji,
	 *
	 * @param int $iCurrentVersion
	 *
	 */
	protected function _sortUpdateCases($iCurrentVersion) {
		$aSortedUpdateCases = array();
		foreach ($this->_aUpdateCases as $sUpdateCase) {
			$oUpdateCase = new $sUpdateCase();
			if ($oUpdateCase instanceof Common_Updater_Interface) {
				$sVersion = $oUpdateCase->getVersion();
				$iVersion = $this->_oVersionTable->convertToIntVersion($sVersion);
				if ($iVersion > $iCurrentVersion) {
					$aSortedUpdateCases[$iVersion] = $oUpdateCase;
				}
			}
		}
		ksort($aSortedUpdateCases);
		$this->_aUpdateCases = $aSortedUpdateCases;
	}

	public function convert() {
		echo "\n". '<div id="updater">MODUŁ:<b>' . strtoupper($this->_sModule) . "</b>\n";
		$oVersionsTable = $this->_oVersionTable;
		$sCurrentVersion = $oVersionsTable->getCurrentVersion( $this->_sModule );
		$iCurrentVersion = 0;
		if ($sCurrentVersion != null) {
			$iCurrentVersion = $oVersionsTable->convertToIntVersion($sCurrentVersion);
		} else {
			$sCurrentVersion = 'brak danych';
		}

		$this->_sortUpdateCases($iCurrentVersion);
		$iUpdateCasesCount = count($this->_aUpdateCases);
		echo "Obecna wersja aplikacji: <strong>{$sCurrentVersion}</strong><br>\n";
		echo "Dostępna ilość aktualizacji: <strong>{$iUpdateCasesCount}</strong><br>\n";
		if ($iUpdateCasesCount == 0) {
			echo "</div>";
			return; // ----->
		}
		echo "Rozpoczynanie procesu aktualizacji...<br/>";
		$sNewVersion = '';
		foreach ($this->_aUpdateCases as $oUpdateCase) {
			$sNewVersion = $oUpdateCase->getVersion();
			try {
				$oUpdateCase->update();
				$oVersionsTable->addVersion($sNewVersion,  $this->_sModule);
				echo "Zaktualizowano do wersji <strong>{$sNewVersion}</strong><br>\n";
			} catch (Exception $e) {
				echo "Wystąpił błąd podczas aktualizacji <strong>{$sNewVersion}</strong>:<br>\n"
						. "<strong>{$e->getMessage()}</strong><br>\n";
				echo "</div>";
				return; // ------>
			}
		}
		echo "<strong>Wszystkie aktualizacje zostały pomyślnie zakończone.\n</strong><br>\n";
		echo "</div>";
	}

}