<?php

class Model_DbTable_Game extends My_Db_Table_Abstract
{
    protected $_name = 'games';
    
    public function getSearchFields() {
        return array(
            'datasig',
            'tablesig',
        );
    }
}


