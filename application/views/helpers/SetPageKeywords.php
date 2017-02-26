<?php
class Zend_View_Helper_SetPageKeywords extends Zend_View_Helper_Abstract {

    public function setPageKeywords($aSEOParams = null) {

        $sDefault = 'default_keyword_1,default_keyword_2';
        $h_m = new Zend_View_Helper_HeadMeta();
        if(isset($aSeoParams['pageKeywords'])) {
            return $h_m->headMeta()->appendName('keywords', $this->aSeoParams['pageKeywords']);
        } else {
            return $h_m->headMeta()->appendName('keywords', $sDefault);
        }

    }

}
