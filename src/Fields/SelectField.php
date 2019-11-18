<?php

namespace Owlcoder\Forms\Fields;

class SelectField extends Field
{
    public $options;
    public $showEmpty = true;

    public function __construct($config, &$instance, &$form)
    {
        parent::__construct($config, $instance, $form);

        if (is_callable($config['options'])) {
            $this->options = $config['options']($this);
        } else {
            $this->options = $config['options'];
        }

        $this->showEmpty = $config['showEmpty'] ?? $this->showEmpty;
    }

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'options' => $this->options,
        ]);
    }

    public function renderInput()
    {
        $attributes = $this->buildInputAttributes();

        $out = "<select {$attributes}>";

        foreach ($this->options as $key => $one) {
            $selected = $key == $this->getValue() ? 'selected' : '';
            $out .= "<option $selected value='$key'>{$one}</option>";
        }

        $out .= "</select>";

        return $out;
    }
}
