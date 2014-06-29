<?php

class Model_Game_Mapper extends My_Db_Mapper implements My_Db_Imapper {
    
    protected $_dbTable;
    
    public function __construct() {
        parent::__construct(new Model_Game());
    }

    /**
     * 
     * @param type $id
     * @return Model_User
     */
    public function getById($id) {
        return $this->translate(parent::getById($id));
    }
    

    protected static function translate($row) {
        if(empty($row)) {
            return;
        }
        
        if($row instanceof Model_Game) {
            return $row;
        }

        $obj = new Model_Game();
        $obj->setId($row->id);
        $obj->setDatasig($row->datasig);
        $obj->setTablesig($row->tablesig);
        $obj->setData($row->data);

        return $obj;
    }
    
    public function fetchAll(array $order = array()) {
        $rows = parent::fetchAll($order);
        $out = array();
        foreach($rows as $row) {
            $out[] = $this->translate($row);
        }
        return $out;
    }
}

