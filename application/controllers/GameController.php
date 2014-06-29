<?php

class GameController extends Sudoku_Controller_Action
{

    private $sudokuGame = null;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->sudokuGame = new Zend_Session_Namespace('sudokuGame');
    }

    public function indexAction()
    {
        $this->scripts[] = '/js/app/Sudoku.js';
        // action body
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/views/scripts/game/');
        $this->scripts_src[] = $html->render('index-js.phtml');
    }
    
    public function resetAction() {
        $this->_helper->layout->setLayout('empty');
        $data = Sudoku_Generator::getTable();
        $this->sudokuGame->game = $data;
        $this->view->table = json_decode($data['mask']);
    }

    public function checkAction() {
        $this->_helper->layout->setLayout('empty');
//        Zend_Debug::dump($this->getRequest()->getParam('data'));
//        Zend_Debug::dump($this->sudokuGame->game);
        die;
        
    }

}

