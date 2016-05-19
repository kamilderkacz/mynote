<?php
//Zend_Session::start();

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // funkcje poprzedzone "_init" wywołują się same
   
    protected function _initResourceAutoloader()
    {
        $db = $this->getPluginResource('db');
        
        
         $autoloader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH,
            'namespace' => 'Application',
         ));

         $autoloader->addResourceType( 'model', 'Models', 'Model');
         return $autoloader;
    }
    
    protected function _initAutoload() {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => APPLICATION_PATH,
            'namespace'     => 'My', /////////////
            'resourceTypes' => array(
                'plugin' => array(
                    'path'      => 'My/plugins/',
                    'namespace' => 'Plugin',
                ),
                'form' => array(
                    'path'      => 'My/forms/',
                    'namespace' => 'MyForm',
                ),
                'validator' => array(
                    'path'      => 'My/forms/validators',
                    'namespace' => 'Validator',
                ),
            ),
        ));
    }

    
    protected function _initRouter() {
        try {
            $route = Zend_Controller_Front::getInstance()->getRouter();
            include APPLICATION_PATH . '/configs/route.php';
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }
    protected function _initPlugins() {
        //Rejestracja Pluginów następuje w application.ini. Chyba że wolisz tak:
        
        /*$front = Zend_Controller_Front::getInstance();
        $ACLPlugin = new My_Plugin_ACL();
        $front->setControllerDirectory(APPLICATION_PATH . '/controllers')
              ->registerPlugin($ACLPlugin);*/
    }    

    protected function _initSessionVars() {
//        $s0 = new Zend_Session_Namespace('general');
//        $s0->baseUrl = '';
        $s1 = new Zend_Session_Namespace('note_auth_login');
        $s2 = new Zend_Session_Namespace('navbar'); // po to by ustalić która zakładka jest aktywna
        $s3 = new Zend_Session_Namespace('sectionController');
            $s3->itemsPerPage = 10;
    }

}

