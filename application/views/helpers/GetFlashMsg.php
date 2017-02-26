<?php
/* Helper, który pobiera tablicę z komunikatami. Pierwszy element tablicy powinien być typem (success, danger, warning, info).
* Po ustaleniu typu, następuje skasowanie go z tablicy i wyświetlenie komunikatów. Funkcja nic nie zwraca.
*/
class Zend_View_Helper_GetFlashMsg extends Zend_View_Helper_Abstract {

    public function getFlashMsg($messages) { 
        if ($messages != null) {
            switch($messages[0]) {
                case 'success': $level = 'success'; break;
                case 'danger': $level = 'danger'; break;
                case 'info': $level = 'info'; break;
                case 'warning': $level = 'warning'; break;
            }
            unset($messages[0]);
            
            if (is_array($messages)) { // mamy więcej niż 1 wiadomość
                echo '<div style=""><div style="text-align:left;" class = "alert alert-'. $level .' hide-this" role = "alert">';
                echo '<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Zamknij</span></button>';

                foreach ($messages as $message) {
                    echo $message . '<br />';
                }
            } else { // mamy 1 wiadomość
                echo $messages;
            }
            echo '</div>';
        }
    }

}
