<?php

class My_Db_Table_Abstract extends Zend_Db_Table_Abstract implements My_Db_Itable {
    public function getDefTableName() {
        return $this->_name;
    }
    
    protected function getCurrentUser() {
        if (My_Db_Mapper::$currentUser === null) {
            $session = new Zend_Session_Namespace("current_user");
            My_Db_Mapper::$currentUser = $session->user;
        }
        return My_Db_Mapper::$currentUser;
    }
}

