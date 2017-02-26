<?php
// Login form
class My_MyForm_Auth_LoginForm extends Zend_Form
{    
    public function __construct($options = null) { 
        parent::__construct($options);
        
        $this->setName('Logowanie')
            ->setMethod('post')
            ->setAction('/')
            ->setDecorators(array(array('ViewScript', array('viewScript' => 'auth/loginForm.phtml'))))
            ->setEnctype(parent::ENCTYPE_MULTIPART);
        
        $username = new Zend_Form_Element_Text('username');
        $username->setName('username')
                 ->addFilter('StringTrim') // removes spaces
                 ->setLabel('Nazwa użytkownika')
                 ->setRequired(1);
        $password = new Zend_Form_Element_Password('password');
        $password->setName('password')
                 ->setLabel('Hasło')
                 ->setRequired(1);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Zaloguj')
               ->setIgnore(true); // We don't want to submit "submit button"
        
        // Add all alements to the form
        $this->addElements(array($username, $password, $submit));
    }
}
