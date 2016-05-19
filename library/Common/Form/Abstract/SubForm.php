<?php
/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 */
abstract class Common_Form_Abstract_SubForm extends Zend_Form_SubForm {

  protected $_oRequest = null;
  protected $_option = null;

  public function getNextAction() {
    return $this->_option['next_action'];
  }

  public function getPrevAction() {
    return $this->_option['prev_action'];
  }

  protected function _getPrevUrl() {
    $url = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
    return $url->url(array(), $this->getPrevAction());
  }

  public function __construct($option) {
    $this->_option = $option;
    if (isset($option['request'])) {
      $this->_oRequest = $option['request'];
      unset($option['request']);
    } else {
      throw new Exception('Obiekt requestu wymagany!');
    }
    parent::__construct($option);
  }

  private function _setLanguage() {
    $polish = Common_Form_TranslatePL::getPolishTranslation();
    $translate = new Zend_Translate('array', $polish, 'pl');
    $this->setTranslator($translate);
  }

  abstract protected function _setForm();

  abstract protected function _setButton();

  private function _setDecorators() {
    // We want to display a 'failed authentication' message if necessary;
    // we'll do that with the form 'description', so we need to add that
    // decorator.
    $this->setDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
        array('Description', array('placement' => 'prepend')),
        'Form'
    ));
    $this->setSubFormDecorators(array(
        'FormElements',
        'Fieldset'
    ));
  }

  protected function _addNextButton() {
    /*$this->addElement('submit', 'dalej', array(
        'required' => false,
        'ignore' => true,
        'label' => 'dalej',
    ));*/
    $this->addElement('image', 'dalej', array(
        'onClick' => 'submit()',
        'src' => './images/submitNext.png'
    ));
  }

  protected function _addPrevButton() {
    /*$button = new Zend_Form_Element_Button('powrót');
    $button->setValue('powrót')
            ->setAttrib('onClick', 'window.location="' . $this->_getPrevUrl() . '"');
    $this->addElement($button);*/
    $this->addElement('image', 'powrot', array(
        'onClick' => "window.location='" . $this->_getPrevUrl() . "'; return false;",
        'src' => './images/submitPrev.png'
    ));

  }

  public function init() {
    $this->_setLanguage();
    $this->_setForm();
    $this->_setButton();
    $this->_setDecorators();
  }

}