<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use App\Helpers\LanguageHelper;
use Illuminate\Support\Arr;

class LanguageFormSetField extends Field
{
    public $path;
    public $model;
    public $forms = [];
    public $formCount = 2;

    public $template = 'forms.language-formset-field';

    public function __construct($config = [], $instance = null, $form = null)
    {
        parent::__construct($config, $instance, $form);

        $this->instance = $instance;
        $this->form = $form;
        $this->attribute = $config['attribute'];

        $namePrefix = ! empty($config['namePrefix']) ? $config['namePrefix'] . '.' : '';
        $tmpName = $namePrefix . ($config['name'] ?? $this->attribute);
        $this->name = static::generateFromDotName($tmpName);

        $fields = array_pop_del($config, 'fields');

        $attribute = $config['attribute'];

        $arr = data_get($instance, $attribute);

        foreach (LanguageHelper::GetLanguages() as $langKey => $language) {

            $nestedInstance = Arr::first($arr, function ($item) use ($langKey) {
                return $item->language == $langKey;
            });

            if ($nestedInstance == null) {
                $relation = $instance->$attribute()->getRelated();
                $className = $relation->getMorphClass();

                $nestedInstance = new $className;
                $nestedInstance->source_id = $this->instance->id;
                $nestedInstance->language = $langKey;
            }

            $form = Form::Build([
                'idPrefix' => $this->idPrefix,
                'fields' => $fields,
                'namePrefix' => join('.', [$this->name, $language]),
            ], $nestedInstance, $form);

//            $form->prefix = $this->form->prefix . $attribute . "_{$langKey}_";
            $this->forms[$language] = $form;
        }
    }

    public function js()
    {
        return Form::removeScriptTag(view('forms.language-formset-field-js', ['field' => $this]));
    }

    public function load($data, $files)
    {
        $allData = [];

        foreach ($data[$this->attribute] as $language => $localData) {
            $allData[$language] = [
                'data' => $localData,
                'files' => [], //$files[$language] ?? []
            ];
        }

        foreach ($allData as $language => $item) {
            $this->forms[$language]->load($item['data'], $item['files']);
        }
    }

    public function afterSave()
    {
        foreach ($this->forms as $language => $form) {
            $form->save();
        }
    }

    public function toArray()
    {
        $forms = [];

        foreach ($this->forms as $lang => $f) {
            $forms[$lang] = $f->toArray();
        }

        return [
            $this->attribute => $forms
        ];
    }

}
