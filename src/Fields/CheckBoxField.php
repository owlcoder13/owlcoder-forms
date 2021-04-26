<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\Html;

class CheckBoxField extends Field
{
    public function apply()
    {
        $this->value = (int) $this->value;
        parent::apply();
    }

    public function renderInput()
    {
        $attributes = array_merge($this->getInputAttributes(), [
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

        $attributesActive = array_merge($this->getInputAttributes(), $attributesActive);

        return Html::tag('input', '', $attributes) . Html::tag('input', '', $attributesActive);
    }
}
