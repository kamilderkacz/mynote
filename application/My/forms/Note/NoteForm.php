<?php


class My_MyForm_Note_NoteForm extends Zend_Form //extends Common_Form_Abstract_MspbApiForm
{    
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        
        $this->setName('Dodawanie notatki')
            ->setMethod('post')
            ->setAction('/')
            ->setDecorators(array(array('ViewScript', array('viewScript' => 'note/form.phtml'))))
            ->setEnctype(parent::ENCTYPE_MULTIPART);
        
        $title = new Zend_Form_Element_Text('title');
        $title->setName('title')
                ->addFilter('StripTags')  // usuwa HTML tags
                ->addFilter('StringTrim') // usuwa białe spacje
              ->setLabel('Tytuł:')
              ->setRequired(1);
        $content = new Zend_Form_Element_Textarea('content');
        $content->setName('content')
//                ->addFilter('StringTrim')
                ->setLabel('Treść:')
                ->setRequired(0);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Zapisz')->setIgnore(true);
        
        $this->addElements(array($title, $content,  $submit));
    }
}
