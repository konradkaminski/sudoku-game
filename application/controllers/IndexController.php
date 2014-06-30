<?php

class IndexController extends Sudoku_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace('sudoku');
        $session->isChallenge = false;
        // action body
    }


}

