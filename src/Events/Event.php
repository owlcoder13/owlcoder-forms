<?php

namespace Owl\OwlForms\Events;

class Event
{
    public $prevented = false;

    public function preventDefault($value = true)
    {
        $this->prevented = $value;
    }
}
