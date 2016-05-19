<?php
/**
 * Definicja nterfejsu Buildera.
 * Ponizsze metody definiują wspólne API pozwalające współdziałać pluginom jak określają sposób dostępu do podobnych obiektów.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
interface Common_Grid_Interface {
    /**
     * Mozliwość dodawania pluginów.
     * @param Common_Grid_Plugin_Interface $oObj
     */
    public function addPlugin( Common_Grid_Plugin_Interface $oObj );
    /**
     * Wykonanie operacji na logice.
     */
    public function execute();
    /**
     * Dodawanie kolejnych metod obiektu SELECTA
     * @param array $aSQLData
     */
    public function addSelectMethod( array $aSQLData );
}