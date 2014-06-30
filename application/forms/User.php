<?php

class Form_User extends Zend_Form {

    public function getModel($object = null) {
        if ($object === null || !($object instanceof Model_User)) {
            $object = new Model_User();
        }
        $data = $this->getValues();
        unset($data['repeat_password']);
        $object->setArrayData($data);
        return $object;
    }
    
    public function init() {
        $this->setMethod('post');
        $this->addElement('hidden', 'id');
        $this->addElement(
          'text', 'email', array(
          'label' => '{{i18n}login{i18n}} *',
          'class' => 'form-control',
          'required' => true,
          'placeholder' => '{{i18n}login{i18n}}',
          'maxlength' => 64,
          'validators' => array(
            array('stringLength', false, array(3, 64)),
            array('EmailAddress'),
            array('Db_NoRecordExists', true, array(
                'table' => 'users',
                'field' => 'email',
                'message' => array(
                  'recordFound'
                )
              ))
          )
          )
        );
        $this->addElement(
          'password', 'password', array(
          'label' => '{{i18n}password{i18n}} *',
          'class' => 'form-control',
          'required' => true,
          'placeholder' => '{{i18n}password{i18n}}',
           'maxlength' => 32,
          'validators' => array(
            array('stringLength', false, array(8, 32)),
          ) ));
        $this->addElement(
          'password', 'repeat_password', array(
          'label' => '{{i18n}repeat_password{i18n}} *',
          'class' => 'form-control',
          'required' => true,
          'placeholder' => '{{i18n}repeat_password{i18n}}',
            'maxlength' => 32,
          'validators' => array(
            array('stringLength', false, array(8, 32)),
            array('Identical', false, array('token' => 'password'))
          )));
        
        $this->addElement(
          'submit', 'sign_in', array(
            'label' => '{{i18n}sign in{i18n}}'
          ));
        
    }

}
