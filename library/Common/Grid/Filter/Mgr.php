<?php
/**
 * Plugin dla buildera - rozpoznaje i definiuje użyte filtry a następnie dokonfigurowuje builderaSQL.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
class Common_Grid_Filter_Mgr implements Common_Grid_Plugin_Interface {
    const FILTER_NAME='filters';
    private $_aFilters = array();
    /**
     * Otrzymuje parametry wejściowe z których zostaną wyszukane filtry dla tego pluginu.
     * @param array $aParams (typowy request jako tablica wejściowa)
     */
    public function  __construct( array $aParams ) {
        foreach( $aParams as $sName => $afilters) {
            if ($sName == Common_Grid_Filter_Mgr::FILTER_NAME and is_array($afilters)) {
                foreach( $afilters as $key => $val) {
                    error_reporting(E_STRICT);
                    try {
                        $sClass = key($val);
                        if ( class_exists($sClass, true) and $aImplements = class_implements($sClass,true) and isset($aImplements['Common_Grid_Filter_Filter_Interface'] ) ) {
                            $this->_aFilters[] = Common_Class::get( $sClass )->newInstance( current($val) );
                        }
                    } catch (Exception $e) {
                        ;
                    }
                    error_reporting(E_ALL | E_STRICT);
                }
            }
        }
    }
    /**
     * Metoda uruchamiająca wykonywanie filtrów na builderze.
     * @param Common_Grid_Interface $oObj
     */
    public function execute( Common_Grid_Interface $oObj ) {
        foreach( $this->_aFilters as $oFilter ) {
            $oFilter->setBuilder( $oObj );
        }
    }
}