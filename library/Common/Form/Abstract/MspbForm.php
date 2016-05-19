<?php

/*
 * @author Adam Nielski
 * @copyright Agnieszka Korbaś
 * @version 1.0
 * @package common
 * @subpackage form
 */

abstract class Common_Form_Abstract_MspbForm extends ZendX_JQuery_Form
{
    protected $_oRequest = null;
    protected $_option = null;
    protected $_oConfig;
    const DEFAULT_INPUT_CLS = 'col-xs-10 col-sm-9';


    /** @var array Decorators to use for standard form elements */

    public $elementDecorators = array(
        'ViewHelper',
        array('ErrorsHtmlTag', array('tag' => 'div', 'class' => 'col-xs-12 col-sm-reset inline text-danger')),
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'col-sm-9')),
        array('Label', array('class' => 'col-sm-3 control-label no-padding-right', 'requiredSuffix' => '*'))
    );

    public $elementDecoratorsCustom = array(
        'ViewHelper',
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'col-sm-9')),
        array('Label', array('class' => 'col-sm-3 control-label no-padding-right', 'requiredSuffix' => '*'))
    );

    /** @var array Decorators for File input elements */
    // these will be used for file elements
    public $fileDecorators = array(
        'File',
        'Errors',
        array('Description', array('tag' => 'p', 'class' => 'description')),
        array('HtmlTag', array('class' => 'form-div')),
        array('Label', array('class' => 'col-sm-3 control-label no-padding-right', 'requiredSuffix' => ''))
    );

    /** @var array Decorator to use for standard for elements except do not wrap in HtmlTag */
    // this array gets set up in the constructor
    // this can be used if you do not want an element wrapped in a div tag at all
    public $elementDecoratorsNoTag = array();

    /** @var array Decorators for button and submit elements */
    // decorators that will be used for submit and button elements
    public $buttonDecorators = array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-button'))
    );


    public function __construct($option = array())
    {
        $this->addPrefixPath('Common_Form_Decorator', 'Common/Form/Decorator', 'decorator');
        $oRegistry = Zend_Registry::getInstance();
//        $this->_oConfig = $oRegistry->get(REGISTRY_CONFIG);
        $this->_option = $option;
        $this->_oRequest = Zend_Controller_Front::getInstance()->getRequest();

        if (isset($option['decorators'])) {
            $this->setDecorators($option['decorators']);
        } else {
            $this->addDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'div', 'class' => 'form')),
                'Form'));
        }

        // first set up the $elementDecoratorsNoTag decorator, this is a copy of our regular element decorators, but do not get wrapped in a div tag
        foreach ($this->elementDecorators as $decorator) {
            if (is_array($decorator) && $decorator[0] == 'HtmlTag') {
                continue; // skip copying this value to the decorator
            }
            $this->elementDecoratorsNoTag[] = $decorator;
        }


        // set the default decorators to our element decorators, any elements added to the form
        // will use these decorators
        $this->setElementDecorators($this->elementDecorators);
        parent::__construct();
    }

    public function addElement($element, $name = null, $options = null, $bAddEmpty = true)
    {   
        if (!$options) {
            $options = array();
        }
        if (!isset($options['class'])) {
            $options['class'] = self::DEFAULT_INPUT_CLS ;
        }
        if($element == 'select' AND $bAddEmpty){
            if(!isset($options['multiOptions'])){
                $options['multiOptions'] = array();
            }
            $options['multiOptions'] = array('' => 'wybierz') + $options['multiOptions'];
        }
        if(isset($this->_option['readonly'])){
            if($element=='select'){
                $options['disabled'] = 'disabled';
            }
            else if($element=='button'){
                if($name=='submit'){
                    $options['class'] ='hideFormHidden';
                }
                if($name=='cancel'){
                    $options['label']= str_replace($options['label'],"Anuluj","Powrót");
                }
            }
            else {
                $options['class'] = self::DEFAULT_INPUT_CLS ;
                $options['readonly'] = true;
            }
        };
        return parent::addElement($element, $name, $options);
    }


    protected function _setLanguage()
    {
        $polish = Common_Form_TranslatePL::getPolishTranslation();
        $translate = new Zend_Translate('array', $polish, 'pl');
        $this->setTranslator($translate);
    }

    abstract protected function _setElements();
    //--------------------------------------------------------------------------
    protected function _setButtons()
    {
        $this->addElement('button', 'submit', array(
            'label' => '<i class="ace-icon fa fa-check bigger-110"></i> Zapisz',
            'class' => 'btn btn-info',
            'type' => 'submit',
            'escape' => false,
            'required' => false,
            'ignore' => false,
        ));
        $this->addElement('button', 'cancel', array(
            'label' => ' <i class="ace-icon fa fa-undo bigger-110"></i> Anuluj',
            'class' => 'btn',
            'type' => 'button',
            'escape' => false,
            'required' => false,
            'ignore' => false,
            'onClick' => "window.location='" . $this->_getCancelUrl() . "'; return false;",
        ));
    }

    protected function _addInFormButton($sName, $sLabel, $sOnClick = '', $sIcon = false, $sClass = 'btn btn-info'){
        if($sIcon){
            $sLabel = '<i class="ace-icon '.$sIcon.' align-top bigger-125"></i> ' . $sLabel;
        }
        $this->addElement('button', $sName, array(
            'label' => $sLabel,
            'class' => $sClass,
            'type' => 'button',
            'escape' => false,
            'required' => false,
            'ignore' => false,
            'onClick' => $sOnClick,
        ));
    }

    public function init()
    {
        $this->_setLanguage();
        $this->_setElements();
        $this->_setButtons();
    }

    protected function getCancelArray(){
        return isset($this->_option['cancel_action_value']) ? $this->_option['cancel_action_value'] : array();
    }

    protected function getCancelAction() {
        return isset($this->_option['cancel_action']) ? $this->_option['cancel_action'] : '';
    }

    protected function _getCancelUrl() {
        $url = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        return $url->url($this->getCancelArray(), $this->getCancelAction());
    }

    protected function _getTableName(){
        return isset($this->_option['tablename']) ? $this->_option['tablename'] : $this->_oRequest->getModuleName() . '/' . $this->_oRequest->getControllerName() . '/' . $this->_oRequest->getActionName();
    }


}