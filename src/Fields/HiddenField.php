<?php

namespace Owlcoder\Forms\Fields;

class HiddenField extends Field
{
    public $type = 'hidden';

    public function render()
    {
        return $this->renderInput();
    }
}
