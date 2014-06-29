<?php

class My_Form extends Twitter_Bootstrap_Form {
    
        /**
     * Override the base form constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        $this->_initializePrefixes();

        parent::__construct($options);
    }
    
    protected function _initializePrefixes()
    {
        if (!$this->_prefixesInitialized)
        {
            if (null !== $this->getView())
            {
                $this->getView()->addHelperPath(
                    'My/View/Helper',
                    'My_View_Helper'
                );
            }

            $this->addPrefixPath(
                'My_Form_Element',
                'My/Form/Element',
                'element'
            );

            $this->_prefixesInitialized = true;
        }
    }    
    
}

