<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class ManyToOneModelsField extends Field
{

    public $template = 'forms.many-to-one-models';
    public $forms = [];
    public $hiddenForm = null;
    public $formsToDelete = [];

    public function getInitialValue()
    {
        // try to get from config
        if (isset($this->config['getInitialValue'])) {
            return $this->config['getInitialValue']($this->instance);
        }

        // get from instance
        return data_get($this->instance, $this->attribute);
    }

    public function createHiddenForm($parentForm)
    {
        $emptyModel = new $this->config['modelClassName'];
        return $this->createForm($emptyModel, '__index__');
    }

    public function __construct($config, $instance, $form = null)
    {
        parent::__construct($config, $instance, $form);

        $this->name .= '';
        $this->value = iterator_to_array($this->getInitialValue());

        $this->hiddenForm = $this->createHiddenForm($form);

        foreach ($this->value as $key => $model) {

            $newForm = $this->createForm($model, $key);
            $this->forms[] = $newForm;
        }
    }

    /** @var HasMany */
    public function getRelation()
    {
        $methodName = $this->attribute;
        return $this->instance->$methodName();
    }

    public function getRelatedClass()
    {
        return $this->getRelation()->getRelated();
    }

    public function save()
    {

    }

    public function getValueFromData()
    {
        return $this->data[$this->attribute];
    }

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'forms' => $this->forms,
        ]);
    }

    public function createForm($instance, $index)
    {

        $name = join('.', [$this->namePrefix, $this->attribute, $index]);

        $newForm = Form::Build([
            'fields' => $this->config['fields'],
            'namePrefix' => $name,
        ], $instance, $this->form);

        return $newForm;
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

        $modelClass = $this->config['modelClassName'];

        $localData = data_get($data, $this->attribute);


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
                    return $form->instance->id === (int) $formData['id'];
                });
            } else {
                $newModel = new $modelClass;
                $form = $this->createForm($newModel, ++$maxKeys);
                $this->forms[] = $form;
            }

            $form->load($formData, $files[$k] ?? []);
        }


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

//        $initial = $this->getInitialValue();
//        $initialIds = array_column($initial, 'id');
//
//        $not_to_delete = [];
//        $data = $this->getValueFromData();
//
//        foreach ($data as $id) {
//
//            if (isset($data['id'])) {
//                $tmp = Arr::first($initial, function ($item) use ($id) {
//                    return $item->id == $id;
//                });
//            } else {
//                $modelClass = $this->config['modelClassName'];
//                $tmp = new $modelClass;
//            }
//
//            if ($tmp == null) {
//
//                if (isset($this->config['beforeEachSave'])) {
//                    $this->config['beforeEachSave']($tmp, $this);
//                }
//
//                $tmp->save();
//            }
//
//            $not_to_delete[] = $tmp->id;
//        }
//
//        foreach ($models as $one) {
//            if ( ! in_array($one->id, $not_to_delete)) {
//                $one->delete();
//            }
//        }
    }

    public function js()
    {
        return Form::removeScriptTag(view('forms.many-to-one-models-js', [
            'field' => $this
        ]));
    }
}
