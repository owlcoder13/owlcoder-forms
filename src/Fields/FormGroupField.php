<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\DataHelper;
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

        $config = $this->nestedConfig;

        // create name prefix
        $name = join('.', [$this->namePrefix, $this->attribute]);
        $config['namePrefix'] = $name;

        $this->form = new Form($config, $this->value);
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

    /**
     * Reset our forms and create new ones
     *
     * @param $data
     * @param $files
     */
    public function load($data, $files)
    {
        $this->forms = [];

        $this->data = $data;
        $this->files = $files;

        $localData = DataHelper::get($data, $this->attribute);
        $localFiles = DataHelper::get($files, $this->attribute);

        $this->form->load($localData, $localFiles ?? null);
    }
}
