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

    public function render()
    {
        $id = $this->id;

        return Html::tag('div', 'loading...', [
            'id' => $id,
            'data-data' => json_encode($this->value),
        ]);
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
