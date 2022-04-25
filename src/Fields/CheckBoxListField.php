<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\Html;
use Owlcoder\Forms\Fields\Field;

class CheckBoxListField extends Field
{
    public $options = [];

    public function renderInput()
    {
        $out = [];

        $options = $this->getOptions();
        $inputValue = $this->value ?? [];

        foreach ($options as $key => $value) {
            $inputOptions = [
                'value' => $key,
                'type' => 'checkbox',
                'name' => $this->name . '[]',
            ];

            if (in_array($key, $inputValue)) {
                $inputOptions['checked'] = 1;
            }

            $checkBox = Html::tag('input', '', $inputOptions) . ' ' . $value;

            $out[] = Html::tag('div', $checkBox);
        }

        return Html::tag('div', join('', $out));
    }

    public function getOptions()
    {
        if ($this->options instanceof \Closure) {
            $closure = $this->options;
            return $closure($this);
        }

        return $this->options;
    }
}
