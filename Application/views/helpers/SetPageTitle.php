<?php

// Helper, który sprawdza czy istnieje tytuł strony w sesji (np. ustanowiony w jakiejś akcji)
// Jeżeli nie, ustawia domyślny tytuł strony
// a jeżeli tak, to wyciąga go z sesji.
// Autor: Kamil Derkacz

class Zend_View_Helper_SetPageTitle extends Zend_View_Helper_Abstract {

    
    public function setPageTitle() {
        //uchwyt do helpera
        $h_t = new Zend_View_Helper_HeadTitle();
        if( isset($_SESSION['pageTitle']['pageTitle']) ) {
            // ustanawiamy tytuł
            $h_t->headTitle($_SESSION['pageTitle']['pageTitle'] . " - mynote.local");
//            $h_t->headTitle()->setSeparator(' / ');
            // usuwamy tytuł z pamięci
            unset($_SESSION['pageTitle']['pageTitle']);
        } else {
            $h_t->headTitle("mynote.local"); // default
        }
        
    }
}
