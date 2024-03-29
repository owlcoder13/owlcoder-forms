<?php

namespace Owlcoder\Forms\Validation;

class StripTagsValidator extends Validator
{
    public $pattern = null;

    public function validate()
    {
        $value = $this->getValue();
        $this->setValue(strip_tags($value));
    }
}
