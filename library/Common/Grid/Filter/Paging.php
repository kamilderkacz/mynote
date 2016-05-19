<?php
/**
 * Plugin dla buildera - rozszerzenie.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
class Common_Grid_Filter_Paging implements Common_Grid_Plugin_Interface {
    private $_oFilter = null;
    /**
     * Otrzymuje parametry wejściowe z których zostaną wyszukane filtry dla tego pluginu.
     * @param array $aParams (typowy request jako tablica wejściowa)
     */
    public function  __construct( array $aParams ) {
        $this->_oFilter = Common_Class::get( 'Common_Grid_Filter_Filter_Grid' )->newInstance( $aParams );
    }
    /**
     * Metoda uruchamiająca wykonywanie filtrów na builderze.
     * @param Common_Grid_Interface $oObj
     */
    public function execute( Common_Grid_Interface $oObj ) {
        $this->_oFilter->setBuilder( $oObj );
    }
}