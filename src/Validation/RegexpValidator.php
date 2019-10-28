<?php

namespace Owl\OwlForms\Validation;

class RegexpValidator extends Validator
{
    public $pattern = null;

    public $message = 'Regexp match fail: %s';

    public function validate()
    {
        $value = $this->getValue();

        if ( ! mb_ereg_match($this->pattern, $value)) {
            $this->addError(sprintf($this->message, $this->pattern));
        }
    }
}