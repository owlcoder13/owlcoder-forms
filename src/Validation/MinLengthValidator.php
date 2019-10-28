<?php

namespace Owl\OwlForms\Validation;

class MinLengthValidator extends Validator
{
    public $value = null;

    public $message = 'Minimum string length is %s';

    public function validate()
    {
        $value = $this->getValue();

        if ($this->max != null && mb_strlen($value) > $this->value) {
            $this->addError(sprintf($this->message, $this->value));
        }
    }
}