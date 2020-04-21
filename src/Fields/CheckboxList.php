<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;
use Owlcoder\Common\Helpers\Html;

class CheckboxList extends Field
{
    public $options = [];

    public function __construct(array $config, &$instance, Form &$form)
    {
        parent::__construct($config, $instance, $form);
        $this->name .= '[]';
    }

    /**
     * Reset value to empty array if value does not exists
     * @param $data
     * @param $files
     */
    public function load($data, $files)
    {
        parent::load($data, $files);

        if ( ! $this->dataHasValue($data, $files)) {
            $this->value = [];
        }
    }

    public function fetchData()
    {
        parent::fetchData();

        if ( ! is_array($this->value)) {
            $this->value = [];
        }
    }

    public function render()
    {
        $out = [];

        $value = $this->value;

        foreach ($this->options as $optionValue => $optionName) {

            $checkBoxAttribute = [
                'value' => $optionValue,
                'type' => 'checkbox',
                'name' => $this->name
            ];

            if (in_array($optionValue, $value)) {
                $checkBoxAttribute['checked'] = '';
            }

            $checkbox = Html::tag('input', null, $checkBoxAttribute);
            $label = Html::tag('label', $checkbox . ' ' . $optionName);
            $out[] = $label;
        }

        return Html::tag("h3", $this->label) . join("\n", $out) . $this->renderErrors();
    }
}
