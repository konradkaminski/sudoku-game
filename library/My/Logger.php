<?php

class My_Logger {
    
    const MSG_LOGIN = 'Zalogowanie użytkownika';
    
    const MSG_CREATE = 1;
    const MSG_UPDATE = 2;
    const MSG_DELETE = 3;
    const MSG_PASSWORD = 4;
    
    private static $messagesType = array(
        self::MSG_CREATE => 'Utworzenie wpisu',
        self::MSG_UPDATE => 'Edycja wpisu',
        self::MSG_DELETE => 'Usunięcie wpisu',
        self::MSG_PASSWORD => 'Zmiana hasła'
    );
    
    private static $dataTypes = array(
        // equipment
        'Model_Trade' => 'branża',
        'Model_Group' => 'grupa',
        'Model_Type' => 'typ',
        'Model_Producer' => 'producent',
        'Model_Equipment' => 'wyposażenie',
        'Model_Supplier' => 'dostawca',
        'Model_Inspection' => 'przegląd',
        // cms
        'Model_News' => 'aktualność',
        // structure
        'Model_Owner' => 'właściciel',
        'Model_Structure' => 'obiekt',
        'Model_Building' => 'budynek',
        'Model_Section' => 'pion',
        'Model_Level' => 'poziom',
        'Model_Place' => 'pomieszczenie',
        // default
        'Model_User' => 'użytkownik',
        'Model_Permit' => 'przepustka',
        'Model_Role' => 'rola',
        
    );
    
    public static function logData($msgType, $object, $extra = null) {
        if(!in_array($msgType, array_keys(self::$messagesType))) {
            throw new Exception("Incorrect event type");
        }
        $type = get_class($object);
        
        if(!in_array($type, array_keys(self::$dataTypes))) {
            throw new Exception("Incorrect data type: " . $type);
        }
        if(!empty($extra)) {
            $extra = '<br />' . $extra;
        }
        self::log(sprintf("%s: %s%s", self::$messagesType[$msgType], self::$dataTypes[$type], $extra), $object->getArrayData);
    }
    
    public static function login() {
        $data = array();
        if(array_key_exists('REMOTE_ADDR', $_SERVER)) $data['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) $data['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        self::log(My_Logger::MSG_LOGIN, $data);
    }
    
    private static function log($message, $data = array()) 
    {
        $session = new Zend_Session_Namespace("current_user");
        $logger = Zend_Registry::get('logger');
        $logger->setEventItem('description', $message);
        $logger->setEventItem('date', date('Y-m-d H:i:s'));
        $logger->setEventItem('username', $session->user->username);
        $logger->setEventItem('data', json_encode($data));
        $logger->info("message");
        
    }
    
}

