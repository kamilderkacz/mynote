<?php
/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 * @see http://wiktorski.us/2008/02/15/formularze-wielostronicowe/
 */
class Common_Form_Multipage extends ZendX_JQuery_Form {

  protected $_namespace;
  protected $_controller;
  protected $_aMapAction = array();

  public function __construct($namespace = 'Form') {
    $this->_namespace = $namespace;
    $this->_controller = Zend_Controller_Front::getInstance();
  }

  public function addSubForm(Zend_Form $form, $name, $order = null) {
    $this->_aMapAction[$name] = $name;
    //$form->addElement('hidden', 'form', array(
    //    'value' => $name
    //));
    parent::addSubForm($form, $name, null);
  }

  public function getSubForm( $name ) {
    $subFormName = array_search($name, $this->_aMapAction);
    $subForm = parent::getSubForm($subFormName);
    //$aParams = $this->_controller->getRequest()->getParam($subForm->getName());
    //echo $name . '-' . $aParams['form'];
    //print_r($aParams); exit;
    if (//$name == $aParams['form'] AND
        $this->_controller->getRequest()->isPost() AND
        $subForm->isValid($this->_controller->getRequest()->getPost())) {
      $this->setStorage($subForm);
      $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
      $redirector->setGotoRoute(array(), $this->getNextAction($subForm));
      //$redirector->gotoRoute(array('action' => $this->getNextAction($subForm)));
    }
    $subForm->populate($this->getStorage());
    return $subForm;
  }

  public function getNextAction($subForm) {
    return $subForm->getNextAction();
  }

  public function getPrevAction($subForm) {
    return $subForm->getPrevAction();
  }

  public function setStorage($subForm) {
    $session = new Zend_Session_Namespace('Multipage_' . $this->_namespace);
    foreach ($subForm->getValues() as $key => $value) {
      $session->$key = $value;
    }
  }

  public function getStorage() {
    $session = new Zend_Session_Namespace('Multipage_' . $this->_namespace);
    return (array) $session->getIterator();
  }

  public function clearStorage() {
    Zend_Session::namespaceUnset('Multipage_' . $this->_namespace);
  }

}