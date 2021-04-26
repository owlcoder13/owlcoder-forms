<?php

namespace Owlcoder\Forms\Validation;

use Owlcoder\Forms\Form;

/**
 * Trait FormValidation
 * @package Owlcoder\Forms\Validation
 * @property Form $this
 */
trait FormValidation
{
    public $errors = [];

    public function addError($attribute, $errors)
    {
        if (empty($this->errors[$attribute])) {
            $this->errors[$attribute] = [];
        }

        $this->errors[$attribute][] = $errors;
    }

    /**
     * @var array
     */
    public $rules = [];

    public $formRules = [];

    public function initRules()
    {
        foreach ($this->rules as $one) {
            $fields = $one[0];
            $validator = $one[1];

            if ($fields == '*') {
                $this->formRules[] = $validator;
                continue;
            }

            if ($fields == '__all__') {
                foreach ($this->fields as $field) {
                    $field->rules[] = $validator;
                }
            }

            if ( ! is_array($fields)) { // if simple string
                $fields = [$fields];
            }


            foreach ($fields as $field) {
                if ( ! empty($this->fields[$field])) {
                    $this->fields[$field]->rules[] = $validator;
                }
            }

        }
    }

    /**
     * Rules for form validation
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function validate()
    {
        $this->errors = [];

        $this->triggerEvent(self::BEFORE_VALIDATE, $this);

        foreach ($this->fields as $field) {
            if ( ! $field->validate() && ! empty($field->errors)) {
                $this->addError($field->attribute, $field->errors);
            }
        }

        $this->triggerEvent(Form::AFTER_VALIDATE, $this);

        $this->validateFormRules();

        return count($this->errors) == 0;
    }

    public function validateFormRules()
    {
        foreach ($this->formRules as $formRule) {

            if (is_callable($formRule)) {
                call_user_func($formRule);
            }

            if ($formRule instanceof \Closure) {
                $formRule();
            }
        }
    }
}
