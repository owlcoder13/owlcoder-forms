<?php

namespace Owlcoder\Forms\Validation;

class EmailValidator extends Validator
{
    public $pattern = null;

    public $message = 'Email is not correct: %s';

    public function validate()
    {
        $value = $this->getValue();

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(sprintf($this->message, $this->pattern));
        }
    }
}
