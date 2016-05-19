<?php

class AuthController extends Zend_Controller_Action {

    public function init() {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->_flashMessenger->getMessages(); // Pobranie wiadomości flashowej
    
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity();
        }
//        $s1 = new Zend_Session_Namespace('note_auth_login');
    }
    
    public function registerAction () {
        
        try {
            $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
            $pageTitleSession->pageTitle = 'Rejestracja';
            $_SESSION['navbar']['zarejestruj'] = 1; 
            
            $request = $this->getRequest();
            $registerForm = new My_MyForm_Auth_RegisterForm();
            if ($this->getRequest()->isPost()) {
                if ($registerForm->isValid($request->getPost())) {
                    $data = $registerForm->getValues();
                    unset($data['password2'],$data['email2'],$data['terms']); // to niepotrzebne 
                    $user = new Application_Model_User($data);
                    $user->setPasswordSalt(uniqid('', true));
                    $str = $user->getPassword() . $user->getPasswordSalt();
                    $user->setPassword(md5($str));

                    $mapper = new Application_Model_UserMapper();
                    $mapper->save($user);
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Rejestracja zakończona. Możesz się zalogować.');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login');
                }

            }
        } catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas rejestracji użytkownika: ' . $e->getMessage());
        }
        $this->view->registerForm = $registerForm;
        
    }
    
    // LOGIN
    // Logowanie za pośrednictwem kombinacji hasła z solą hasła - bardzo bezpieczne!
    public function loginAction() { 
        
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        $pageTitleSession->pageTitle = 'Panel logowania';
        $_SESSION['navbar']['zaloguj'] = 1; 
        try {
            $db = $this->_getParam('db'); 
            $loginForm = new My_MyForm_Auth_LoginForm();
            
            if ($loginForm->isValid($_POST)) {
                // poprawność formularza
                $adapter = new Zend_Auth_Adapter_DbTable(
                    $db, 'users', 'user_username', 'user_password', 'MD5( CONCAT(?,user_password_salt) )  '
                );
                $adapter->setIdentity($loginForm->getValue('username'));
                $adapter->setCredential($loginForm->getValue('password'));
                // Singleton
                $auth = Zend_Auth::getInstance(); 
                // warunek logowania
                $adapter->getDbSelect()->where('user_active = 0'); 
                $result = $auth->authenticate($adapter); 
//                $auth->getIdentity() / $result->getIdentity(); // LOGIN 
//                var_dump($result->getMessages()); 
//                die();
                if ($result->isValid()) {
                    // autentykacja pomyślna
                    $storage = $auth->getStorage();
                    // pakujemy potrzebne nam dane do "storage" - sesji, np. $_SESSION['Zend_Auth']['storage'][0]->user_role;
                    $storage->write(array(
                        $adapter->getResultRowObject(array(
                            'user_id',
                            'user_username',
                            'user_role'
                            ))
                    ));
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Zalogowano pomyślnie!');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
                }
                else {
                    // autentykacja nie powiodła się...
                    switch ($result->getCode()) {
                        case (-1): $this->_flashMessenger->addMessage('danger');
                                   $this->_flashMessenger->addMessage('Podany użytkownik nie istnieje w systemie lub został zablokowany.');
                            break; 
                        case (-3): $this->_flashMessenger->addMessage('danger');
                                   $this->_flashMessenger->addMessage('Podane hasło jest nieprawidłowe.');
                            break; 
                    }
                }

                //Przekazanie loginu,aby wypełnić nim formularz na nast, stronie.
                $_SESSION['note_auth_login']['username'] = $loginForm->getValue('username');
                return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login');
                
            } // endif
            
            
            
        }
        catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas logowania. ' . $e->getMessage());
        }
        $this->view->loginForm = $loginForm;
        // po nieudanej autentykacji, powtarzamy login
            if( isset($_SESSION['note_auth_login']['username']))
            $loginForm->getElement('username')->setValue($_SESSION['note_auth_login']['username']);
    }
    
    public function logoutAction() { 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        Zend_Auth::getInstance()->clearIdentity();
        
        $this->_flashMessenger->addMessage('success');
        $this->_flashMessenger->addMessage('Wylogowałeś się.');
//        $this->forward('login');
        return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login');
    }

}
