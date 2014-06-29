<?php

class Model_User_Mapper extends My_Db_Mapper implements My_Db_Imapper {
    
    protected $_dbTable;
    
    public function __construct() {
        parent::__construct(new Model_User());
    }
    
    public function findCount($search = null) {
        return parent::findCount($search);
    }
    
    protected function userFilter(Zend_Db_Table_Select $select) {
        return $select;
    }
    
    public function find($search, $limit = null, $offset = null, array $order = array()) {
        $data = parent::find($search, $limit, $offset, $order);
        $all = $this->findCount(null);
        $display = $this->findCount($search);
        return array('rows' => $data['rows'], 'total' => $all, 'display' => $display);
    }

    /**
     * 
     * @param type $id
     * @return Model_User
     */
    public function getById($id) {
        return $this->translate(parent::getById($id));
    }

    /**
     * 
     * @param Model_Base_Object $newObject
     * @throws Exception
     */
    public function save(My_Db_Object $newObject, My_Db_Object $oldObject = null) {
        /**
         * nadpisanie 
         */
        $data = $newObject->getArrayData();
        
        if(!$this->isUnique(array('email' => $data['email']), $data['id'])) {
            throw new My_Exception_Unique('User exists.');
        }
        
        if(!array_key_exists('id', $data) || empty($data['id'])) {
            $data['password_salt'] = md5(time() . rand(1, 1000) . uniqid());
            $data['password'] = md5($data['password'] . $data['password_salt']);
            return $this->getDbTable()->insert($data);
        } else {
            $id = $data['id'];
            unset($data['id']);
            $this->getDbTable()->update($data, array('id = ?' =>  $id));
            return $id;
        }
    }
    
    public function updatePassword($data) {
        $id = $data['id'];
        unset($data['id']);
        $data['password_salt'] = md5(time() . rand(1, 1000) . uniqid());
        $data['password'] = md5($data['password'] . $data['password_salt']);
        $this->getDbTable()->update($data, array('id = ?' =>  $id));
    }

    protected static function translate($row) {
        if(empty($row)) {
            return;
        }
        
        if($row instanceof Model_User) {
            return $row;
        }

        $obj = new Model_User();
        $obj->setId($row->id);
        $obj->setEmail($row->email);
        $obj->setPassword($row->password);
        $obj->setPasswordSalt($row->password_salt);

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
    
    public function loadByUsername($name) {
        $select = $this->getDbTable()->select()->where('email = ?', $name)->limit(1, 0);
        return $this->translate($this->getDbTable()->fetchRow($select));
    }

}

