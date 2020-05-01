<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\EventTrait;
use Owlcoder\Forms\Validation\FieldValidation;
use Owlcoder\Forms\Form;

use Owlcoder\Common\Helpers\DataHelper;
use stringEncode\Exception;

class TimeField extends Field
{
    public function js()
    {
        return "$(el).timepicker({});";
    }
}
