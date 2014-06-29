<?php

class Model_User extends My_Db_Object {

    const STATUS_ACTIVE = 2;
    const STATUS_NEW = 1;
    
    protected $_dbTable = "Model_DbTable_User";
    
    public function getUniqueFields() {
        return array(
            'email' => $this->email,
        );
    }
}


