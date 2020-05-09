<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\Html;

/**
 * Class DivField
 * @package Owlcoder\Forms\Fields
 */
class DivField extends Field
{
    public $func;
    public $inputAttributes = [];

    public function render()
    {
        $inputAttributes = $this->getInputAttributes();

        return Html::tag('div', 'loading...', array_merge([
            'data-data' => json_encode($this->value),
        ], $inputAttributes));
    }

    public function js()
    {
        return "$(el)." . $this->func . "();";
    }

    public function load($data, $files)
    {
        $this->value = $this->getValueFromData($data, $files);
    }
}
