<?php

class Zend_View_Helper_Redirecter extends Zend_View_Helper_Abstract {

    public function redirecter($array,$urll,$reset = false) {
        $url = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        if ($reset) {
            $task = "window.location = '" . $url->url($array, $urll) . "/I/1'; return false;";
        } else {
            $task = "window.location = '" . $url->url($array, $urll) . "'; return false;";
        }
        return $task;
    }

}
