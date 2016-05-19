<?php
/**
 * Baza kontrolera aktualizującego strukturę bazy danych.
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage updater
*/
abstract class Common_Updater_Controller extends Zend_Controller_Action {

/**
 * Określa, czy wynik działania indexAction() ma zostać
 * zamknięty w znacznikach <html></html>
 *
 * @var bool
 */
	protected $_bAddHTMLEnvelope = true;

	/**
	 * Nazwa aplikacji do wyświetlenia w tytule wygenerowanej strony.
	 *
	 * @var string
	 */
	protected $_sApplicationName = 'Updater';

	protected function _addHeader() {
		if ($this->_bAddHTMLEnvelope) {
			echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"'
					. ' "http://www.w3.org/TR/html4/loose.dtd">'
					. "\n<html>\n<head>"
					. "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>'
					. "\n<title>Updater - {$this->_sApplicationName}</title>\n</head>\n<body>";
		}
	}

	protected function _addFooter() {
		if ($this->_bAddHTMLEnvelope) {
			echo "\n</body>\n</html>";
		}
	}

	/**
	 * Inicjalizuje klasę managera wersji.
	 */
	public function init() {
		$oFrontCtrl = $this->getFrontController();
		$oFrontCtrl->setParams(array('noViewRenderer' => true, 'neverRender' => true));
		parent::init();
	}

	/**
	 * Wykonuje aktualizację aplikacji poprzez wykonanie metod
	 * Common_Updater_Interface::update() w klasach aktualizacyjnych.
	 * Wynik wykonania aktualizacji zwracany jest do buforu wyjściowego
	 * z wykorzystaniem echa. Log operacji znajduje się w <div>
	 * o id="updater".
	 *
	 */
	public function indexAction() {
    $bException = false;
    if ($this->getRequest()->getParam('all',0)) {
      $this->_dropAllTables();
    }
		$this->_addHeader();
		$oFrontCtrl = Zend_Controller_Front::getInstance();
    $aModules = $oFrontCtrl->getControllerDirectory();
    asort($aModules);
		foreach( $aModules as $key => $val) {
			$sClass =  ucwords($key) . '_Model_Setup_Updater';
			error_reporting(E_STRICT);
			try {
				if (class_exists($sClass, true)) {
					$oObj = new $sClass( $key );
					if ($oObj instanceof Common_Updater_Mgr) {
						error_reporting(E_ALL | E_STRICT);
						$oObj->convert();
					}
				}
			} catch (Exception $e) {
                if ($e->getCode() != 0 ) {
                    echo $e->getMessage();
                }
			}
			error_reporting(E_ALL | E_STRICT); 
		}
		$this->_addFooter();
	}

  private function _dropAllTables() {
    $dbAdapter = Zend_Db_Table::getDefaultAdapter();
    $sql = "SHOW tables";
    $oTables = $dbAdapter->fetchAll($sql);
    $dbAdapter->query('SET FOREIGN_KEY_CHECKS=0;');
    foreach( $oTables as $oTable) {
      $result = array_values($oTable);
      $dbAdapter->query('drop table  `' . $result[0] . '`;');
    }
    $dbAdapter->query('SET FOREIGN_KEY_CHECKS=1;');
  }
}