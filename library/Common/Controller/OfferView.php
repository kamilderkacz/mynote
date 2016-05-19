<?php
/* Klasa bazowa dla kontrollerów aplikacji BAC - inicjalizuje globalne zmienne.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_OfferView extends Zend_Controller_Action {
    /**
     * Bazowy url aplikacji pobierany z requestu.
     *
     * @var string
     */
    protected $_sBaseUrl;
    /**
     * Instancja konfiguracji aplikacji.
     *
     * @var Zend_Config
     */
    protected $_oConfig;
    /* Nadpisana metoda bazowego kontrollera względem tej klasy.
     * Inicjalizuje podstawowe zmienne.
     * a) Konfigurację zapisaną w konfigu - pobierając ją z rejestru.
     * b) Ustawia baseUrl na widoku.
     */
    public function init() {
        $oRegistry = Zend_Registry::getInstance();
        $this->_oConfig = $oRegistry->get(REGISTRY_CONFIG);
        $this->_setViewParam();
		//przy braku mod_rewrite trzeba podawać inną niż baseUrl bazową ścieżkę dla plików JS i CSS
		//$this->view->staticFilesBaseUrl = $this->_oConfig->server->staticFilesPath;
        parent::init();
    }

    protected function _addPaginator($oOperation)
    {
        //dodaj standardowe metody grida
//        list($sDBSort, $sDBOrder) = $this->_getDatabaseSort();
//        $oOperation->setSort($sDBSort);
//        $oOperation->setOrder($sDBOrder);
//        $oOperation->setSearch($this->_getDatabaseSearch());
        $oOperation->init();
        //grid paginowany
        $page = $this->getRequest()->getParam('page', 1);
//        $iResultLimit =  $this->_getDatabaseResultLimit();
        if(method_exists($oOperation,'pageLimit')){
            $iResultLimit = $oOperation->pageLimit();
        }
        $adapter = new Zend_Paginator_Adapter_DbSelect($oOperation->getSelect());
        $adapter->setRowCount($oOperation->getSelectCount());
        $paginator = new Zend_Paginator($adapter);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($iResultLimit);
        if($paginator->count() == 0){
            Message_Operation_Flash::setMsg('Brak rekordów', Message_Operation_Flash::LEVEL_DANGER);
        }
        $this->view->paginator = $paginator;
        //config
        $this->view->config_url = $this->_oConfig->url;
    }
    
    protected function _setViewParam() {
        $this->_sBaseUrl = $this->_request->getBaseUrl();
        $this->view->baseUrl = $this->_sBaseUrl;
    }
}