<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Support\Arr;

class FormSetField extends ArrayField
{
    public $model;
    public $formCount = 2;
    public $createInstance;
    public $nestedConfig;

    public $onCreateInstance;
    public $onCreateEmptyInstance;

    /** @var Form[] nested form holder */
    public $forms = [];

    /** @var Form[] will be deleted while save */
    public $formsToDelete = [];

    public $template = 'formset-field';

    public $hiddenForm = null;

    public function createEmptyInstance()
    {
        if ($this->onCreateInstance) {
            return call_user_func($this->onCreateInstance, [$this]);
        }

        return [];
    }

    public function getValue()
    {
        return array_map(function (Form $form) {
            return $form->toArray();
        }, $this->forms);
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

            // if id exists
            if (isset($formData['id'])) {
                $form = Arr::first($this->forms, function (Form $form) use ($formData) {
                    return data_get($form->instance, 'id') === (int) $formData['id'];
                });
            } else {

                /**
                 * form is new => create new model
                 */

                $newModel = $this->createEmptyInstance();
                $form = $this->createForm($newModel, ++$maxKeys);
                $this->forms[] = $form;
            }

            $form->load($formData, $localFiles[$k] ?? null);
        }
    }

    public function js()
    {
        return Form::removeScriptTag(view('forms::formset-field-js', [
            'field' => $this
        ]));
    }

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'forms' => $this->forms
        ]);
    }

    public function toArray()
    {
        return [
            $this->attribute => array_map(function (Form $childForm) {
                return $childForm->toArray();
            }, $this->forms)
        ];
    }

    public function setValue($value)
    {
        parent::setValue($value);
    }
}
