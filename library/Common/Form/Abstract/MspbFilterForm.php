<?php

abstract class Common_Form_Abstract_MspbFilterForm extends Common_Form_Abstract_MspbForm
{
    const FILTER_ARRAY = 'filters';

    public function addElement($element, $name = null, $options = null, $bAddEmpty = true){
        $options['belongsTo'] = self::FILTER_ARRAY;
        parent::addElement($element, $name, $options, $bAddEmpty);
    }

    protected function _setButtons()
    {
        $this->addElement('button', 'submit', array(
            'label' => 'Filtruj',
            'class' => 'btn btn-info',
            'type' => 'submit',
            'escape' => false,
            'required' => false,
            'ignore' => false,
        ));

        $this->addElement('button', 'reset', array(
            'label' => 'Resetuj',
            'class' => 'btn btn-info',
            'onclick' => 'resetTableSettings("' . $this->_getTableName() . '");return false;',
            'escape' => false,
            'required' => false,
            'ignore' => false,
        ));
    }
}