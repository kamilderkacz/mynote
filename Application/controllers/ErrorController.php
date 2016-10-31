<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $sesPageHeader = new Zend_Session_Namespace('pageHeader'); 
        
        $errors = $this->_getParam('error_handler');
        
        
        if (!$errors || !$errors instanceof ArrayObject) {
            // Access denied
            $this->view->message = 'Nie masz dostępu do tego zasobu.<br /><br /><a href="'.$this->_helper->url('login','auth').'">Zaloguj się</a>';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                
                $this->getResponse()->setHttpResponseCode(404);
                $sesPageHeader->pageTitle = 'Nie znaleziono strony';
                
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Nie znaleziono strony.';
                $this->view->errorCode = 404;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $sesPageHeader->pageTitle = 'Wystąpił problem.';
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Wystąpił błąd systemu. Jeśli problem powtarza się, skontaktuj się z administratorem serwisu.'; // jakiś błąd w widoku/brak pliku z widokiem na 100%.
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

