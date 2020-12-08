<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\Html;

class SelectField extends Field
{
    public $options = [];
    public $showEmpty = true;

    public function __construct($config, &$instance, &$form)
    {
        parent::__construct($config, $instance, $form);

        if (isset($config['options'])) {
            if (is_callable($config['options'])) {
                $this->options = $config['options']($this);
            } else {
                $this->options = $config['options'];
            }
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
        $attributes = $this->getInputAttributes();
        $out = '';

        foreach ($this->options as $key => $one) {
            $selected = $key == $this->getValue() ? 'selected' : '';
            $out .= Html::tag('option', $one, ['value' => $key, 'selected' => $selected ? true : null]);
        }

        return Html::tag('select', $out, $attributes);
    }
}
