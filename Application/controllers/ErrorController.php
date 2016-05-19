<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $pageTitleSession = new Zend_Session_Namespace('pageTitle'); 
        
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            
            //sam żem to napisoł:
            $auth = Zend_Auth::getInstance();
            if(!$auth->hasIdentity()) {
                $this->view->message = 'Nie odnaleziono strony lub masz do niej zabroniony dostęp.<br /><br /><a href="'.$this->_helper->url('login','auth').'">Logowanie</a>';
            }
            else {
                $this->view->message = 'Nie odnaleziono strony lub masz do niej zabroniony dostęp.';
            }
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                
                $this->getResponse()->setHttpResponseCode(404);
                $pageTitleSession->pageTitle = 'Przykro mi... ta strona nie istnieje';
                
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Nie znaleziono strony.';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $pageTitleSession->pageTitle = 'Przykro mi... wystąpił błąd';
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Wystąpił błąd systemu. Jeśli problem powtarza się, skontaktuj się proszę z administratorem serwisu.'; // jakiś błąd w widoku/brak pliku z widokiem na 100%.
                break;
        }
        
        
        
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

