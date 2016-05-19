<?php
/**
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage response
 */
class Common_Response_Abstract {

    private static $_instance = array();

    protected $_sHelper = null;
    protected $_oRequest;
    protected $_oController;
    static protected $_oPermission;
    static protected $_oConfig = null;

    protected function __construct(Zend_Controller_Action $oController) {
        $this->_oRequest = $this->_getRequest();
        $this->_oController = $oController;
        if (!is_object(self::$_oPermission)) {
            $oRegistry = Zend_Registry::getInstance();
            self::$_oConfig = $oRegistry->get(REGISTRY_CONFIG);
            if (isset($_oConfig->permission->response)) {
                self::$_oPermission = new self::$_oConfig->permission->response;
            }
        }
    }

    protected function _getRequest() {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    public static function getInstance( Zend_Controller_Action $oController = null, $sClass) {
        if (!isset(self::$_instance[$sClass])) {
            self::$_instance[$sClass] = new $sClass( $oController );
        }
        return self::$_instance[$sClass];
    }
    //zwraca pola do skasowania
    protected function _checkPermission( $aKeys ) {
        if (self::$_oConfig->uodo->enabled) {
            //sprawdza z podanych kluczy do których użytkownik nie ma dostępu?
            $key = md5(serialize($aKeys));
            $idUser = self::$_oPermission->getUserId();
            //tworzenie keszu
            $oCache = new Common_Cache_Cache();
            $oTag = new Common_Cache_Tag();
            $oTag->setTags(array('permission'));//@todo to powinna być stała
            $oCache->setLifetime(86400);//@todo ustawić to jakoś w konfigu na np. 99999999
            $oCache->setSerialize(true);
            $sId = $oCache->getId(get_class($this), 'perm-response-abst', array($key,$idUser));
            //pobieranie/tworzenie danych
            $aResult = $oCache->load($sId);
            if ($aResult == false) {
                $aResult = array();
                foreach($aKeys as $sField) {
                    if (!self::$_oPermission->checkPermission($sField, Acl_Model_Table_ZasobTyp::FIELD_READ)) {
                        $aResult[] = $sField;
                    }
                }
                $oCache->save($aResult, $sId, $oTag->getTags());
            }
        } else {
            $aResult = array();
        }
        return $aResult;
    }

    public function setLayout( string $sPath ) {
        $this->_sLayout = $sPath;
    }

    public function setHelper( string $sHelper ) {
        $this->_sHelper = $sHelper;
    }

    static public function setPermission( $oPermission ) {
        self::$_oPermission = $oPermission;
    }

    public function json( $oResult ) {
        $oHelper = $this->_oController->getHelper('json');
        $oHelper->direct( $oResult );
    }
}