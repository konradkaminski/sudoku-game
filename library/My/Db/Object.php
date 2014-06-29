<?php

class My_Db_Object extends stdClass {
    
    protected $_dbTable = null;
    
    protected $_properties = array();
    
    public function getUniqueFields() {
        return array();
    }
    
    public function getSearchColumns() {
        return array();
    }
    
    public function __call($name, $arguments) {
        if(substr($name, 0, 3) == 'set' && count($arguments) == 1) {
            $name = substr($name, 3, strlen($name));
            $name = strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $name));
            $this->{$name} = $arguments[0];
        }
        if(substr($name, 0, 3) == 'get' && count($arguments) == 0) {
            $name = substr($name, 3, strlen($name));
            $name = strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $name));
            return $this->{$name};
        }
    }
    
    public function __set($name, $value) {
        $this->_properties[$name] = $value;
    }
    
    public function __get($name) {
        if(!array_key_exists($name, $this->_properties)) {
            return null;
            throw new Exception("Uninitialized property: " . $name);
        }
        return $this->_properties[$name];
    }
    
    public function setArrayData($data) {
        $this->_properties = $data;
    }
    
    public function getArrayData() {
        return $this->_properties;
    }
    
    public function getDbTable() {
        return $this->_dbTable;
    }
    
    public function getCode() {
        return "[undefined]";
    }

}

