<?php

namespace Owl\OwlForms\Fields;

class FormSetJsonField extends FormSetField
{
    public function getValue()
    {
        return json_decode(parent::getValue(), true);
    }

    public function setValue($value)
    {
        parent::setValue(json_encode($value));
    }
}
