<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\StringHelper;

class Select2Field extends SelectField
{
    public function js()
    {
        return '$(el).select2();';
    }
}
