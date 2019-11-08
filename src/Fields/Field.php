<?php

namespace Owl\OwlForms\Fields;

use Owl\Common\EventTrait;
use Owl\OwlForms\Validation\FieldValidation;
use Owl\OwlForms\Form;
use Owl\OwlForms\IFieldEvent;

use Owl\Common\Helpers\DataHelper;

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

        if (is_array($this->value)) {
            throw new \Exception('Value of field can not be array: ' . print_r($this->value, true));
        }
    }

    public function getValue()
    {
        if (isset($this->config['getValue'])) {
            return $this->config['getValue']($this);
        } else {
            return $this->form->instanceGetValue($this->instance, $this->attribute, $this);
        }
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
            $this->setValue($this->getValueFromData($data, $files));
        }

        $this->file = $this->getFileByKey($files, $this->attribute, null);
    }

    public function dataHasValue($data, $file)
    {
        return isset($data[$this->attribute]);
    }

    public function getValueFromData($data, $file)
    {
        if (isset($this->config['getValueFromData'])) {
            return $this->config['getValueFromData']($this, $data, $file);
        }

        return DataHelper::get($data, $this->attribute);
    }

    public function setValue($value)
    {
        if ($this->isChanged($value)) {
            if (isset($this->config['setValue'])) {
                $this->config['setValue']($this, $value);
            } else {
                $this->form->instanceSetValue($this->data, $this->files, $this);
            }
        }
    }

    public function js()
    {
        return '';
    }

    public function beforeSave()
    {

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
