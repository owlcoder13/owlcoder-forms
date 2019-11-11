<?php

namespace Owl\OwlForms\Validation;

use Owl\OwlForms\Fields\Field;

class Validator
{
    /** @var Field */
    public $field;

    public function __construct($field, $options = [])
    {
        $this->field = $field;

        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    public function validate()
    {

    }

    public function addError($message)
    {
        $this->field->addError($message);
    }

    public function getValue()
    {
        return $this->field->value;
    }

    public function setValue($value)
    {
        $this->field->value = $value;
    }
}
