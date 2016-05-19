<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        $request = $this->getRequest();
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity();
        }
        
    }

    public function indexAction() {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'strona główna';
        // w widoku sprawdzana jest ta zmienna (navbar.phtml):
        $_SESSION['navbar']['home'] = 1; 
    }

}
