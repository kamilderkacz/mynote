<?php
/* Klasa bazowa dla kontrollerów obslugujacych wyjscia dla stylów, skryptów itp.
 * Dziedzicząc po tej klasie można odwołać się do wybranego skryptu przy tym przetwarzajac go, np. dodając odpowiednie adresy/aliasy url.
 * Typowe odwołanie to <modul>/<kontroller>/load/id/<nazwa skryptu>
 * Przeciążenie parametrów protected daje możliwość wysłania dowolnego hedera i wskazania dowolnej ściezki zasobu.
 * @author Adam Nielski, Grzegorz Marchwiński
 * @copyright Andryej Korbaś
 * @version 1.0
 * @package common
 * @subpackage controllers
*/
abstract class Common_Controller_Abstract_ScriptLoader extends Common_Controller_NoView {
    /*
     * Typ wyjścia CSS
    */
    const CSS = 'text/css';
    /*
     * Typ wyjścia JavaScript
    */
    const JS = 'text/javascript';
    /**
     * Określa czy ma używać cachowania skryptów
     * @var boolean 
     */
    protected $_useCache = true;
    
    public function init() {
        parent::init();
        
        //pobieranie konfiguracji cachowania (config.ini lub config_local.ini)
        $config = @Zend_Registry::get(REGISTRY_CONFIG);        
        if (($config instanceof Zend_Config) && isset($config->script_loader->useCache) ) {            
            $this->setUseCache( $config->script_loader->useCache );
        }
    }
    
    /*
     * Domyślna akcja wysyłająca odpowiedni header + pobierajocą zasób wyrenderowanie jego.
    */
    public function loadAction() {
        header("Content-type: " . $this->_getContentType());
        $sPath = str_replace('_', '/', $this->_request->getParam('id'));
        echo $this->_getScript( $sPath );
    }
    /**
     * Metoda zwracająca odpowiedni typ, zdefiniowany w Common_Controller_Abstract_ScriptLoader jako stała np. @see Common_Controller_Abstract_ScriptLoader::CSS
     * @return string - typ
     */
    abstract protected function _getContentType();
    /**
     * Metoda zwracająca ścieżkę do zasobów.
     * @return string - ścieżka do katalogu ze skryptami np. '../public/scripts'
     */
    abstract protected function _getPath();

    protected function _getScript( $sName ) {
        try {
            $oView = new Zend_View();
            $oView->setScriptPath($this->_getPath());
            $oView->baseUrl = $this->_sBaseUrl;
            //$oView->staticFilesBaseUrl = $this->_oConfig->server->staticFilesPath;
            $oView->config = $this->_oConfig;
            $oView->request = $this->_request;
            return $oView->render($sName);
        } catch (Exception $e) {
        }
        return '';
    }
    
    protected function _getFileContent() {
        $sResult = '';
        foreach (new Common_Directory_Iterator($this->_getPath()) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if($fileInfo->GetExtension() != 'js') continue;
            $sResult .= $this->_getScript( $fileInfo->getFilename() ) . "\n";
        }
        
        return $sResult;
    }
    
    protected function _getCachedFileContent() {
		$oCache = new Common_Cache_Cache();
		$oTag = new Common_Cache_Tag();
        $oTag->setTags(array('javascript'));//@todo to powinna być stała
        $oCache->setLifetime(1);//@todo ustawić to jakoś w konfigu na np. 99999999
        $oCache->setSerialize(false);
        $sId = $oCache->getId(get_class($this), 'js-load', array());
        $sResult = $oCache->load($sId);
        if ($sResult != false) {
            if ($oCache->getSerialize()) {
                $sResult = unserialize($sResult);
            }
        } else {
            $sResult = $this->_getFileContent();
            $sNewResult = ($oCache->getSerialize())? serialize($sResult): $sResult;
            $oCache->save($sNewResult, $sId, $oTag->getTags());
        }
        
        return $sResult;
    }
    
    protected function getUseCache() {
        return $this->_useCache;
    }
    
    protected function setUseCache($value) {
        $this->_useCache = (bool)$value;
    }

    public function getAction() {
        if ($this->getUseCache()) {
            $sResult = $this->_getCachedFileContent();
        }
        else {
            $sResult = $this->_getFileContent();
        }
        echo $sResult;
        exit;
    }
}