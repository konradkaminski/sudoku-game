<?php

class Model_Game extends My_Db_Object {

    
    protected $_dbTable = "Model_DbTable_Game";
    
    public function getUniqueFields() {
        return array(
            'datasig' => $this->datasig,
            'tablesig' => $this->tablesig,
        );
    }
}


