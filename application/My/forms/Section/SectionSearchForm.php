<?php
class My_MyForm_Section_SectionSearchForm extends Zend_Form //extends Common_Form_Abstract_MspbApiForm
{    
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        
        $this->setName('Wyszukiwarka sekcji')
            ->setMethod('post')
            ->setAction('/')
            ->setDecorators(array(array('ViewScript', array('viewScript' => 'section/search-form.phtml'))))
            ->setEnctype(parent::ENCTYPE_MULTIPART);

        
        $fullname = new Zend_Form_Element_Text('fullname');
        $fullname->setName('fullname')
                ->addFilter('StripTags') // usuwa tagi HTML
                ->addFilter('StringTrim') // usuwa spacje
              ->setLabel('Nazwa sekcji')
              ->setRequired(1);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true); // nie wyśle sie wraz z formularzem
        

        
        $this->addElements(array($fullname,  $submit));
    }
}
