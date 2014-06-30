<?php

class Model_Gameresult_Mapper extends My_Db_Mapper implements My_Db_Imapper {
    
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
        
        if($row instanceof Model_Gameresult) {
            return $row;
        }

        $obj = new Model_Gameresult();
        $obj->setId($row->id);
        $obj->setIdUser($row->id_user);
        $obj->setIdGame($row->id_game);
        $obj->setTime($row->time);
        $obj->setHits($row->hits);

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

