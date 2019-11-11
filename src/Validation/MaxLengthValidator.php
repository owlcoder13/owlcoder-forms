<?php

namespace Owlcoder\Forms\Validation;

class MaxLengthValidator extends Validator
{
    public $value = null;

    public $message = 'Maximum string length is %s';

    public function validate()
    {
        $value = $this->getValue();

        if ($this->value != null && mb_strlen($value) > $this->value) {
            $this->addError(sprintf($this->message, $this->value));
        }
    }
}
