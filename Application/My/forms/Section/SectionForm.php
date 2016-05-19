<?php
class My_MyForm_Section_SectionForm extends Zend_Form //extends Common_Form_Abstract_MspbApiForm
{    
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        
        $this->setName('Dodawanie sekcji')
            ->setMethod('post')
            ->setAction('/')
            ->setDecorators(array(array('ViewScript', array('viewScript' => 'section/form.phtml'))))
            ->setEnctype(parent::ENCTYPE_MULTIPART);

        
        $fullname = new Zend_Form_Element_Text('fullname');
        $fullname->setName('fullname')
                ->addFilter('StripTags') //?
                ->addFilter('StringTrim') //?
              ->setLabel('Nazwa')
              ->setRequired(1);
        $color = new Zend_Form_Element_Text('color');
        $color->setName('color')
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                ->setLabel('Kolor')
                ->setRequired(0);
        $visibility = new Zend_Form_Element_Select('visibility');
        $visibility->setName('visibility')
                ->setLabel('Widoczność')
                ->addMultiOptions(array('Prywatna','Publiczna'))
                ->setRequired(1);
        
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Zapisz')->setIgnore(true);
        
//        $this->setElementDecorators(array('ViewHelper'));
        
        $this->addElements(array($fullname, $color, $visibility, $submit));
    }
}
