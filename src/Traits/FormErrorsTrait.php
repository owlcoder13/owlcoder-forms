<?php

namespace Owlcoder\Forms\Traits;

trait FormErrorsTrait
{
    public $errors = [];

    public function addError($attribute, $errors)
    {
        $this->errors[$attribute] = $errors;
    }
}
