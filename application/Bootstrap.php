<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
    }
    
    protected function _initAutoloader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Sudoku_');
        $this->_resourceLoader();
        return $autoloader;
    }    
    
    protected function _resourceLoader() {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => APPLICATION_PATH,
            'namespace'     => '',
            'resourceTypes' => array(
                'model' => array(
                    'path'      => 'models/',
                    'namespace' => 'Model',
                ),
                'form' => array(
                    'path'      => 'forms/',
                    'namespace' => 'Form',
                ),
            ),
        ));
    }    
    

}

