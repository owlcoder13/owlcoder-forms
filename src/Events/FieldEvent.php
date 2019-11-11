<?php

namespace Owlcoder\Forms;

class FieldEvent
{

    public $field;
    public $eventType;

    public function __construct($field)
    {
        $this->field = $field;
    }


}
