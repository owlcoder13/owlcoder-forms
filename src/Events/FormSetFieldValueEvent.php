<?php

namespace Owlcoder\Forms\Events;

class FormSetFieldValueEvent extends Event
{
    public $attribute;
    public $instance;
    public $value;
}
