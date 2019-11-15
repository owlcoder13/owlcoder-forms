<?php

namespace Owlcoder\Forms\Fields;

class CheckBoxField extends Field
{
    public function renderInput()
    {
        $attributes = $this->buildInputAttributes([
            'type' => 'hidden',
            'class' => '',
            'value' => '0',
            'id' => '',
        ]);

        $attributesActive = [
            'type' => 'checkbox',
            'class' => '',
            'value' => '1',
        ];

        if ($this->value) {
            $attributesActive['checked'] = 1;
        }

        $attributesActive = $this->buildInputAttributes($attributesActive);

        return "<input $attributes >" . "<input $attributesActive >";
    }
}
