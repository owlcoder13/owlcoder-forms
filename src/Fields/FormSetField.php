<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Support\Arr;

class FormSetField extends Field
{
    public $path;
    public $model;
    public $formCount = 2;
    public $createInstance;
    public $nestedConfig;

    /** @var Form[] nested form holder */
    public $forms = [];

    /** @var Form[] will be deleted while save */
    public $formsToDelete = [];

    public $template = 'formset-field';

    public $hiddenForm = null;

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

    public function render()
    {
        return view('forms::' . $this->template, ['field' => $this]);
    }

    public function createEmptyInstance()
    {
        if ($this->createInstance) {
            return call_user_func($this->createInstance, [$this]);
        }

        return [];
    }

    public function save()
    {
        // all actions in nested forms
    }

    /**
     * Сохраняет по спрятанному id
     */
    public function afterSave()
    {
        foreach ($this->forms as $form) {

            if (isset($this->config['beforeEachSave'])) {
                $this->config['beforeEachSave']($form);
            }

            $form->save();
        }

        foreach ($this->formsToDelete as $one) {
            $one->instance->delete();
        }
    }

    public function load($data, $files)
    {
        $this->formsToDelete = [];

        $this->data = $data;
        $this->files = $files;

        if (count($this->forms) > 0) {
            $maxKeys = call_user_func('max', array_keys($this->forms)) + 1;
        } else {
            $maxKeys = 0;
        }

        $localData = data_get($data, $this->attribute);
        $localFiles = data_get($files, $this->attribute);

        // fill delete forms
        $ids = array_column($localData, 'id');

        foreach ($this->forms as $one) {
            if ( ! in_array($one->instance->id, $ids)) {
                $this->formsToDelete[] = $one;
                $key = array_search($one, $this->forms);
                unset($this->forms[$key]);
            }
        }

        foreach ($localData as $k => $formData) {

            // skip hidden form
            if ($k === '__index__') {
                continue;
            }

            if (isset($formData['id'])) {
                $form = Arr::first($this->forms, function (Form $form) use ($formData) {
                    return data_get($form->instance, 'id') === (int) $formData['id'];
                });
            } else {
                $newModel = $this->createEmptyInstance();
                $form = $this->createForm($newModel, ++$maxKeys);
                $this->forms[] = $form;
            }

            $form->load($formData, $localFiles[$k] ?? null);
        }
    }

    public function createHiddenForm()
    {
        return $this->createForm($this->createEmptyInstance(), '__index__');
    }

    public function __construct(array $config, &$instance, Form &$form)
    {
        parent::__construct($config, $instance, $form);

        $this->createInstance = isset($config['createInstance'])
        && is_callable($config['createInstance']) ?
            $config['createInstance'] : null;

        $this->hiddenForm = $this->createHiddenForm();
        $arr = data_get($instance, $config['attribute']);

        if (is_array($arr)) {
            foreach ($arr as $key => $row) {
                $this->forms[] = $this->createForm($row, $key);
            }
        }

        $this->hiddenForm = $this->createHiddenForm();
    }

    public function getValue()
    {
        return null;
    }

    public function js()
    {
        return Form::removeScriptTag(view('forms.formset-field-js', [
            'field' => $this
        ]));
    }

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'forms' => $this->forms
        ]);
    }
}
