<?php

namespace Owlcoder\Forms\Events;

use Owlcoder\Forms\Fields\Field;

class FormSetInstanceValueEvent extends Event
{
    public $attribute;
    public $instance;
    public $value;

    /** @var Field */
    public $field;

    public $data;
    public $files;
}
