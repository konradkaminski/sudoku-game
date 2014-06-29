<?php

class My_Exception_Unique extends Zend_Exception {
    
    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        parent::__construct($msg, $code, $previous);
    }
    
}

