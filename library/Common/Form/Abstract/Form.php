<?php

/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 */

abstract class Common_Form_Abstract_Form extends ZendX_JQuery_Form {

    protected $_oRequest = null;
    protected $_option = null;
    protected $_bDone;
    protected $_oConfig;

    public function setDone($val) {
        $this->_bDone = $val;
    }

    protected function _setLanguage() {
        $polish = Common_Form_TranslatePL::getPolishTranslation();
        $translate = new Zend_Translate('array', $polish, 'pl');
        $this->setTranslator($translate);
    }

    public function __construct($option) {
        $oRegistry = Zend_Registry::getInstance();
        $this->_oConfig = $oRegistry->get(REGISTRY_CONFIG);
        $this->_option = $option;
        if (isset($option['request'])) {
            $this->_oRequest = $option['request'];
            unset($option['request']);
        } else {
            $this->_oRequest = Zend_Controller_Front::getInstance()->getRequest();
            //throw new Exception('Obiekt requestu wymagany!');
        }
        parent::__construct($option);
    }

    abstract protected function _setElements();

    protected function _setDecorators() {
        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }

    public function init() {
        $this->_setLanguage();
        $this->_setElements();
        $this->_setDecorators();
    }

    public function getOption() {
        return $this->_option;
    }

    public function getErrorMessage($name) {
        if ($this->_oRequest->isPost()) {
            if (!$this->isValid($this->_oRequest->getPost())) {
                if ($arr = $this->getElement($name)->getMessages() AND count($arr)) {
                    return $arr[$this->getElement($name)->getErrors()[0]];
                }
            }
        }
        return '';
    }

}
