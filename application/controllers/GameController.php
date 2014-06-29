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
        $session = new Zend_Session_Namespace('sudoku');
        $session->sudokuGame = $data;
        $session->ts = time();
        $this->view->table = json_decode($data['mask']);
    }

    public function checkAction() {
        $out = array(
          'isValid' => false,
          'msg' => ''
        );
        $this->_helper->layout->setLayout('empty');
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParam('data');
            $data = trim($data, "=");
            $fields = explode("=", $data);
            if(count($fields) == 81) {
                $result = array();
                $valid = 0;
                foreach($fields as $field) {
                    $d = explode("|", $field);
                    if(!array_key_exists($d[0], $result)) {
                        $result[$d[0]] = array();
                    }
                    $result[$d[0]][$d[1]] = (int)$d[2];
                    if(!empty($result[$d[0]][$d[1]])) {
                        $valid++;
                    }
                }
                
                if($valid == 81) {
                    $rsp = array();
                    foreach ($result as $resultLine) {
                        $rsp[] = '[' . implode(',', $resultLine) . ']';
                    }
                    $rspString = '[' . implode(',', $rsp) . ']';
                    if($data['table'] != $rspString) {
                        $out['msg'] = 'Niepoprawne rozwiązanie';
                    } else {
                        $time = time() - $session->ts;
                        $out['msg'] = 'Poprawne rozwiązanie';
                        $out['isValid'] = true;
                 
                        $session = new Zend_Session_Namespace('sudoku');
                        $data = $session->sudokuGame;

                        $dbSource = new Model_Game_Mapper();
                        $dataSig = $dbSource->find($data['sig']);
                        $tableSig = $dbSource->find($data['sig_table']);
                        $id = -1;
                        if(empty($dataSig['rows']) && empty($tableSig['rows'])) {
                            $model = new Model_Game();
                            $model->setDatasig($data['sig']);
                            $model->setTablesig($data['sig_table']);
                            $model->setData($data['data']);
                            $id = $dbSource->save($model);
                        } else if(!empty($dataSig['rows'])) {
                            $id = $dataSig['rows'][0]->id;
                        } elseif ( !empty($tableSig['rows'])) {
                            $id = $tableSig['rows'][0]->id;
                        }
                        $gr = new Model_Gameresult();
                        $gr->setIdUser();
                        $gr->setIdGame($id);
                        $gr->setTime($time);
                        $gr->setHits(0);
                        $dbSource = new Model_Gameresult_Mapper();
                        $dbSource->save($gr);
                    }
                } else {
                    $out['msg'] = 'Nie wypełniono wszystkich pól';
                }
            }
        }
        $this->view->data = json_encode($out);
    }
    
    
    public function challengeAction() {
        $session = new Zend_Session_Namespace('sudoku');
        $session->isChallenge = true;
        return $this->_redirect('/game');
    }
    
    public function rankingAction() {
        $sql = "select 
	us.email, gr.time
from 
	game_results gr
	LEFT JOIN users us ON (gr.id_user = us.id)

order by time
LIMIT 10 ";
        
        $db = Zend_Registry::get('db');
        $rows = $db->query($sql);
        
        $tmpRows = array();
        
        while($row = $rows->fetch()) {
            $tmpRows[] = $row;
        }
        
        $this->view->rows = $tmpRows;
    }
    

}

