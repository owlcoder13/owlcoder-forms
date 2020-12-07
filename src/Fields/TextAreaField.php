<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\Html;

class TextAreaField extends Field
{
    public $rows = 6;

    public function renderInput()
    {
        $attributes = array_merge($this->getInputAttributes(), [
            'rows' => $this->rows,
        ]);

        return Html::tag('textarea', $this->value, $attributes);
    }
}
