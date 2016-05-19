<?php

abstract class Common_Form_Abstract_MainForm extends ZendX_JQuery_Form
{
    abstract protected function _setElements();
    abstract protected function _setButtons();
//    abstract public function getColumns();
//    abstract public function getIdName();
    protected $_idValue;

    public function setIdValue($value){
        $this->_idValue = $value;
    }

    public function getIdValue(){
        return $this->_idValue;
    }

    public function getCondition(){
        return $this->getIdName() . '=' . $this->getIdValue();
    }
}