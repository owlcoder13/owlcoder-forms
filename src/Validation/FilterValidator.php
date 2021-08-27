<?php

namespace Owlcoder\Forms\Validation;

class FilterValidator extends Validator
{
    public $function = 'trim';

    public function validate()
    {
        $value = $this->getValue();

        if (is_callable($this->function)) {
            $callFunc = $this->function;
            $newValue = $callFunc($value);
        } else if (is_string($this->function)){
        $newValue = call_user_func($this->function, $value);
    }

        $this->setValue($newValue);
    }
}
