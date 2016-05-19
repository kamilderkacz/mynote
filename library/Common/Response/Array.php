<?php
/**
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage response
 */
class Common_Response_Array extends Common_Response_Abstract {
    
    public function checkPermission( array &$aData ) {
        if (self::$_oConfig->uodo->enable) {
            $aKeys = array_keys($aData);
            $aDelKeys = $this->_checkPermission( $aKeys );
            foreach($aData as $key => $aRow) {
                foreach($aDelKeys as $delKey) {
                    $aData[$delKey] = '';
                }
                if (is_array($aData[$key])) {
                    $this->checkPermission($aData[$key]);
                }
            }
        }
    }

    public static function out( Zend_Controller_Action $oController = null, array $aResult ) {
        $oSelf = parent::getInstance( $oController, __CLASS__ );
        $oSelf->checkPermission( $aResult );
        $oSelf->json( $aResult );
    }
}