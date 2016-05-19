<?php


class My_MyForm_Auth_RegisterForm extends Zend_Form
{    
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        
        $this->setName('Rejestracja')
            ->setMethod('post')
            ->setAction('/')
            ->setDecorators(array(array('ViewScript', array('viewScript' => 'auth/registerForm.phtml'))))
            ->setEnctype(parent::ENCTYPE_MULTIPART);
        
        $username = new Zend_Form_Element_Text('username');
        $username->setName('username')
                ->addFilter('StringTrim') // usuwa białe spacje
              ->setLabel('Nazwa użytkownika')
              ->addValidators(array(new Zend_Validate_Alnum(), new My_Validator_multipleEntry('username')))
              ->setRequired(1);

        $password = new Zend_Form_Element_Password('password');
        $password->setName('password')
              ->setLabel('Hasło')
              ->setRequired(1);
        
        $password2 = new Zend_Form_Element_Password('password2');
        $password2->setName('password2')
                ->addValidators(array(
                    array('identical', false, array('token' => 'password'))
                ))
              ->setLabel('Powtórz hasło')
              ->setRequired(1);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setName('email')
              ->setLabel('E-mail')
              ->addValidators(array(new Zend_Validate_EmailAddress(), new My_Validator_multipleEntry('email')))
              ->setRequired(1);
        
        $email2 = new Zend_Form_Element_Text('email2');
        $email2->setName('email2')
                ->addValidators(array(
                    array('identical', false, array('token' => 'email')), new Zend_Validate_EmailAddress()
                ))
              ->setLabel('Powtórz e-mail')
              ->setRequired(1);
        
        $terms = new Zend_Form_Element_Checkbox('terms');
        $terms->setName('terms')
              ->setLabel('Akceptuję regulamin')
              ->setRequired(1)
              ;
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Rejestruj!');
        
        $this->addElements(array($username, $password, $password2, $email, $email2, $terms, $submit));
    }
}
