<?php

/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 */

abstract class Common_Form_Abstract_OfferForm extends ZendX_JQuery_Form {

    protected $_oRequest = null;
    protected $_option = null;
    protected $_bDone;
    protected $_oConfig;
    public $id_value;
    public $edit_code;

    public function setDone($val) {
        $this->_bDone = $val;
    }

    protected function _setLanguage() {
        $polish = Common_Form_TranslatePL::getPolishTranslation();
        $translate = new Zend_Translate('array', $polish, 'pl');
        $this->setTranslator($translate);
    }

    public function __construct($option,$id = null,$code = null) {
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
        $this->setIdValue($id);
        $this->setEditCodeValue($code);
        parent::__construct($option);
    }

    abstract protected function _setElements();

    protected function _setDecorators() {
        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'ViewHelper',
            'HtmlTag',
            'Label'
//            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
//            array('Description', array('placement' => 'prepend')),
//            'Form'
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

    public function highlightErrorElements()
    {
        foreach($this->getElements() as $element) {
            if($element->hasErrors()) {
                $class = ' '.$element->class;
                $element->setAttrib('class', 'form-control form-error' . $class);
            }
        }
    }

    public function isValid($data)
    {
        $valid = parent::isValid($data);

        foreach ($this->getElements() as $element) {
            if ($element->hasErrors()) {
                $oldClass = $element->getAttrib('class');
                if (!empty($oldClass)) {
                    $element->setAttrib('class', $oldClass . ' error');
                } else {
                    $element->setAttrib('class', 'error');
                }
            }
        }
        return $valid;
    }

    private function setIdValue($id){
        $this->id_value = $id;
    }

    private function setEditCodeValue($id){
        $this->edit_code = $id;
    }

    public function getIdValue(){
        return $this->id_value;
    }

    public function getEditCodeValue(){
        return $this->edit_code;
    }

    public function populate(array $array){
        foreach($array as $ar=>$v){
                preg_match('/\[\d+\]/', $ar, $matches);
                if($matches){
                    $string = preg_replace('/\[\d+\]/','',$ar);
                    $array[$string][] = $v;
                }
        }
        parent::populate($array);
    }

    public function getPopulate(array $array){
        foreach($array as $ar=>$v){
            preg_match('/\[\d+\]/', $ar, $matches);
            if($matches){
                $string = preg_replace('/\[\d+\]/','',$ar);
                $array[$string][] = $v;
            }
        }
        return $array;
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
