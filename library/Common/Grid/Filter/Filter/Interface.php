<?php
/**
 * Interfejs definiujący metody pojedynczego filtra dla pluginu Common_Grid_Filter_Mgr (FILTER)
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
interface Common_Grid_Filter_Filter_Interface {
    /**
     * PRzekazanie i zapis parametrów wejściowych (zazwyczaj requesta)
     * @param array $aParams
     */
    public function __construct( array $aParams = null );
    /**
     * Ustawia obiekt na którym będą wykonywane operacje.
     * @param object $oObj (aktualnie realizujący Common_Grid_Interface np. Common_Grid_Builder lecz nie ma jeszcze interfejsu wskazującego podobne buildery
     */
    public function setBuilder( $oObj );
}