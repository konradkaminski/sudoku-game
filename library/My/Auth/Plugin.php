<?php

class My_Auth_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if (Zend_Auth::getInstance()->hasIdentity()) {
            if(
                $this->getRequest()->getControllerName() == 'auth'
                && in_array($this->getRequest()->getActionName(), array('login', 'init', 'changepassword', 'forgotpassword'))
                && $this->getRequest()->getModuleName() == 'default')
            {
                $redirector = new Zend_Controller_Action_Helper_Redirector;
                $redirector->gotoUrl('/')->redirectAndExit();
            }
        } else {
            if(
                $this->getRequest()->getControllerName() == 'auth'
                && in_array($this->getRequest()->getActionName(), array('init', 'changepassword', 'forgotpassword'))
                && $this->getRequest()->getModuleName() == 'default')
            {
            } else {
                $request->setModuleName('default');
                $request->setControllerName('auth');
                $request->setActionName('login');
            }
            
        }
    }
}

