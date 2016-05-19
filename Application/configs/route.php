<?php

/* 
 * Kamil Derkacz
 */

//Controller: index
$route->addRoute(
            'note_index_index',
            new Zend_Controller_Router_Route(
                    '/',
            array('controller' => 'index','action' => 'index'))
        );
//Controller: section
$route->addRoute(
            'note_section_index',
            new Zend_Controller_Router_Route(
                    '/sekcje/pokaz/*',
            array('controller' => 'section','action' => 'index'))
        );
$route->addRoute(
            'note_section_add',
            new Zend_Controller_Router_Route(
                    '/sekcja/dodaj/*',
            array('controller' => 'section','action' => 'add'))
        );
$route->addRoute(
            'note_section_edit',
            new Zend_Controller_Router_Route(
                    '/sekcja/edytuj/:section_id/*',
            array('controller' => 'section','action' => 'edit'))
        );
$route->addRoute(
            'note_section_delete',
            new Zend_Controller_Router_Route(
                    '/sekcja/usun/:section_id/*',
            array('controller' => 'section','action' => 'delete'))
        );
$route->addRoute(
            'note_section_setipp',
            new Zend_Controller_Router_Route(
                    '/sekcje/ustawipp/*',
            array('controller' => 'section','action' => 'setipp'))
        );

//Controller: note
$route->addRoute(
            'note_note_index',
            new Zend_Controller_Router_Route('/notatki/pokaz/:section_id/*',
            array('controller' => 'note','action' => 'index'))
        );
$route->addRoute(
            'note_note_show',
            new Zend_Controller_Router_Route('/notatka/pokaz/:note_id/*',
            array('controller' => 'note','action' => 'show'))
        );
$route->addRoute(
            'note_note_add',
            new Zend_Controller_Router_Route('/notatka/dodaj/:section_id/*',
            array('controller' => 'note','action' => 'add'))
        );
$route->addRoute(
            'note_note_edit',
            new Zend_Controller_Router_Route('/notatka/edytuj/:note_id/*',
            array('controller' => 'note','action' => 'edit'))
        );
$route->addRoute(
            'note_note_delete',
            new Zend_Controller_Router_Route('/notatka/usun/:note_id/*',
            array('controller' => 'note','action' => 'delete'))
        );

//Controller: auth
$route->addRoute(
            'note_auth_register',
            new Zend_Controller_Router_Route('/rejestracja',
            array('controller' => 'auth','action' => 'register'))
        );
$route->addRoute(
            'note_auth_login',
            new Zend_Controller_Router_Route('/zaloguj',
            array('controller' => 'auth','action' => 'login'))
        );
$route->addRoute(
            'note_auth_logout',
            new Zend_Controller_Router_Route('/wyloguj',
            array('controller' => 'auth','action' => 'logout'))
        );