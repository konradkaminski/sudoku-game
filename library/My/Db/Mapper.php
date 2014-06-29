<?php

class My_Db_Mapper implements My_Db_Imapper {

    protected $_dbTable;
    public static $currentUser = null;

    /** protected function * */

    /**
     *
     */
    protected function getCurrentUser() {
        if (My_Db_Mapper::$currentUser === null) {
            $session = new Zend_Session_Namespace("current_user");
            My_Db_Mapper::$currentUser = $session->user;
        }
        return My_Db_Mapper::$currentUser;
    }
    
    protected function userFilter(Zend_Db_Table_Select $select) {
        return $select;
        if(My_Check::p('structure_structure_index_all')) {
            return $select;
        }
        
        return $select->where($this->getDbTable()->getDefTableName() . '.id_structure in (?)', array_keys($this->getCurrentUser()->selected));
    }

    /** Model_Base_Imapper * */
    public function __construct(My_Db_Object $object) {
        $this->setDbTable($object);
    }

    /**
     * licznik
     */
    public function findCount($search = null) {
        $select = $this->getDbTable()->select();
        $select->from($select->getTable());
        if (!empty($search)) {
            $searchFields = $this->getDbTable()->getSearchFields();
            if(!empty($searchFields)) {
                $tmpQuery = array();
                foreach($searchFields as $field) {
                    $tmpQuery[] = $field . " LIKE '%{$search}%'";
                }
                $select->where('(' . implode(' OR ', $tmpQuery) . ')');
            }
        }
        $select = $this->getExtraFilters($select);
        $this->userFilter($select)->reset(Zend_Db_Select::COLUMNS)->columns('count(' . $this->getDbTable()->getDefTableName() . '.id) as count');
        
        $data = $this->getDbTable()->fetchRow($select);
        return $data['count'];
    }

    public function find($search, $limit = null, $offset = null, array $order = array()) {
        $select = $this->getDbTable()->select();
        
        if(!empty($search)) {
            $searchFields = $this->getDbTable()->getSearchFields();
            if(!empty($searchFields)) {
                $tmpQuery = array();
                foreach($searchFields as $field) {
                    $tmpQuery[] = $field . " LIKE '%{$search}%'";
                }
                $select->where('(' . implode(' OR ', $tmpQuery) . ')');
            }
        }
        if(!empty($limit) || !empty($offset)) {
            $select = $select->limit($limit, $offset);
        }
        
        if(!empty($order)) {
            foreach($order as $orderItem) {
                $select = $select->order($orderItem);
            }
        }
        
        
        
        $select = $this->getExtraFilters($select);
        
//        $select = $this->userFilter($select);
        
//        echo $select->__toString(); die;
        
        $data = $this->getDbTable()->fetchAll($select);
        
        $out = array();
        foreach($data as $item) {
             $out[] = $this->translate($item);
        }
        $all = $this->findCount(null);
        $display = $this->findCount($search);
        return array('rows' => $out, 'total' => $all, 'display' => $display);
    }

    public function getUnique($uniqueFields = array()) {
        if(empty($uniqueFields)) {
            throw new Exception('There is no unique fields');
        }
        
        $select = $this->getDbTable()->select();
        foreach($uniqueFields as $k => $v) {
            $select = $select->where($k . ' = ? ', $v);
        }
        
        $select->limit(1, 0);
        $select = $this->userFilter($select);
        
        $obj = $this->translate($this->getDbTable()->fetchRow($select));
        if(empty($obj)) {
            throw new Exception('There is no object');
        }
        return $obj;
    }
    
    /**
     * 
     * @param type $id
     * @return My_Db_Object
     */
    public function getById($id) {
        $select = $this->getDbTable()->select()->where( $this->getDbTable()->getDefTableName() . '.id = ?', $id)->limit(1, 0);
        $select = $this->userFilter($select);
        
        $obj = $this->translate($this->getDbTable()->fetchRow($select));
        if(empty($obj)) {
            return false;
            // throw new Exception('There is no object');
        }
        return $obj;
    }

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!($dbTable instanceof My_Db_Object)) {
            throw new Exception("Invalid table data gateway provided");
        }
        $this->_dbTable = $dbTable->getDbTable();
        if (is_string($this->_dbTable)) {
            $this->_dbTable = new $this->_dbTable();
        }
        return $this;
    }

    /**
     * 
     * @return My_Db_Table_Abstract
     * @throws Exception
     */
    public function getDbTable() {
        if (!$this->_dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception("Invalid table data gateway provided");
        }
        return $this->_dbTable;
    }

    public function save(My_Db_Object $newObject, My_Db_Object $oldObject = null) {
        
        $data = $newObject->getArrayData();
        $tmpId = null;
        if (array_key_exists('id', $data)) {
            $tmpId = $data['id'];
        }

        if (!$this->isUnique($newObject->getUniqueFields(), $tmpId)) {
            throw new My_Exception_Unique('Object exists.');
        }
        if ($tmpId == null) {
            return $this->getDbTable()->insert($data);
        } else {
            
            $id = $data['id'];
            unset($data['id']);
            $this->getDbTable()->update($data, array('id = ?' => $id));
            return $id;
        }
    }

    public function fetchAll(array $order = array()) {
        $data = array();
        if (empty($order)) {
            $data = $this->getDbTable()->fetchAll();
        } else {
            $select = $this->getDbTable()->select();
            foreach ($order as $item) {
                $select = $select->where($item);
            }
            $data = $this->getDbTable()->fetchAll($select);
        }
        $out = array();
        foreach($data as $item) {
             $out[] = $this->translate($item);
        }
        return $out;
    }

    public function isUnique($fields = array(), $id = null) {
        if(empty($fields)) {
            return true;
        }
        $select = $this->getDbTable()->select();
        if (!empty($fields)) {
            foreach ($fields as $k => $v) {
                $select = $select->where($k . ' = ?', $v);
            }
        }
        $subquery = $select->getPart(Zend_Db_Select::WHERE);
        $select = $select->reset(Zend_Db_Select::WHERE);
        if (!empty($id)) {
            $select = $select->where('id != ?', $id);
        }
        $select = $select->where(implode(' ', $subquery))->limit(1, 0);
        
        $row = $this->getDbTable()->fetchAll($select);
        if (count($row) == 0) {
            return true;
        }
        return false;
    }

    public function delete(My_Db_Object $object) {
        $this->getDbTable()->delete(array('id = ?' => $object->id));
    }
    
    protected static function translate($object) {
        throw new Exception("Unimplemented method");
    }

    protected $extraFilters = null;
    
    public function addExtraFilter($key, $value) {
        $this->extraFilters[$key] = $value;
    }
    
    public function addExtraFilters($extraFilters = array()) {
        $this->extraFilters = $extraFilters;
    }
    
    public function getExtraFilters(Zend_Db_Select $select) {
        if(!empty($this->extraFilters)) {
            foreach($this->extraFilters as $key => $value) {
                if(is_array($value)) {
                    $select->where($key .= ' IN (?)', $value);
                } else {
                    $select->where($key .= ' = ?', $value);
                }
                
            }
        }
        return $select;
    }

}
