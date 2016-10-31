<?php
// Helper for setting title element for HTML head
class Zend_View_Helper_SetPageTitle extends Zend_View_Helper_Abstract {
    public function setPageTitle() {
        $h_t = new Zend_View_Helper_HeadTitle(); // helper handler
        if( isset($_SESSION['pageHeader']['pageTitle']) ) {
            $h_t->headTitle($_SESSION['pageHeader']['pageTitle'] . " - mynote.local");
//            $h_t->headTitle()->setSeparator(' / ');
            unset($_SESSION['pageHeader']['pageTitle']);
        } else {
            return $h_t->headTitle("mynote.local"); // default
        }
        
    }
}
