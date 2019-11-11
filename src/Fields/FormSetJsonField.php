<?php

namespace Owlcoder\Forms\Fields;

class FormSetJsonField extends FormSetField
{
    public function fetchData()
    {
        parent::fetchData();

        $this->value = json_decode($this->value, true);
    }

    public function apply()
    {
        parent::apply(json_encode($this->getValue()));
    }
}
