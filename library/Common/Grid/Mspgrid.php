<?php

abstract class Common_Grid_Mspgrid extends Common_Operation_Abstract implements Common_Grid_Delegate_Interface
{
    protected $_oBuilder;
    protected $_aSearch = null;
    protected $_sSort;
    protected $_sOrder;
    protected $_iTime = null;
    protected $_iFilter = null;
    protected $_sSearchSource;
    protected $_aSearchColumns;

    public function init()
    {
        $this->_init();
        $this->_oBuilder = new Common_Grid_Builder($this);
    }

    public function getSelect()
    {
        return $this->_oBuilder->getSelect();
    }

    public function getSelectCount()
    {
        return $this->_oBuilder->getSelectCount();
    }

    protected function _makeFilter(&$aResult){
        if (!$this->_iFilter || !is_array($this->_iFilter)) {
            return;
        }
        foreach($this->_iFilter as $sColumn => $sValue){
            if($sValue == ''){
                continue;
            }
            $aResult[]['where'] = array( $sColumn . '="' . $sValue. '"');
        }
    }

    protected function _makeSearch(&$aResult)
    {
        if (!$this->_aSearch) {
            return;
        }
        $aColumns = $this->getSearchColumns();
        if(empty($aColumns)){
            return;
        }
        $sWhere = '';
        $bFirst = true;
        foreach ($aColumns as $i => $sColumn) {
            foreach ($this->_aSearch as $sStringPart) {
                if ($bFirst) {
                    $sWhere .= $sColumn . ' LIKE "%' . $sStringPart . '%"';
                    $bFirst = false;
                } else {
                    $sWhere .= ' OR ' . $sColumn . ' LIKE "%' . $sStringPart . '%"';
                }
            }
        }
        $aResult[]['where'] = array('(' . $sWhere . ')');
    }

    protected function _makeYearFilter(&$aResult,$value,$name){
        if($value){
            if($value){
                $aResult[]['where'] = array('YEAR('.$name.') = '.$value);
            }
        }
    }

    protected function _makeMonthFilter(&$aResult,$value,$name){
        if($value){
            $aResult[]['where'] = array('MONTH('.$name.') = '.$value);
        }
    }

    protected function _makeSort(&$aResult, $sDefault)
    {
        if ($this->_sSort AND $this->_sOrder) {
            if (is_array($this->_sSort)) {
                foreach ($this->_sSort as $sSort) {
                    $aResult[]['order'] = array($sSort . ' ' . $this->_sOrder);
                }
            } else {
                $aResult[]['order'] = array($this->_sSort . ' ' . $this->_sOrder);
            }
        } else {
            $aResult[]['order'] = array($sDefault);
        }
    }

    protected function _objmakeSort(&$aResult, $sDefault)
    {
        if ($this->_sSort AND $this->_sOrder) {
            if (is_array($this->_sSort)) {
                foreach ($this->_sSort as $sSort) {
                    $aResult->order($sSort . ' ' . $this->_sOrder);
                }
            } else {
                $aResult->order($this->_sSort . ' ' . $this->_sOrder);
            }
        } else {
            $aResult->order($sDefault);
        }
    }

    public function setFilter($iFilter)
    {
        $this->_iFilter = $iFilter;
    }

    public function setOrder($sOrder)
    {
        $this->_sOrder = $sOrder;
    }

    public function setSearch($sData)
    {
        $this->_sSearchSource = $sData;
        $sData = str_replace('/', ' ', $sData);
        $explode = explode(" ", $sData);
        $aSearch = array();

        foreach ($explode as $string) {
            if ($string == '') {
                continue;
            }
            $aSearch[] = $string;
        }
        if (!empty($aSearch)) {
            $this->_aSearch = $aSearch;
        }
    }
    
    protected function setSearchColumns(array $aSC){
        $this->_aSearchColumns = $aSC;
    }

    public function setSortAsField($sSort)
    {
        $this->_sSort = $sSort;
    }

    public function setSort($iSort)
    {
        $this->_sSort = $iSort;
    }
    
    public function getFilters()
    {
        return $this->_iFilter;
    }

    public function getASearch(){
        return $this->_aSearch;
    }
}