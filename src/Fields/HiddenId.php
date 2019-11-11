<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;
use mysql_xdevapi\Exception;

class HiddenId extends Field
{
    public function render()
    {
        return "<input type='hidden' name='{$this->name}' value='{$this->value}'/>";
    }

    public function load($data = [], $files = [])
    {

    }
}
