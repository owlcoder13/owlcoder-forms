<?php

namespace Owlcoder\Forms\Fields;

class TextAreaField extends Field
{
    public $rows = 6;

    public function renderInput()
    {
        $attributes = $this->buildInputAttributes([
//            'value' => $this->escapeAttrValue($this->getValue()),
            'rows' => $this->rows,
        ]);

        return "<textarea {$attributes} type='text'>{$this->value}</textarea>";
    }
}
