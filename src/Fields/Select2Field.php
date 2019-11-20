<?php

namespace Owlcoder\Forms\Fields;

class Select2Field extends SelectField
{
    public function js()
    {
        return '$(el).select2();';
    }
}
