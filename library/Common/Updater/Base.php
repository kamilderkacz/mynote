<?php
/**
 * Bazowa klasa obsługująca wersjonowanie/aktualizowanie.
 * Jej zadaniem jest tylko zainicjalizowanie adaptera do domyśnej
 * bazy danych. Przy dziedziczeniu z tej klasy konieczne jest
 * również zaimplementowanie interface'u @see Common_Updater_Interface
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage updater
*/
abstract class Common_Updater_Base {
	
	/**
	 * Konfiguracja aplikacji.
	 * 
	 * @var Zend_Config
	 */
	protected $_oConfig;
	
	/**
	 * Adapter do bazy danych brany z domyślnych ustawień
	 * w pliku konfiguracyjnym
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_oDBAdapter;
	
	/**
	 * Prefix tabel z pliku konfiguracyjnego.
	 * 
	 * @var string
	 */
	protected $_sPrefix;
	
	/**
	 * Wykonuje wiele zapytań SQL'owych.
	 *
	 * @param array $aSql
	 */
	protected function _execScripts(array $aSql) {
		$oConnection = $this->_oDBAdapter->getConnection();
        $bBug = false;
		foreach ($aSql as $sSql) {
			try {
				$oConnection->exec($sSql);
			} catch (Exception $e) {
				switch ($this->_oConfig->db->adapter) {
					case 'PDO_PGSQL':
							if (!stristr($e->getMessage(), '42P07')) {
								throw new Exception($e->getMessage());
                                $bBug = true;
							}
						break;
					default:
							throw new Exception($e->getMessage());
                            $bBug = true;
						break;
				}
			}
		}
        if ($bBug) {
            throw new Exception("błąd");
        }
	}
	
	public function __construct() {
		$this->_oConfig = Zend_Registry::get(REGISTRY_CONFIG);
		$this->_oDBAdapter = Zend_Db::factory($this->_oConfig->db->adapter, $this->_oConfig->db->config->toArray());
		if ( $this->_oConfig->db->adapter == 'PDO_MYSQL') {
			$this->_oDBAdapter->query("SET NAMES 'utf8'");
			$this->_oDBAdapter->query("SET CHARACTER SET utf8");
		}
		$this->_sPrefix = $this->_oConfig->db->prefix;
	}

	/**
	 * @see Common_Updater_Interface::update()
	 */
	public function update() {
		switch ($this->_oConfig->db->adapter) {
			case 'PDO_PGSQL':
					$this->_updatePGSQL();
				break;
			default:
					$this->_updateMySQL();
				break;
		}
	}
    /**
     * Definicja metody zawiera szereg zmian, najczęściej w bazie danych.
     * Wykonuje się dla adaptera PostgreSQL
     */
	abstract protected function _updatePGSQL();
    /**
     * Definicja metody zawiera szereg zmian, najczęściej w bazie danych.
     * Wykonuje się dla adaptera MySQL
     */
	abstract protected function _updateMySQL();
}