<?php
/**
 * uprawnienia są w filtrach
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage response
 */
class Common_Response_Filter extends Common_Response_Abstract {

    public static function out( Zend_Controller_Action $oController = null, array $aResult ) {
        $oSelf = parent::getInstance( $oController, __CLASS__ );
        $oSelf->json( $aResult );
    }

}