<?php

class Sudoku_Controller_Action extends My_Controller_Action {
    
    protected $scripts = array();
    protected $scripts_src = array();
    
    public function init() {
        parent::init();
        
    }
    
    public function postDispatch() {
        parent::postDispatch();
        if(!empty($this->scripts)) {
            Zend_Layout::getMvcInstance()->assign('scripts', $this->scripts);
            Zend_Layout::getMvcInstance()->assign('scripts_src', $this->scripts_src);
        }
    }
    
}

