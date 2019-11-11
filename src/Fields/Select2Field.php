<?php

namespace Owlcoder\Forms\Fields;

class Select2Field extends SelectField
{
    public $template = 'forms.select';

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'options' => $this->options,
        ]);
    }
}
