<?php

/* Klasa bazowa dla kontrollerów obsługujących formularze
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 */

abstract class Common_Form_Controller extends Common_Controller_NoView {

    /** Oddelegowanie operacji logicznych.
     * @return Common_Form_Operation_Interface - warstwa obsługi operacji grida o zdefniowanych metodach.
     */
    abstract protected function _getOperation();

    /**
     * Akcja pobierająca dane (row).
     */
    public function getAction() {
        try {
            $aParams = Common_Request_Permission::in2array(false);
            Common_Response_Form::out($this, $this->_get($aParams), $aParams);
        } catch (Exception $e) {
            Common_Response_Array::out($this, array('success' => false, 'msg' => $e->getMessage()));
        }
    }

    protected function _get(array $aParams) {
        return $this->_getOperation()->get($aParams);
    }

    abstract public function structureAction();

    abstract public function fieldsAction();

    public function saveAction() {
        try {
            $aParams = Common_Request_Permission::in2array(false);
            $id = $this->_save($aParams);
            Common_Response_Array::out($this, array('success' => true, 'idObject' => $id));
        } catch (Exception $e) {
            Common_Response_Array::out($this, array('success' => false, 'msg' => $e->getMessage()));
        }
    }

    protected function _save($aParams) {
        return $this->_getOperation()->save($aParams);
    }

}
