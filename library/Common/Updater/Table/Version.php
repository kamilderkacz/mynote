<?php
/**
 * Zarządza wersjonowaniem w bazie danych. W przypadku
 * braku odpowiedniej struktury tworzy tabelę zawierającą: 
 * identyfikator, numer wersji w postaci łańcucha znaków,
 * liczby całkowitej oraz datę aktualizacji
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage updater
 */
class Common_Updater_Table_Version extends Common_Db_Table_Abstract {
	/**
	 * Nazwa tabeli w bazie.
	 * 
	 * @var string
	 */
	protected $_name = 'version_app';
	
	/**
	 * Nazwa klasy obsługującej wiersze tabeli jako obiekty.
	 * 
	 * @var string
	 */
	protected $_rowClass = 'Common_Updater_Row_Version';
	
	/**
	 * Tworzy tabelę wersji w bazie danych, w przypadku jej braku.
	 */
	protected function _createTable() {
		//@todo niezbyt ajne rozwiazanie uzycie tu konfiga!!!
		$registry = Zend_Registry::getInstance();
		$oConfig = $registry->get(REGISTRY_CONFIG);
		$oDBAdapter = $this->getAdapter();
		switch ($oConfig->db->adapter) {
			case 'PDO_PGSQL':
					$sSql = "CREATE SEQUENCE {$this->_name}_id_seq;";
					$oDBAdapter->query( $sSql );
					$sSql = "CREATE TABLE {$this->_name} (
							id integer PRIMARY KEY default nextval('{$this->_name}_id_seq'),
							version character varying(45) NOT NULL,
							int_version bigint NOT NULL,
							module character varying(45),
							create_date timestamp without time zone DEFAULT now() NOT NULL
							);";
					$oDBAdapter->query( $sSql );
				break;
			default:
					$sSql = "CREATE TABLE `{$this->_name}` (" . "\n"
					. "`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, " . "\n"
					. "`version` VARCHAR(45) NOT NULL, " . "\n"
					. "`int_version` INTEGER UNSIGNED NOT NULL, " . "\n"
					.	"`module` VARCHAR(45) NULL, " . "\n"
					. "`create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, " . "\n"
					. "PRIMARY KEY (`id`) " . "\n"
					. ",INDEX `IDX_version` (`version`) " . "\n"
					. ",INDEX `IDX_int_version` (`int_version`) " . "\n"
					. ") ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_polish_ci;";
					$oDBAdapter->query( $sSql );
				break;
		}
	}
	
	/**
	 * Inicjalizuje managera tabeli. W przypadku, gdy w bazie nie ma
	 * wymaganej tabeli wykonuje skrypt tworzący odpowiednią strukturę.
	 */
	public function __construct($aConfig = array()) {
		// przepisanie wartości nazwy bazy danych ze względu na fakt,
		// iż konstruktor Common_Mgr_Base powoduje nadpisanie
		// tej wartości poprzez dołączenie do niej prefiksu.
		try {
			parent::__construct($aConfig);
		} catch (Exception $e) {
			$sTableName = $this->_name;
			$this->_createTable();
			$this->_name = $sTableName;
			//parent::__construct($aConfig);
		}
	}
	
	/**
	 * Dodaje nowy wpis o numerze wersji.
	 * 
	 * @param string $sVersion
	 */
	public function addVersion($sVersion, $sModule) {
		$oRow = $this->fetchNew();
		$oRow->version = $sVersion;
		$oRow->module = $sModule;
		$oRow->int_version = $this->convertToIntVersion($sVersion);
		$oRow->save();
	}
	
	/**
	 * Zamienia numer wersji z postaci "xx.yy.zz"
	 * na postać liczby całkowitej, dodając do każdej
	 * części numeru wersji 4 zera wiodącę, 
	 * np.:
	 * dla "0.0.1" zwróci 1
	 * dla "1.12.3" zwróci 100120003
	 * 
	 * @param string $sVersion
	 * @return int
	 */
	public function convertToIntVersion($sVersion) {
		$aVersions = explode('.', $sVersion);
		if (count($aVersions) != 3) {
			throw new Exception('Podany łańcuch znaków nie jest numerem wersji.');
		}
		return intval(sprintf('%04d%04d%04d', $aVersions[0], $aVersions[1], $aVersions[2]));
	}

	private function _getCurrentVersion( $sModule ) {
		$sResult = null;
		$oRow = $this->fetchRow("module='".$sModule."'", 'int_version DESC');
		if ($oRow != null) {
			$sResult = $oRow->version;
		}	
		return $sResult;	
	}	
	/**
	 * Zwraca obecną wersję bazy danych.
	 * 
	 * @return string W przypadku braku wersji zwraca null.
	 */
	public function getCurrentVersion( $sModule ) {
		try {
			$sResult = $this->_getCurrentVersion( $sModule );
		} catch (Exception $e) {
			$sTableName = $this->_name;
			$this->_createTable();
			$this->_name = $sTableName;
			$sResult = $this->_getCurrentVersion( $sModule );
		}
		return $sResult;
	}
}