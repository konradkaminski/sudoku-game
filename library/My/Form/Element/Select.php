<?php

class My_Form_Element_Select extends Zend_Form_Element {
    public $options = array();
    public $helper = 'Select';
    /**
     * Adds a new <option>
     * @param string $value value (key) used internally
     * @param string $label label that is shown to the user
     * @param array $attribs additional attributes
     */
    public function addOption ($value,$label = '',$attribs = array()) {
        $value = (string) $value;
        if (!empty($label)) $label = (string) $label;
        else $label = $value;
        $this->options[$value] = array(
            'value' => $value,
            'label' => $label
        ) + $attribs;
        return $this;
    }
}

