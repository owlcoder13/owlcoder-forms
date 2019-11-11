<?php

namespace Owl\OwlForms\Validation;

class NumberValidator extends Validator
{
    public $pattern = null;

    public $message = 'Value must be a number';

    public function validate()
    {
        $value = $this->getValue();

        if ( ! is_numeric($value)) {
            $this->addError($this->message);
        }
    }
}
