<?php
/**
 * Filtr - wykonuje sort, limit, order, dir na builderze.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage grid
 */
class Common_Grid_Filter_Filter_Grid implements Common_Grid_Filter_Filter_Interface {    
    private $_aParams;
    public function __construct( array $aParams = null ) {
        $this->_aParams = $aParams;
    }

    public function setBuilder( $oObj ) {
        return $this->_build( $oObj );
    }

    protected function _build( Common_Grid_Interface $oObj ) {
        $oData = Common_Class::get( 'Common_Controller_Grid_Data_Request' )->newInstance( $this->_aParams );
        $this->_setLimit( $oData, $oObj );
        $this->_setMultiSort( $oData, $oObj );
        $this->_setSort( $oData, $oObj );
    }

    private function _setMultiSort( $oData, $oObj ) {
        $aMultisort = $oData->getMultiSort();
        if ($aMultisort != null) {
            foreach ($aMultisort as $aSort) {
                if ($aSort['sort'] == '') {
                    $aSort['sort'] = 'ASC';
                }
                $oObj->addSelectMethod( array( array( 'order' => array("{$aSort['id']} {$aSort['sort']}") ) ) );
            }
        }
    }

    private function _setSort( $oData, $oObj ) {
        $sSort = $oData->getSort();
        $sDir = ($oData->getDir())?" {$oData->getDir()}":'';
        if (($sSort != '') && ($sSort != null)) {
            $oObj->addSelectMethod( array( array( 'order' => array("{$sSort}{$sDir}") ) ) );
        }
    }

    private function _setLimit( $oData, $oObj ) {
        $iOffset = $oData->getOffset();
        $iLimit = $oData->getLimit();

        if ($iLimit != -1 && $iOffset != -1) {
            if ($iLimit <= 0) {
                $iLimit = 100;//@todo ustawiana gdzies w stalych
            }
            if ($iOffset < 0) {
                $iOffset = 0;
            }
            $iPage = (int) ($iOffset / $iLimit) + 1;
        }

        if ($iLimit != -1 && $iOffset != -1) {
            $oObj->addSelectMethod( array( array( 'limitPage' => array( $iPage, $iLimit) ) ) );
        }
    }
}