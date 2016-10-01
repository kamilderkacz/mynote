<?php
/**
 * Plugin zawierający wszystkie reguły dostępu dla klientów systemu.
 * Nie bawiłem się w przywileje, zasoby to po prostu całe nazwy ścieżek routera.
 * 
 * @author Kamil Derkacz
 */

class My_Plugin_ACL extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(\Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        
        try {
        
            $acl = new Zend_Acl;

                // DODAWANIE RÓL
                $acl
                ->addRole(new Zend_Acl_Role('guest'))
                ->addRole(new Zend_Acl_Role('member'))
                ->addRole(new Zend_Acl_Role('admin'));

                // DODAWANIE ZASOBÓW
                $acl    //KONTROLER
                        //index
                ->addResource('note_index_index')
                        //section
                ->addResource('note_section_index')        
                ->addResource('note_section_add')
                ->addResource('note_section_edit')
                ->addResource('note_section_delete')
                ->addResource('note_section_changevisibility')
                ->addResource('note_section_setipp')
                        //note
                ->addResource('note_note_index')
                ->addResource('note_note_show')
                ->addResource('note_note_add')
                ->addResource('note_note_edit')
                ->addResource('note_note_delete')
                        //auth
                ->addResource('note_auth_register')
                ->addResource('note_auth_login')
                ->addResource('note_auth_logout');

                // DODAWANIE REGUŁ DLA POSZCZEGÓLNYCH RÓL 
                // panuje zasada, że wszystko domyślnie jest zabronione, a więc daję zezwolenia:
                // 
                    // GUESTS
                    $acl
                    ->allow('guest', 'note_index_index')
                    ->allow('guest', 'note_auth_register')
                    ->allow('guest', 'note_auth_login')
    //                ->allow('guest', 'note_error_error') // not found
                    ;
                    // ADMINS
                    $acl->allow('admin'); // Wszystko
                    // MEMBERS
                    $acl
                    ->allow('member', 'note_index_index') 
                    ->allow('member', 'note_section_index') 
                    ->allow('member', 'note_section_add') 
                    ->allow('member', 'note_section_edit') 
                    ->allow('member', 'note_section_delete') 
                    ->allow('member', 'note_section_changevisibility') 
                    ->allow('member', 'note_section_setipp') 
                    ->allow('member', 'note_note_index')
                    ->allow('member', 'note_note_add')
                    ->allow('member', 'note_note_show')
                    ->allow('member', 'note_note_edit')
                    ->allow('member', 'note_note_delete')
                    ->allow('member', 'note_auth_login')
                    ->allow('member', 'note_auth_logout')

                    ; 

            // SPRAWDZENIE PRAWA DOSTĘPU DO ZASOBU:
                    
            $role = $this->getRole();
            $params = $request->getParams();
            $route = 'note_' . $params['controller'] . '_' . $params['action'];

            if(!$acl->isAllowed($role, $route)) {
                $request->setControllerName('error')
                        ->setActionName('error');
            }
            //Jeśli dostęp jest zezwolony, nie zmieniamy niczego.
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
    }

    /*
     * 
     */
    public function getRole() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { // jeśli zalogowany
            $role = $_SESSION['Zend_Auth']['storage'][0]->user_role;
            return $role;
        } else { // nie jest zalogowany
          return 'guest'; 
        }
    }
}
