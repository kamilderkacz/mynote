<?php
class Zend_View_Helper_GetColor extends Zend_View_Helper_Abstract {
    public function getColor($hColor, $type) {
       
//    const INDEX_TABLE_HEADER_BG = 1;
//    const INDEX_TABLE_HEADER_TXT = 3;
//    
//    const SHOW_NOTE_HEADING_BG = 2;
//    const SHOW_NOTE_HEADING_TXT = 4;
        
        switch($hColor) {
            case "default": {
                if($type == 1) return "#fff";
                if($type == 2) return "#fff";
                if($type == 3) return "#333";
                if($type == 4) return "#333";
                
            }
            case "success": {
                if($type == 1) return "#5CB85C";
                if($type == 2) return "#5Cdd5C";
                if($type == 3) return "#eee";
                if($type == 4) return "#fff";
                
            }
            case "info": {
                if($type == 1) return "#5BC0DE";
                if($type == 2) return "#5BC0DE";
                if($type == 3) return "#eee";
                if($type == 4) return "#fff";
                
            }
            case "warning": {
                if($type == 1) return "#EC971F";
                if($type == 2) return "#EC971F";
                if($type == 3) return "#fff";
                if($type == 4) return "#fff";
                
            }
            case "danger": {
                if($type == 1) return "#C9302C";
                if($type == 2) return "#C9302C";
                if($type == 3) return "#fff";
                if($type == 4) return "#fff";
                
            }
            default: return $hColor;
        }
    }
}
//warning #e88
//zolty #fc8