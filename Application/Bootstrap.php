<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // All the functions which have "_init" at the beginning are called automatically
   
    // Autoloaders
    protected function _initResourceAutoloader() {
        $autoloader = new Zend_Loader_Autoloader_Resource(array(
           'basePath'  => APPLICATION_PATH,
           'namespace' => 'Application',
        ));
        $autoloader->addResourceType('model', 'Models', 'Model');
        return $autoloader;
    }
    protected function _initAutoload() {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => APPLICATION_PATH,
            'namespace'     => 'My',
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
        return $resourceLoader;
    }

    // Routing initialization
    protected function _initRouter() {
        try {
            $route = Zend_Controller_Front::getInstance()->getRouter();
            include APPLICATION_PATH . '/configs/route.php';
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
    }
    
    // Session variables initialization
    protected function _initSessionVars() {
//        $s0 = new Zend_Session_Namespace('general');
        $s1 = new Zend_Session_Namespace('note_auth_login');
        $s2 = new Zend_Session_Namespace('navbar'); // for active menu tab purpose
        $s3 = new Zend_Session_Namespace('sectionController');
        $s3->itemsPerPage = 10;
    }
    
    // Pass config to registry
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

}

