<?php

class Common_Controller_Plugin_Acl_Ini extends Zend_Controller_Plugin_Abstract
{

    /**
     * @var string
     */
    protected $_fileName;

    /**
     * Konstruktor
     *
     * @param string $fileName
     * @return MSP_Controller_Plugin_Acl
     */
    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    /**
     * preDispatch
     *
     * @access public
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $sMember = new Zend_Session_Namespace('member');
        $member = $sMember->member;
        if(isset($member->user_acl) AND $member->user_acl) {
            $acl = $member->user_acl;
        } else {
            $acl = 'guest';
        }

        $cAcl = new Common_Acl_Ini($this->_fileName);

        if($request->getControllerName() != 'error')
        {
            $resourceName = $request->getModuleName() . '_' . $request->getControllerName();
            //echo $resourceName;
            //echo $acl;
            //echo $cAcl->has($resourceName);
            //echo $cAcl->isAllowed($acl, $resourceName, $request->getActionName()); //exit;
            if(!$cAcl->has($resourceName) ||
                !$cAcl->isAllowed($acl, $resourceName, $request->getActionName()))
            {
                    $request->setModuleName('default');
                    $request->setControllerName('index');
                    $request->setActionName('index');
               /* if($acl == 'guest')
                {echo 2;
                    $request->setModuleName('default');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                }
                else
                {echo 1;
                    $request->setModuleName('acl');
                    $request->setControllerName('login');
                    $request->setActionName('index');
                }*/
            }
        }//echo $resourceName; exit;
    }

}