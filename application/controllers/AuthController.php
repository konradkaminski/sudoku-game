<?php

class AuthController extends Sudoku_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy(true);
        return $this->redirect("/auth/login");
    }

    public function loginAction() {

        $params = $this->getRequest()->getUserParams();
        $form = new Form_User();
        $form->removeElement('repeat_password');
        if ($this->getRequest()->isPost()) {
            $form->getElement('email')->removeValidator('Db_NoRecordExists');
            if ($form->isValid($this->getRequest()->getPost())) {
                $db = Zend_Registry::get("db");
                $adapter = new Zend_Auth_Adapter_DbTable(
                  $db, 'users', 'email', 'password', 'MD5(CONCAT(?, password_salt))'
                );
                $adapter->setIdentity($form->getValue('email'));
                $adapter->setCredential($form->getValue('password'));
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    // ladowanie uprawnien
                    $session = new Zend_Session_Namespace("current_user");
                    $usersMap = new Model_User_Mapper();
                    $user = $usersMap->loadByUsername($auth->getIdentity());
                    $session->user = clone $user;
                    return $this->_redirect('/');
                } else {
                    if ($form->getElement('email')->hasErrors()) {
                        $form->getElement('email')->addErrorMessage('{{i18n}incorret login or password{i18n}}');
                    } else {
                        $form->getElement('email')->setErrors(array('{{i18n}incorret login or password{i18n}}'));
                    }
                    $form->markAsError();
                }
            } else {
                
            }
        }
        $this->view->form = $form;
    }

    public function forgotAction() {
        $params = $this->getRequest()->getUserParams();
        $form = new Form_User();
        $form->removeElement('active');
        $form->removeElement('password');
        $form->removeElement('repeat_password');
        $form->removeElement('active');
        $form->removeElement('role');
        $form->getElement('username')->removeValidator('Db_NoRecordExists');
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $dbSource = new Model_User_Mapper();
                $dbSource->addExtraFilter('active', Model_User::STATUS_ACTIVE);
                $data = $dbSource->find($form->getValue('username'));
                if ($data['total'] = 1) {
                    $userObj = $data['rows'][0];
                    $html = new Zend_View();
                    $html->setScriptPath(APPLICATION_PATH . '/views/emails/');
                    $html->assign('key', md5($userObj->password . $userObj->password_salt));
                    $html->assign('email', $userObj->username);
                    $body = $html->render('forgot.phtml');
                    $this->sendMail($body, "Forgot password", $form->getValue('username'));
                    return $this->_redirect('/');
                }
            }
        }
        $this->view->form = $form;
    }

    public function changepasswordAction() {
        $params = $this->getRequest()->getUserParams();
        if (array_key_exists('email', $params) && array_key_exists('key', $params)) {
            $form = new Form_User();
            $form->removeElement('active');
            $form->removeElement('username');
            $form->removeElement('role');
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $dbSource = new Model_User_Mapper();
                    $dbSource->addExtraFilter('active', Model_User::STATUS_ACTIVE);
                    $data = $dbSource->find($params['email']);
                    if ($data['total'] = 1) {
                        $userObj = $data['rows'][0];
                        if (md5($userObj->password . $userObj->password_salt) == $params['key']) {
                            $data = array(
                              'password' => $form->getElement('password')->getValue(),
                              'id' => $userObj->id
                            );
                            $dbSource->updatePassword($data);
                        }
                    }
                    return $this->_redirect("/");
                }
            }
            $this->view->form = $form;
        } else {
            return $this->_redirect("/");
        }
    }

    public function startupAction() {
        $dbSource = new Model_User_Mapper();
        $user = new Model_User();
        $user->email = 'admin@local.pl';
        $user->password = 'test1234';
        $id = $dbSource->save($user);
    }

    public function registerAction() {
        $params = $this->getRequest()->getUserParams();
        $form = new Form_User();
        $form->removeElement('role');
        $form->getElement('email')->removeValidator('Db_NoRecordExists');
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $dbSource = new Model_User_Mapper();
                $user = $form->getModel();
                $user->password = $form->getValue('password');
                $user->active = Model_User::STATUS_NEW;
                $user->create_date = date(My_H::DATETIME_FORMAT);
                $id = $dbSource->save($user);

                $userObj = $dbSource->getById($id);

                $html = new Zend_View();
                $html->setScriptPath(APPLICATION_PATH . '/views/emails/');
                $html->assign('key', md5($userObj->password . $userObj->password_salt));
                $html->assign('email', $userObj->username);
                $body = $html->render('register.phtml');

                $this->sendMail($body, "Register - krok 2", "test");

                return $this->_redirect("/");
            }
        }
        $this->view->form = $form;
    }

    public function confirmAction() {
        $params = $this->getRequest()->getUserParams();
        if (array_key_exists('email', $params) && array_key_exists('key', $params)) {
            $dbSource = new Model_User_Mapper();
            $dbSource->addExtraFilter('active', Model_User::STATUS_NEW);
            $data = $dbSource->find($params['email']);
            if ($data['total'] == 1) {
                $userObj = $data['rows'][0];
                if (md5($userObj->password . $userObj->password_salt) == $params['key']) {
                    $userObj->active = Model_User::STATUS_ACTIVE;
                    $dbSource->save($userObj);
                }
            }
        }
        return $this->_redirect('/');
    }

    private function sendMail($body, $subject, $to) {
        $configSMTP = array(
          'ssl' => 'ssl',
          'port' => 465,
          'auth' => 'login',
          'username' => 'fb.test@interia.pl',
          'password' => 'test1234'
        );

        $transport = new Zend_Mail_Transport_Smtp('poczta.interia.pl', $configSMTP);

        $mail = new Zend_Mail();
        $mail->setSubject($subject)
          ->addTo($to)
          ->setBodyHtml($body)
          ->setFrom('fb.test@interia.pl', 'sudoku game [ver 0.1]')
          ->send($transport);
    }
    
    public function guestAction(){
        
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate(new My_Auth_Adapter_Guest());
        
        $session = new Zend_Session_Namespace("current_user");
        $user = new Model_User();
        $user->email = "guest";
        $session->user = clone $user;
        return $this->_redirect('/');
    }

}
