<?php
class Common_Request_Permission {
    private static $_instance = null;
    private $_oRequest;
    static private $_oPermission;

    private function __construct() {
        if (!is_object(self::$_oPermission)) {
            $oRegistry = Zend_Registry::getInstance();
            $oConfig = $oRegistry->get(REGISTRY_CONFIG);
            if (isset($oConfig->permission->request)) {
                self::$_oPermission = new $oConfig->permission->request;
            }
        }
        $this->_oRequest = $this->_getRequest();
    }

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function in2array( $bCheckPermission = true ) {
        $oRegistry = Zend_Registry::getInstance();
        $oConfig = $oRegistry->get(REGISTRY_CONFIG);
        $oSelf = self::getInstance();
        if ( $bCheckPermission && $oConfig->uodo->enable) {
            return $oSelf->checkPermission( $oSelf->getRequestParams() );
        }
        return $oSelf->getRequestParams();
    }

    private function _cp( $aAllowKeys, $aParams ) {
        foreach($aParams as $key => $val) {
            if ( in_array($key, $aAllowKeys ) ) {
                if (is_array($val)) {
                    $aParams[$key] = $this->_cp( $aAllowKeys, $val );
                }
            } else {
                unset($aParams[$key]);
            }
        }
        return $aParams;
    }

    private function _getAllowedKeys( $aParams ) {
        $aResult = array();
        foreach($aParams as $key => $val) {
            if ( $this->_checkPermission( $key ) ) {
                $aResult[] = $key;
                if (is_array($val)) {
                    $aResult = array_merge($aResult, $this->_getAllowedKeys( $val ) );
                }
            }
        }
        return $aResult;
    }


    private function _getKeys( $aParams ) {
        $aResult = array_keys($aParams);
        foreach($aParams as $key => $val) {
            if ( is_array($val)) {
                $aResult = array_merge( $aResult, $this->_getKeys($val) );
            }
        }
        return $aResult;
    }

    public function checkPermission( $aParams ) {
        $aKeys = $this->_getKeys($aParams);
        $key = md5(serialize($aKeys));
        //$idUser = Zend_Auth::getInstance()->getIdentity()->user_id;
        $idUser = self::$_oPermission->getUserId();
        //tworzenie keszu
        $oCache = new Common_Cache_Cache();
        $oTag = new Common_Cache_Tag();
        $oTag->setTags(array('permission'));//@todo to powinna być stała
        $oCache->setLifetime(86400);//@todo ustawić to jakoś w konfigu na np. 99999999
        $oCache->setSerialize(true);
        $sId = $oCache->getId(get_class($this), 'perm-request', array($key,$idUser));
        //pobieranie/tworzenie danych
        $aAllowKeys = $oCache->load($sId);
        if ($aAllowKeys == false) {
            $aAllowKeys = $this->_getAllowedKeys($aParams);
            $oCache->save($aAllowKeys, $sId, $oTag->getTags());
        }
        return $this->_cp( $aAllowKeys, $aParams);;
    }

    private function _checkPermission( $vKey ) {
        if ($vKey) {
            return (self::$_oPermission->checkPermission($vKey, Common_Acl_Model_Table_ResourceTypes::FIELD_WRITE) & self::$_oPermission->checkPermission($vKey, Common_Acl_Model_Table_ResourceTypes::FIELD_READ));
        } else {
            return true;
        }
    }

    private function _getRequest() {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    static public function setPermission( $oPermission ) {
        self::$_oPermission = $oPermission;
    }
    /**
     * Pobiera parametry requesta wyłączając nazwę kontrolera, akcji i modułu.
     *
     * @return array
     */
    public function getRequestParams() {
        $aParams = $this->_oRequest->getParams();

        $sCtrlKey = $this->_oRequest->getControllerKey();
        $sModKey = $this->_oRequest->getModuleKey();
        $sActKey = $this->_oRequest->getActionKey();
        $aData = array();
        foreach ($aParams as $sKey => $sParam) {
            if (($sKey != $sCtrlKey) && ($sKey != $sActKey) && ($sKey != $sModKey)) {
                try {
                    if (is_array($sParam)) {
                        $aData[$sKey] = $sParam;
                    } elseif ((boolean) strtotime($sParam)) {//zabezpieczenie przed niektorymi versjami php @see Zend_Json_Decoder::decode źle interpretuje ten format daty xxxx-xx-xx pozostawiajac vvvv
                        $aData[$sKey] = $sParam;
                    } else {
                        $sParamNew =  str_replace('\\"','"',$sParam);
                        $aData[$sKey] = @Zend_Json_Decoder::decode($sParamNew);
                    }
                } catch (Exception $e) {
                    $aData[$sKey] = $sParam;
                }
            }
        }
        return $aData;
    }
}