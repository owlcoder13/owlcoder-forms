<?php

namespace Owl\OwlForms\Events;

use Owl\OwlForms\Fields\Field;

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
