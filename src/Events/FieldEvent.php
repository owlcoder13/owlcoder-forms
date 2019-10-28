<?php

namespace Owl\OwlForms;

class FieldEvent
{

    public $field;
    public $eventType;

    public function __construct($field)
    {
        $this->field = $field;
    }


}
