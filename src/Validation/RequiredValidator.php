<?php

namespace Owlcoder\Forms\Validation;

class RequiredValidator extends Validator
{
    public $pattern = null;

    public $message = 'Field "%s" must be filled';

    public function validate()
    {
        $value = $this->getValue();

        if (empty($value)) {
            $this->addError(sprintf($this->message, $this->field->label));
        }
    }
}
