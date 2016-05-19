<?php
/**
 * Definiuje metody dla pluginów bildera.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
interface Common_Grid_Plugin_Interface {
    /**
     * Metoda wykonuje się w momencie builder->execute
     * @param Common_Grid_Interface $oObj
     */
    public function execute( Common_Grid_Interface $oObj );
}