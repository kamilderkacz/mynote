<?php
/**
 * @author Adam Nielski
 * @copyright Adam Nielski
 * @version 1.0
 * @package common
 * @subpackage response
 */
class Common_Response_Form extends Common_Response_Abstract {

    public $bNew = false;

    protected function _checkPerm($sField, $iResourceType) {
        if (self::$_oConfig->uodo->enable) {
            return parent::$_oPermission->checkPermission($sField, $iResourceType);
        } else {
            return true;
        }
    }

    private function _setValue($aData, $aNewData) {
        foreach ($aData as $key => $value) {
            if ($aNewData[$key]['hide'] == 'true') {
                $aNewData[$key]['value'] = '';
            } else {
                $aNewData[$key]['value'] = $value;
            }
        }
        return $aNewData;
    }

    private function _schema(array $aData) {
        foreach ($aData as $key => $value) {
            //@todo to przerobienie data na protokół, należy to przerobić na coś ładniejszego
            $aData[$key] = array('disable' => 'false',
                'hide' => 'false',
                //'value' => $value,
                'msg' => null,
                'new' => $this->bNew); //@todo schema dla nowych robi sie inna przez ta flage - ale robi sie i tak bo aktualnie
            if (self::$_oConfig->uodo->enable) {
                //przy dodawaniu nie ma obostrzen na uprawnienia wiec tablica jest pusta
                if (!$this->_checkPerm($key, Acl_Model_Table_ZasobTyp::FIELD_READ)) {
                    //nie pokazuj
                    //$aData[$key]['value'] = '';
                    $aData[$key]['hide'] = 'true';
                    $aData[$key]['msg'] = 'blokada';
                }
                if (!$this->_checkPerm($key, Acl_Model_Table_ZasobTyp::FIELD_WRITE)) {
                    //nie edytuj
                    $aData[$key]['disable'] = 'true';
                }
            }
        }
        return $aData;
    }

    public function checkPermission(array &$aData) {
        //inicjalizacja keszu
        $key = md5(serialize(array_keys($aData)));
        if (self::$_oConfig->uodo->enable) {
            $idUser = parent::$_oPermission->getUserId();
        } else {
            $aUser = Zend_Auth::getInstance()->getIdentity();
            $idUser = $aUser['user_id'];
        }
        //tworzenie keszu
        $oCache = new Common_Cache_Cache();
        $oTag = new Common_Cache_Tag();
        $oTag->setTags(array('permission')); //@todo to powinna być stała
        $oCache->setLifetime(86400); //@todo ustawić to jakoś w konfigu na np. 99999999
        $oCache->setSerialize(true);
        $sId = $oCache->getId(get_class($this), 'perm-response-form', array($key, $idUser));
        //pobieranie/tworzenie danych
        $aResult = $oCache->load($sId);
        if ($aResult == false) {
            $aResult = $this->_schema($aData);
            $oCache->save($aResult, $sId, $oTag->getTags());
        }
        $aData = $this->_setValue($aData, $aResult);
    }

    public static function out(Zend_Controller_Action $oController = null, $vResult, array $aParams) {
        $oSelf = parent::getInstance($oController, __CLASS__);
        if (is_object($vResult)) {
            $aResult = $vResult->toArray();
            $oSelf->bNew = false;
        } elseif (is_array($vResult)) {
            $aResult = $vResult;
            $oSelf->bNew = false;
        } else {
            $aResult = array_flip($aParams['fields']); //@todo nazwa powinna być stałą
            if (isset($aResult['remove'])) {
                unset($aResult['remove']);
            }
            foreach ($aResult as &$val) {
                $val = '';
            }
            $oSelf->bNew = true;
        }
        $oSelf->checkPermission($aResult);
        $oSelf->json(array('success' => true, 'data' => $aResult));
    }

}