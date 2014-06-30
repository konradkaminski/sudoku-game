<?php

class My_Check {
    public static function p($pass, $throw = false) {
        return true;
        if($pass == 'index_auth_logout' || $pass == 'index_jscript_index') {
            return true;
        }
        $session = new Zend_Session_Namespace("current_user");
        
        if( isset($session->user) &&  !$session->user->hasPermit($pass)) {
            if($throw) {
                throw new My_Exception_Auth($pass);
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

