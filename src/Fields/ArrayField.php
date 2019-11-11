<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;
use Illuminate\Support\Arr;

class ArrayField extends Field
{

    public $model;
    public $createInstance;
    public $nestedConfig;

    public $onBeforeEachSave;

    public $forms = [];

    public $template = 'array-field';

    public $hiddenForm = null;

    /**
     * Create hidden form
     * ArrayField constructor.
     * @param array $config
     * @param $instance
     * @param Form $form
     */
    public function __construct(array $config, &$instance, Form &$form)
    {
        parent::__construct($config, $instance, $form);

        $this->hiddenForm = $this->createHiddenForm();
    }

    /**
     * Validate all forms
     * @return bool
     */
    public function validate()
    {
        $valid = 1;
        foreach ($this->forms as $one) {
            $valid &= $one->validate();
        }

        return $valid === 1;
    }

    public function createForm($instance, $index)
    {
        $name = join('.', [$this->namePrefix, $this->attribute, $index]);

        $config = [
            'namePrefix' => $name,
        ];

        if (isset($this->config['nestedConfig']) && is_array($this->config['nestedConfig'])) {
            $config = array_merge($config, $this->config['nestedConfig']);
        }

        $newForm = new Form($config, $instance, $this->form);

        return $newForm;
    }

    /**
     * Render field with template
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        return view('forms::' . $this->template, ['field' => $this]);
    }

    /**
     * Create new instance from fields with null values
     * @return array
     */
    public function createEmptyInstance()
    {
        $out = [];

        foreach ($this->config['nestedConfig']['fields'] as $field) {
            $out[$field['attribute']] = null;
        }

        return $out;
    }

    /**
     * clear method to cancel apply
     */
    public function beforeSave()
    {

    }

    /**
     * apply after other field saved
     */
    public function afterSave()
    {
        $this->apply();
    }

    /**
     * Get value from nested forms
     * @return array
     */
    public function getValue()
    {
        return array_map(function (Form $form) {
            return $form->toArray();
        }, $this->forms);
    }

    public function createInitialForms()
    {
        if (is_array($this->value)) {
            foreach ($this->value as $key => $row) {
                $this->forms[] = $this->createForm($row, $key);
            }
        }
    }

    /**
     * Create nested forms
     */
    public function init()
    {
        parent::init();
        $this->createInitialForms();
        $this->hiddenForm = $this->createHiddenForm();
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

        $localData = data_get($data, $this->attribute);
        $localFiles = data_get($files, $this->attribute);

        foreach ($localData as $k => $formData) {

            /**
             * We don't want to save hidden form. Ignore it
             */
            if ($k === '__index__') {
                continue;
            }

            $newModel = $this->createEmptyInstance();
            $form = $this->createForm($newModel, $k);
            $this->forms[] = $form;

            $form->load($formData, $localFiles[$k] ?? null);
        }
    }

    /**
     * Create hidden form for dynamically add new record
     * @return Form
     */
    public function createHiddenForm()
    {
        return $this->createForm($this->createEmptyInstance(), '__index__');
    }

    /**
     * Render form js with template: dynamic form array
     * @return string
     */
    public function js()
    {
        return Form::removeScriptTag(view('forms::formset-field-js', [
            'field' => $this
        ]));
    }

    /**
     * build context for rendering
     * @return array
     */
    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'forms' => $this->forms
        ]);
    }

    /**
     * serializer field data
     * @return array
     */
    public function toArray()
    {
        return [
            $this->attribute => array_map(function (Form $childForm) {
                return $childForm->toArray();
            }, $this->forms)
        ];
    }
}
