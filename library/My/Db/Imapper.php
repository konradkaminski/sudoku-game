<?php


/**
 * interfejs podstawowy, umozliwia:
 *  
 */
interface My_Db_Imapper {
    public function setDbTable($table); // ?? - do klasy bazowej
    public function getDbTable(); // ?? - do klasy bazowej
    public function getById($id);
    public function fetchAll(array $order = array());
    public function find($search, $limit, $offset, array $order = array());
    public function findCount($search = null);
    public function save(My_Db_Object $newObject, My_Db_Object $oldObject = null);
    public function isUnique($fields = array(), $id = null);
    public function addExtraFilters($extraFilters = array());
    public function addExtraFilter($key, $value);
}

