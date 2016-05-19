<?php
/**
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage history
 */
class Common_History_Table_History extends Common_Db_Table_Abstract {
    const OPERATION_INSERT = 1;
    const OPERATION_UPDATE = 2;
    const OPERATION_DELETE = 3;
	/**
	 * Nazwa tabeli w bazie.
	 *
	 * @var string
	 */
	protected $_name = 'history';

	/**
	 * Nazwa klasy obsługującej wiersze tabeli jako obiekty.
	 *
	 * @var string
	 */
	protected $_rowClass = 'Common_History_Row_History';

	/**
	 * Inicjalizuje managera tabeli. W przypadku, gdy w bazie nie ma
	 * wymaganej tabeli wykonuje skrypt tworzący odpowiednią strukturę.
	 */
	public function __construct($aConfig = array(), $aAttrs = array()) {
		// przepisanie wartości nazwy bazy danych ze względu na fakt,
		// iż konstruktor Common_Mgr_Base powoduje nadpisanie
		// tej wartości poprzez dołączenie do niej prefiksu.
		try {
			parent::__construct($aConfig, $aAttrs);
		} catch (Exception $e) {
			$sTableName = $this->_name;
			$this->_createTable();
			$this->_name = $sTableName;
			//parent::__construct($aConfig);
		}
	}

	/**
	 * Tworzy tabelę wersji w bazie danych, w przypadku jej braku.
	 */
	protected function _createTable() {
		//@todo niezbyt ajne rozwiazanie uzycie tu konfiga!!!
		$oDBAdapter = $this->getAdapter();
		switch ($this->_oConfig->db->adapter) {
			case 'PDO_PGSQL':
					$sSql = "CREATE SEQUENCE {$this->_name}_history_id_seq;";
					$oDBAdapter->query( $sSql );
					$sSql = "CREATE TABLE {$this->_name} (
							history_id integer PRIMARY KEY default nextval('{$this->_name}_history_id_seq'),
                            history_operation_id int NOT NULL,
							history_change_from TEXT NULL,
                            history_change_to TEXT NULL,
                            history_change TEXT NULL,
                            history_class TEXT NULL,
                            history_data int NOT NULL
							);";
					$oDBAdapter->query( $sSql );
				break;
			default:
					$sSql = "CREATE TABLE `{$this->_name}` (" . "\n"
					. "`history_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, " . "\n"
                    . "`history_operation_id` INTEGER UNSIGNED NOT NULL, " . "\n"
					. "`history_change_from` TEXT NULL, " . "\n"
                    . "`history_change_to` TEXT NULL, " . "\n"
                    . "`history_change` TEXT NULL, " . "\n"
                    . "`history_class` TEXT NULL, " . "\n"
                    . "`history_data` INTEGER, " . "\n"
					. "PRIMARY KEY (`history_id`) " . "\n"
					. ")";
					$oDBAdapter->query( $sSql );
				break;
		}
	}
}