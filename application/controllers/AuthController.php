<?php

class AuthController extends Zend_Controller_Action {

    public function init() {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->msg = $this->_flashMessenger->getMessages(); // Getting Flash message
    
        $auth = Zend_Auth::getInstance();
        if( $auth->hasIdentity() ) {
            $this->view->storage = $auth->getIdentity();
        }
    }
    
    public function registerAction () {
        
        try {
            $sesHeader = new Zend_Session_Namespace('pageTitle'); 
            $sesHeader->pageTitle = 'Rejestracja';
            $_SESSION['navbar']['zarejestruj'] = 1; 
            
            $request = $this->getRequest();
            $registerForm = new My_MyForm_Auth_RegisterForm();
            if ($this->getRequest()->isPost()) {
                if ($registerForm->isValid($request->getPost())) {
                    $data = $registerForm->getValues();
                    // Delete unnecessery data
                    unset($data['password2'],$data['email2'],$data['terms']);
                    
                    $user = new Application_Model_User($data);
                    // Setting random password salt 
                    $user->setPasswordSalt(uniqid('', true));
                    // Concate the password and password salt and hash it
                    $str = $user->getPassword() . $user->getPasswordSalt();
                    $user->setPassword(md5($str));

                    $mapper = new Application_Model_UserMapper();
                    $mapper->save($user);
                    $this->_flashMessenger->addMessage('success');
                    $this->_flashMessenger->addMessage('Rejestracja zakończona. Możesz się zalogować.');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login'); // returning to the login site
                }

            }
            $this->view->registerForm = $registerForm;
        } catch (Zend_Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas rejestracji użytkownika: ');
        }
    }
    

    public function loginAction() { 
        
        try {
            $sesHeader = new Zend_Session_Namespace('pageTitle'); 
            $sesHeader->pageTitle = 'Panel logowania';
            $_SESSION['navbar']['zaloguj'] = 1; 
            
            $db = $this->_getParam('db');
            $loginForm = new My_MyForm_Auth_LoginForm();
            $request = $this->getRequest();
            if ($this->getRequest()->isPost()) {
                if ($loginForm->isValid($request->getPost())) {
                    // Login validation is starting here
                    $adapter = new Zend_Auth_Adapter_DbTable(
                        $db, 'users', 'user_username', 'user_password', 'MD5( CONCAT(?,user_password_salt) )  '
                    );
                    $adapter->setIdentity($loginForm->getValue('username'));
                    $adapter->setCredential($loginForm->getValue('password'));
                    // Get the Zend_Auth instance (singleton)
                    $auth = Zend_Auth::getInstance(); 
                    // User has to be an active user to log in
                    $adapter->getDbSelect()->where('user_active = 0'); 
                    // Authentication
                    $result = $auth->authenticate($adapter); 
    //                $auth->getIdentity() / $result->getIdentity(); // LOGIN 
                    // If authentication has finished successfully
                    if ($result->isValid()) {
                        $storage = $auth->getStorage();
                        // We pack all needed data to the "storage" - session, to use it later in our app (e.g. $_SESSION['Zend_Auth']['storage'][0]->user_role)
                        $storage->write(array(
                            $adapter->getResultRowObject(array(
                                'user_id',
                                'user_username',
                                'user_role'
                                ))
                        ));
                        $this->_flashMessenger->addMessage('success');
                        $this->_flashMessenger->addMessage('Zalogowano pomyślnie!');
                        // Redirect to the page...
                        return $this->_helper->redirector->gotoRoute(array(), 'note_section_index');
                    }
                    else {
                        switch ($result->getCode()) {
                            case (-1): // No user found or he is inactive
                                    $this->_flashMessenger->addMessage('danger');
                                    $this->_flashMessenger->addMessage('Podany użytkownik nie istnieje lub został zablokowany.');
                                break; 
                            case (-3): // Bad password
                                    $this->_flashMessenger->addMessage('danger');
                                    $this->_flashMessenger->addMessage('Błędny login lub hasło.');
                                break; 
                        }
                    }

                    $_SESSION['note_auth_login']['username'] = $loginForm->getValue('username');
                    return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login');

                } 
            }
            
            
        }
        catch (Exception $e) {
            $this->_flashMessenger->addMessage('danger');
            $this->_flashMessenger->addMessage('Wystąpił błąd podczas logowania. ' . $e->getMessage());
        }
        // Passing login form to the view
        $this->view->loginForm = $loginForm;
        // After login failure we want to the login field filled
        if( isset($_SESSION['note_auth_login']['username']) ) {
            $loginForm->getElement('username')->setValue($_SESSION['note_auth_login']['username']);
        }
            
    }
    
    public function logoutAction() { 
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        Zend_Auth::getInstance()->clearIdentity();
        
        $this->_flashMessenger->addMessage('success');
        $this->_flashMessenger->addMessage('Wylogowałeś się.');
        return $this->_helper->redirector->gotoRoute(array(), 'note_auth_login');
    }

}
