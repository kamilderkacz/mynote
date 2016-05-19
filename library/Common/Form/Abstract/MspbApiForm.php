<?php

abstract class Common_Form_Abstract_MspbApiForm extends Common_Form_Abstract_MspbForm
{
    abstract public function getColumns();
    abstract public function getIdName();
    protected $_idValue;
    public $extraparam;

    public function setIdValue($value){
        $this->_idValue = $value;
    }

    public function getCondition(){
        return $this->getIdName() . '=' . $this->getIdValue();
    }

    public function isEditForm(){
        return isset($this->_option['edit_form']) ? $this->_option['edit_form'] : true;
    }

    public function getOptions(){
        return $this->_option;
    }
    public function getIdValue(){
        return $this->_idValue;
    }
}