<?php

namespace Owlcoder\Forms;

class FormEvent
{
    public $prevented = false;
    public $field;
    public $eventType;

    public function __construct($field)
    {
        $this->field = $field;
    }

    public function preventDefault($value = true)
    {
        $this->prevented = $value;
    }
}
