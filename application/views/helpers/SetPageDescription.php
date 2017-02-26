<?php
class Zend_View_Helper_SetPageDescription extends Zend_View_Helper_Abstract {
    public function setPageDescription($aSeoParams = null) {
        $sDefault = 'Domyślny opis strony...';
        $h_m = new Zend_View_Helper_HeadMeta();
        if(isset($aSeoParams['pageDescription'])) {
            return $h_m->headMeta()->appendName('description', $aSeoParams['pageDescription']);
        } else {
            return $h_m->headMeta()->appendName('description', $sDefault);
        }
    }
}
