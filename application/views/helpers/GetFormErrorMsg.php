<?php
class Zend_View_Helper_GetFormErrorMsg extends Zend_View_Helper_Abstract {
    public function getFormErrorMsg($element) { 
        foreach($element->getMessages() as $error) { 
            echo '<span class="errors-block">'.$error . '</span>';
        }
    }
}
