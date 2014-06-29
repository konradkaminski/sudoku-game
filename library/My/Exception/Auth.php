<?php

class My_Exception_Auth extends Zend_Exception {
    
    const MSG = "Brak uprawnień";
    
    public function __construct($msg = '', $code = 0, Exception $previous = null) {
        if(empty($msg)) {
            $msg = My_Exception_Auth::MSG;
        }
        parent::__construct($msg, $code, $previous);
    }
    
}

