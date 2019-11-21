<?php

namespace Owlcoder\Forms\Validation;

use Owlcoder\Forms\Fields\Field;

class Validator
{
    /** @var Field */
    public $field;
    public $if;

    public function __construct($field, $options = [])
    {
        $this->field = $field;

        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Если не задано условие if то вызывается валидация
     * Если задано - сначала идёт проверка - можно ли вызывать валидатор
     */
    public function makeValidation()
    {
        $if = $this->if;
        if ($this->if != null && is_callable($if)) {
            if ($if($this->field->form, $this->field)) {
                $this->validate();
            }
        } else {
            $this->validate();
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
