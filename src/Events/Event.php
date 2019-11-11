<?php

namespace Owlcoder\Forms\Events;

class Event
{
    public $prevented = false;

    public function preventDefault($value = true)
    {
        $this->prevented = $value;
    }
}
