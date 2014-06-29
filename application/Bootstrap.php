<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        $view->addFilterPath('My/View/Filter', 'My_View_Filter_')
            ->addFilter('Translate');
    }
    
    protected function _initAutoloader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Sudoku_');
        $autoloader->registerNamespace('My_');
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
    
    protected function _initDb()
    {
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();
        $db->query("SET NAMES 'utf8'");
        Zend_Registry::set("db", $db);
    }
    
        protected function _initTranslation() {
        $localeValue = 'pl';
        $locale = new Zend_Locale($localeValue);
        Zend_Registry::set('Zend_Locale', $locale);
        $translationFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $localeValue . '.inc.php';
        $translate = new Zend_Translate('array', $translationFile, $localeValue);
        Zend_Registry::set('Zend_Translate', $translate);
//        Zend_Form::setDefaultTranslator($translate);
    }
    
    

}

