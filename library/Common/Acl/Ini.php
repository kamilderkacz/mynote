<?php

class Common_Acl_Ini extends Zend_Acl
{
     /**
     * Konstruktor
     *
     * @param string $filename
     */
    public function  __construct($filename)
    {
        $aclIni = new Zend_Config_Ini($filename);
        $aclIni = $aclIni->toArray();

        $this->_setRoles($aclIni['roles']);
        $this->_setRessources($aclIni['ressources']);

        foreach ($aclIni['roles'] as $role => $parent)
        {
            $this->_setPrivilages($role, $aclIni[$role]);
        }

    }

    /**
     * Ustawienie ról
     *
     * @access protected
     * @param array $roles
     * @return void
     */
    protected  function _setRoles(array $roles)
    {
        foreach($roles as $role => $parent)
        {
            if(empty ($parent))
            {
                $parent = null;
            }
            else
            {
                $parent = explode(',', $parent);
            }

            $this->addRole(new Zend_Acl_Role($role), $parent);
        }
    }

    /**
     * Ustawienie zasobów
     *
     * @access protected
     * @param array $ressources
     * @return void
     */
    protected function _setRessources(array $ressources)
    {
        foreach($ressources as $ressource => $parent)
        {
            if(empty($parent))
            {
                $parent = null;
            }
            else
            {
                $parent = explode(',', $parent);
            }

            $this->add(new Zend_Acl_Resource($ressource), $parent);
        }
    }

    /**
     * Ustawienie uprawnień
     *
     * @param string $role
     * @param array $privilages
     * @return void
     */
    protected function _setPrivilages($role, array $privilages)
    {
        foreach($privilages as $do => $ressources)
        {
            foreach($ressources as $ressource => $action)
            {
                if(empty($action))
                {
                    $action = null;
                }
                else
                {
                    $action = explode(',', $action);
                }

                $this->{$do}($role, $ressource, $action);
            }
        }
    }
}