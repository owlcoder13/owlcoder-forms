<?php

namespace Owlcoder\Forms\Fields;

class TextAreaField extends Field
{
    public function renderInput()
    {
        $attributes = $this->buildInputAttributes([
            'value' => $this->escapeAttrValue($this->getValue()),
        ]);

        return "<textarea {$attributes} type='text'>{$this->value}</textarea>";
    }
}
