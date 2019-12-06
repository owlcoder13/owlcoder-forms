<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\ViewHelper;
use Owlcoder\Forms\Form;
use Illuminate\Support\Arr;

class FormGroupField extends Field
{
    public $nestedConfig;

    /**
     * @var Form
     */
    public $form;

    public function __construct(array $config, &$instance, Form &$form)
    {
        parent::__construct($config, $instance, $form);

        if ( ! isset($config['nestedConfig'])) {
            throw new \Exception('Array field must have nestedConfig attribute');
        }
    }

    public function init()
    {
        parent::init();

        $this->form = new Form($this->nestedConfig, $this->value);
    }

    public function getValue()
    {
        return $this->form->toArray();
    }

    public function render()
    {
        $template = __DIR__ . '/../../resources/views/form-group-field.php';
        return ViewHelper::Render($template, [
            'field' => $this,
        ]);
    }

    public function js()
    {
        $template = __DIR__ . '/../../resources/views/form-group-field-js.php';
        return Form::removeScriptTag(
            ViewHelper::Render($template,
                ['field' => $this])
        );
    }
}
