<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\EventTrait;
use Owlcoder\Forms\Validation\FieldValidation;
use Owlcoder\Forms\Form;
use Owlcoder\Forms\IFieldEvent;

use Owlcoder\Common\Helpers\DataHelper;

class Field implements IFieldEvent
{
    use EventTrait;
    use FieldValidation;

    public $instance;
    public $attribute;
    public $idPrefix = '';

    public $data;
    public $files;

    public $form;

    public $template = 'forms.text-field';
    public $label;
    public $value;
    public $name;

    public $config = [];

    public $namePrefix = '';
    public $id;

    public $inputAttributes = ['class' => 'form-control'];

    public function __construct(array $config, &$instance, Form &$form)
    {
        $this->instance = &$instance;
        $this->form = $form;
        $this->config = $config;
        $this->attribute = $config['attribute'];
        $this->label = $config['label'] ?? $config['attribute'] ?? '';
        $this->rules = $config['rules'] ?? [];

        $namePrefix = ! empty($config['namePrefix']) ? $config['namePrefix'] . '.' : '';
        $tmpName = $namePrefix . ($config['name'] ?? $this->attribute);

        $this->idPrefix = $config['idPrefix'] ?? '';
        $this->name = static::generateFromDotName($tmpName);
        $this->id = $this->idPrefix . ($config['id'] ?? str_replace('.', '_', $tmpName));

        $this->inputAttributes = ($this->config['inputAttributes'] ?? []) + $this->inputAttributes;

        foreach ($config as $key => $val) {
            DataHelper::set($this, $key, $val);
        }

        if ($this->instance) {
            $this->value = $this->getValue();
        }
    }

    /**
     * Fetch initial data from instance
     */
    public function fetchData()
    {
        $this->value = DataHelper::get($this->instance, $this->attribute);
    }

    /**
     * fetch instance value and etc...
     */
    public function init()
    {
        $this->fetchData();

//        $field = $this;
//
//        $this->form->on('beforeValidate', function ($form) use ($field) {
//            $field->setValue($field->value);
//        });
    }

    public function getValue()
    {
        return $this->value;
    }

    public function escapeAttrValue($value)
    {
        return mb_ereg_replace("'", "\\'", $value);
    }

    /**
     * @return []
     */
    public function buildInputAttributes($additionalAttributes = [])
    {
        $out = [];

        $allAttributes = array_merge($this->inputAttributes, $additionalAttributes);

        $allAttributes['name'] = $this->name;
        $allAttributes['id'] = $this->id;

        foreach ($allAttributes as $key => $value) {
            $value = $this->escapeAttrValue($value);
            $out[] = "$key='$value'";
        }

        return join(' ', $out);
    }

    public function buildContext()
    {
        return [
            'field' => $this,
            'inputAttributes' => $this->buildInputAttributes(),
        ];
    }

    public static function generateFromDotName($dotName)
    {
        $parts = explode('.', $dotName);

        $parts = array_filter($parts, function ($val) {
            return $val !== '';
        });

        $newName = array_shift($parts);

        while (($pn = array_shift($parts)) !== null) {
            $newName .= "[$pn]";
        }

        return $newName;
    }

    public static function normalizeFormConfig($key, $value)
    {
        $out = [];

        if (is_string($value)) { // assoc
            $out['attribute'] = $value;
        } else if (is_array($value)) {

            if (is_string($key)) {
                $attribute = $key;
            } else {
                $attribute = $value['attribute'] ?? null;
            }

            $out['attribute'] = $attribute;

            foreach ($value as $k => $v) {
                DataHelper::set($out, $k, $v);
            }
        }

        $out['class'] = $out['class'] ?? Field::class;

        return $out;
    }

    public function isChanged($value)
    {
        if (isset($this->config['isChanged'])) {
            return $this->config['isChanged']($this, $this->data, $this->files);
        }

        return $this->getValue() != $value;
    }

    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;

        if ($this->dataHasValue($data, $files)) {
            $this->value = $this->getValueFromData($data, $files);
        }

        $this->file = $this->getFileByKey($files, $this->attribute, null);
    }

    public function dataHasValue($data, $file)
    {
        return array_key_exists($this->attribute, $data);
    }

    public function getValueFromData($data, $file)
    {
        if (isset($this->config['getValueFromData'])) {
            return $this->config['getValueFromData']($this, $data, $file);
        }

        return DataHelper::get($data, $this->attribute);
    }

    public function js()
    {
        return '';
    }

    public function apply()
    {
        DataHelper::set($this->instance, $this->attribute, $this->getValue());
    }

    public function beforeSave()
    {
        $this->apply();
    }

    public function afterSave()
    {

    }

    public function toArray()
    {
        return [$this->attribute => $this->getValue()];
    }

    public function renderInput()
    {
        $attributes = $this->buildInputAttributes([
            'value' => $this->escapeAttrValue($this->getValue()),
        ]);

        return "<input {$attributes} type='text' value='{$this->value}'/>";
    }

    public function renderLabel()
    {
        return "<label for='{$this->id}'>{$this->label}</label><br>";
    }

    public function renderErrors()
    {
        $out = '';

        if (count($this->errors) > 0) {
            $errors = join(', ', $this->errors);
            $out .= "<div class='error'>{$errors}</div>";
        }

        return $out;
    }

    public function render()
    {
        $out = $this->renderLabel();
        $out .= $this->renderInput();
        $out .= $this->renderErrors();

        return $out;
    }

    public function getFileByKey($files, $key, $default = null)
    {
        $file = DataHelper::get($files, $key, $default);
        return empty($file) ? $default : $file;
    }
}
