<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;
use mysql_xdevapi\Exception;

class HiddenId extends Field
{
    public function render()
    {
        return "<input type='hidden' name='{$this->name}' value='{$this->getValue()}'/>";
    }

    public function apply()
    {
        // id always not settable
        // parent::apply();
    }
}
