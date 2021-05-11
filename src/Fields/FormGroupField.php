<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Common\Helpers\ViewHelper;
use Owlcoder\Forms\Form;

class FormGroupField extends Field
{
    public $nestedConfig;

    /**
     * @var Form
     */
    public $nestedForm;

    public function __construct(array $config, &$instance, Form &$form)
    {
        parent::__construct($config, $instance, $form);

        if ( ! isset($config['nestedConfig']) && empty($this->nestedConfig)) {
            throw new \Exception('Array field must have nestedConfig attribute');
        }
    }

    public function init()
    {
        parent::init();
    }

    public function fetchData()
    {
        parent::fetchData();

        // create name prefix
        $this->createForm($this->value);
    }

    public function createForm($value)
    {
        if (empty($this->nestedForm)) {
            $config = $this->nestedConfig;
            $path = [$this->namePrefix, $this->attribute];
            $path = array_filter($path);
            $name = join('.', $path);

            $config['namePrefix'] = $name;

            $this->nestedForm = new Form($config, $this->value, $this->form);
        }
    }

    public function getValue()
    {
        if ($this->nestedForm) {
            return $this->nestedForm->toArray();
        }
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

        $this->nestedForm->load($localData, $localFiles ?? null);
    }
}
