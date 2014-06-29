<?php

abstract class My_Controller_Action extends Zend_Controller_Action {
    
    const MSG_SAVE_OK = "Dane zostały zapisane";
    const MSG_DELETE_OK = "Wiersz został usunięty";
    const MSG_SAVE_ER = "Podczas zapisywania danych wystąpił błąd";
    const MSG_DELETE_ER = "Nie można usunąć wiersza";

    protected $messenger = null;
    
    protected $breadcrumbs = array();
    
    protected function addBreadcrumb($name, $url) {
        $this->breadcrumbs[$name] = $url;
    }
    
    protected function getMessenger() {
        if($this->messenger == null) {
            $this->messenger = $this->_helper->FlashMessenger;
        }
        return $this->messenger;
    }
    
    protected function addMessage($msg, $type) {
        $messenger = $this->getMessenger();
        $messenger->setNamespace($type)->addMessage($msg);
    }
    
    protected function addSuccess($msg) {
        $this->addMessage($msg, 'success');
    }
    
    protected function addError($msg) {
        $this->addMessage($msg, 'error');   
    }


    public function init(){
        parent::init();
        $this->messenger = $this->_helper->FlashMessenger;
        Zend_Layout::getMvcInstance()->assign('scripts', array());
        Zend_Layout::getMvcInstance()->assign('styles', array());
        
        $scripts = Zend_Layout::getMvcInstance()->scripts;
        $scripts[] = '/js/jquery-ui-1.10.4.custom.js';
        $scripts[] = '/js/jquery.ui.datepicker-pl.js';
        $scripts[] = '/js/plugins/datatables/jquery.dataTables.js';
        $scripts[] = '/js/plugins/datatables/dataTables.bootstrap.js';
        $scripts[] = '/js/plugins/validate/jquery.validate.min.js';
        $scripts[] = '/js/plugins/select2/select2.min.js';
        $scripts[] = '/jscript?time' . time();
        Zend_Layout::getMvcInstance()->assign('scripts', $scripts);

        $styles = Zend_Layout::getMvcInstance()->styles;
        $styles[] = '/css/jQueryUI/jquery-ui-1.10.4.custom.css';
        $styles[] = '/css/datatables/dataTables.bootstrap.css';
        $styles[] = '/css/select2/select2.css';
        $styles[] = '/css/select2/select2-bootstrap.css';
        Zend_Layout::getMvcInstance()->assign('styles', $styles);
    }

    protected $actionsTranslate = array();
    protected $commonTranslate = array(
    );

    public function preDispatch() {
        parent::preDispatch();
        $isLogged = false;
        $auth = Zend_Auth::getInstance();
        
        
        if(!$auth->hasIdentity()) {
            if(
                ($this->getRequest()->getControllerName() != 'auth'
                && !in_array($this->getRequest()->getActionName(), array('login', 'logout', 'init', 'changepassword', 'forgotpassword')))
                || $this->getRequest()->getModuleName() != 'default')
            {
                
              return $this->redirect("/auth/login");  
            }
        } else {
            // check access
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $action = $this->getRequest()->getActionName();
            
            if($module == 'default') {
                $module = 'index';
            }
            if($action == 'list' || $action == 'show') {
                $action = 'index';
            }
            if(array_key_exists($action, $this->actionsTranslate)) {
                $action = $this->actionsTranslate[$action];
            }
            
            if(array_key_exists($action, $this->commonTranslate)) {
                $action = $this->commonTranslate[$action];
            }
            My_Check::p($module . '_' . $controller . '_' . $action, true);
            $isLogged = true;
            $session = new Zend_Session_Namespace("current_user");
            Zend_Layout::getMvcInstance()->assign('user', $session->user);
        }
        Zend_Layout::getMvcInstance()->assign('isLogged', $isLogged);
        
    }
    
    public function postDispatch() {
        parent::postDispatch();
        $this->view->app_data = array(
            'current_url' => $this->getRequest()->getRequestUri()
        );
        $this->getMessenger()->setNamespace('error');
        $this->view->error_messages = $this->getMessenger()->getMessages();
        
        $this->getMessenger()->setNamespace('success');
        $this->view->success_messages = $this->getMessenger()->getMessages();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $layout = $this->view->layout();
            $view = $layout->getView();
        }
        $this->view->breadcrumbs = $this->breadcrumbs;
    }
    
    protected function getBackUrl() {
        $params = $this->getRequest()->getUserParams();
        foreach ($params as $k => $v) {
            if (!in_array($k, array('controller', 'module'))) {
                unset($params[$k]);
            }
        }
        $params['action'] = 'list';
        return $this->view->url($params);
    }

}

