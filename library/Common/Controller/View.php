<?php
/* Klasa bazowa dla kontrollerów aplikacji BAC - inicjalizuje globalne zmienne.
 * @author Adam Nielski
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
 */
class Common_Controller_View extends Zend_Controller_Action {
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
    protected $_columns;
    const DEFAULT_RESULT_LIMIT = 25;
    
    public function init() {
        $oRegistry = Zend_Registry::getInstance();
        $this->_oConfig = $oRegistry->get(REGISTRY_CONFIG);
        $this->_sBaseUrl = $this->_request->getBaseUrl();
        if ($this->view) {
            $this->view->baseUrl = $this->_sBaseUrl;
            //przy braku mod_rewrite trzeba podawać inną niż baseUrl bazową ścieżkę dla plików JS i CSS
            $this->view->staticFilesBaseUrl = $this->_oConfig->server->staticFilesPath;
            $this->view->paginator = new Zend_Paginator(new Common_Paginator_Adapter_Soap(array()));
            $oOperation = new UserSettings_Operation_UserSettings();
            if($this->_columns) {
                try {
                    $oRequest = $this->getRequest();
                    $tablename = $oRequest->getModuleName() . '/' . $oRequest->getControllerName() . '/' . $oRequest->getActionName();
                    $this->view->columns = $oOperation->get($tablename, $this->_columns);
                } catch (Exception $e){
                    Message_Operation_Flash::setMsg($e->getMessage(), Message_Operation_Flash::LEVEL_DANGER);
                    return $this->_helper->redirector->gotoRoute(array(), 'login');
                }
            }
        }
        parent::init();
    }
    
    protected function _setViewParam() {
        $this->_sBaseUrl = $this->_request->getBaseUrl();
        $this->view->baseUrl = $this->_sBaseUrl;
    }
    
    protected function _addPaginator($oOperation)
    {
        //dodaj standardowe metody grida
        list($sDBSort, $sDBOrder) = $this->_getDatabaseSort();
        $oOperation->setSort($sDBSort);
        $oOperation->setOrder($sDBOrder);
        $oOperation->setSearch($this->_getDatabaseSearch());
        $oOperation->init();
        //grid paginowany
        $page = $this->getRequest()->getParam('page', 1);
        $iResultLimit =  $this->_getDatabaseResultLimit();
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

    protected function _addPaginatorWh($oOperation)
    {

        //dodaj standardowe metody grida
//        list($sDBSort, $sDBOrder) = $this->_getDatabaseSort();
        $oOperation->setSort($this->getRequest()->getParam('sort', ''));
        $oOperation->setOrder($this->getRequest()->getParam('order', ''));
        $oOperation->setSearch($this->_getDatabaseSearch());
        $oOperation->setFilter($this->getRequest()->getParams());
        $oOperation->init();
        //grid paginowany
        $page = $this->getRequest()->getParam('page', 1);
        $iResultLimit =  $this->_getDatabaseResultLimit();
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
        
        $this->view->extraParam = $this->getRequest()->getParams();
        //config
        $this->view->config_url = $this->_oConfig->url;
    }

    protected function _addSecPaginator($oOperation)
    {
        $oOperation->init();
        //grid paginowany
        $page = $this->getRequest()->getParam('form_page', $this->getRequest()->getParam('page1', 1) );
        $iResultLimit =  $this->_getDatabaseResultLimit();
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
        $this->view->paginator2 = $paginator;
        //config
        $this->view->config_url2 = $this->_oConfig->url;
    }

    protected function _getDatabaseResultLimit()
    {
        $oRequest = $this->getRequest();
        $sTableName = $oRequest->getModuleName() . '/' . $oRequest->getControllerName() . '/' . $oRequest->getActionName();
        $iCurrentLimit = self::DEFAULT_RESULT_LIMIT;
        $oSettingsTableTab = new UserSettings_Model_Table_UserSettingsTable();
        try {
            $iCurrentLimit = $oSettingsTableTab->getSettingParam(UserSettings_Model_Table_UserSettingsTable::SETTINGS_RESULT_LIMIT, $sTableName);
        } catch (Exception $e) {
        } //jak nie ma tablicy to zostaje domyślna
        return $iCurrentLimit;
    }

    protected function _getAndSaveFilters($sTableName = false){
        $oRequest = $this->getRequest();
        if(!$sTableName){
            $sTableName = $oRequest->getModuleName() . '/' . $oRequest->getControllerName() . '/' . $oRequest->getActionName();
        }
        if($aFilters = $oRequest->getParam(Common_Form_Abstract_MspbFilterForm::FILTER_ARRAY)){
            $oOperation = new UserSettings_Operation_Table();
            $oOperation->saveFilters(array('tableName' => $sTableName, 'value' => $aFilters));
        } else {
            $aFilters = array();
            try {
                $oSettingsTableTab = new UserSettings_Model_Table_UserSettingsTable();
                $aFilters = $oSettingsTableTab->getSettingParam(UserSettings_Model_Table_UserSettingsTable::SETTINGS_FILTERS, $sTableName);
            } catch (Exception $e) {}
        }
        return $aFilters;
    }

    protected function _getDatabaseSearch()
    {
        $oRequest = $this->getRequest();
        $sTableName = $oRequest->getModuleName() . '/' . $oRequest->getControllerName() . '/' . $oRequest->getActionName();
        $sSearchText = '';
        $oSettingsTableTab = new UserSettings_Model_Table_UserSettingsTable();
        try {
            $sSearchText = $oSettingsTableTab->getSettingParam(UserSettings_Model_Table_UserSettingsTable::SETTINGS_SEARCH_TEXT, $sTableName);
        } catch (Exception $e) {
        } //jak nie ma tablicy to zostaje domyślna
        return $sSearchText;
    }

    protected function _getDatabaseSort(){
        $oOperation = new UserSettings_Operation_UserSettings();
        $oRequest = $this->getRequest();
        $sTableName = $oRequest->getModuleName() . '/' . $oRequest->getControllerName() . '/' . $oRequest->getActionName();
        try {
            $sSort = $oOperation->getSort($sTableName);
            return explode(' ', $sSort);
        }catch (Exception $e) {}
        return array ('', 0);
    }

    protected function _mspAddPaginator($aData, MspOptimaStructJK_pagination $oPagination)
    {
        $page = $this->getRequest()->getParam('page', 1);
        $iResultLimit = $oPagination->get_Ile();
        $paginator = new Zend_Paginator(new Common_Paginator_Adapter_Soap($aData));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($iResultLimit);
        $this->view->paginator = $paginator;        //config
        $this->view->config_url = $this->_oConfig->url;

    }

    protected function _mspaddPaginator2($aData,$columns) {
        $page = $this->getRequest()->getParam('form_page', $this->getRequest()->getParam('page', 1) );
        $paginator = new Zend_Paginator(new Common_Paginator_Adapter_Soap($aData));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($aData->totalcount);

        $oRequest = $this->getRequest();
        $oOperation = new UserSettings_Operation_UserSettings();
        $tablename = $oRequest->getModuleName().'/'.$oRequest->getControllerName().'/'.$oRequest->getActionName();

        $this->view->columns = $oOperation->get($tablename);
        $this->view->paginator = $paginator;        //config
        $this->view->config_url = $this->_oConfig->url;
    }
    
}