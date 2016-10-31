<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity();
        }
    }

    public function indexAction() {
        
        $sesPageHeader = new Zend_Session_Namespace('pageHeader'); 
        $sesPageHeader->pageTitle = 'Strona główna';
        
        $sesNavbar = new Zend_Session_Namespace('navbar');
        $sesNavbar->home = 1; // used in view
        
//        require_once 'Zend/Acl/Exception.php';
//        throw new Zend_Acl_Exception('aaa');
        
    }

}
