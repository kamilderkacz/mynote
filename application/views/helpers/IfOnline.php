<?php

class Zend_View_Helper_IfOnline extends Zend_View_Helper_Abstract {

    public function ifOnline($role = null) {
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            return true;
        }
        
        return false;
    }

}
