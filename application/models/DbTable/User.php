<?php

class Model_DbTable_User extends My_Db_Table_Abstract
{
    protected $_name = 'users';
    
    public function getSearchFields() {
        return array(
            'email',
        );
    }
}


