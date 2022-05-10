<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\EventTrait;
use Owlcoder\Common\Helpers\Html;
use Owlcoder\Forms\Validation\FieldValidation;
use Owlcoder\Forms\Form;
use Owlcoder\Forms\IFieldEvent;

use Owlcoder\Common\Helpers\DataHelper;
use stringEncode\Exception;

class Field implements IFieldEvent
{
    use EventTrait;
    use FieldValidation;

    /** @var bool set data value to null if empty loading data */
    public $nullIfEmpty = false;

    /** @var object|array store source data */
    public $instance;

    /** @var string */
    public $attribute;

    public $idPrefix = '';
    public $type = 'text';

    /** @var array from load method */
    public $data;
    /** @var array from load method */
    public $files;

    /** @var Form the parent form of current field */
    public $form;

    public $template = 'forms.text-field';
    public $label;

    /** @var mixed current value buffer */
    public $value;

    /** @var string name of current field */
    public $name;

    public $config = [];

    /** @var string if you using nested forms */
    public $namePrefix = '';

    public $id;

    /** @var bool May field write data back to source or not */
    public $canApply = true;

    /** @var bool Base function for fetch attribute */
    public $canFetch = true;

    public $tip;


    public function __construct(array $config, &$instance, Form &$form)
    {
        $this->instance = &$instance;
        $this->form = $form;
        $this->config = $config;
        $this->attribute = $config['attribute'] ?? null;
        $this->label = $config['label'] ?? $config['attribute'] ?? '';
        $this->rules = $config['rules'] ?? [];

        foreach ($config['events'] ?? [] as $event => $callable) {
            $this->addEventListener($event, $callable);
        }

        $namePrefix = !empty($config['namePrefix']) ? $config['namePrefix'] . '.' : '';
        $tmpName = $namePrefix . ($config['name'] ?? $this->attribute);

        $this->idPrefix = $config['idPrefix'] ?? '';
        $this->name = static::generateFromDotName($tmpName);
        $this->id = $this->idPrefix . ($config['id'] ?? str_replace('.', '_', $tmpName));

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
        if ($this->canFetch) {
            if (isset($this->config['fetchData']) && is_callable($this->config['fetchData'])) {
                $this->config['fetchData']($this);
            } else {
                $this->form->fetchAttributeData($this);
            }
        }

        $this->triggerEvent(static::EVENT_FETCH_VALUE, $this);
    }

    /**
     * fetch instance value and etc...
     */
    public function init()
    {
        $this->fetchData();
    }

    /**
     * Получение текущего значения формы
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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
        return is_array($data) && array_key_exists($this->attribute, $data);
    }

    /**
     * Try to fetch data from instance
     */
    public function getValueFromData($data, $file)
    {
        if (isset($this->config['getValueFromData'])) {
            return $this->config['getValueFromData']($this, $data, $file);
        }

        return DataHelper::get($data, $this->attribute);
    }

    /**
     * Represent js for current field instance
     */
    public function js()
    {
        return '';
    }

    /**
     * Write data from the form field to the instance field
     */
    public function apply()
    {
        $this->triggerEvent(self::EVENT_BEFORE_APPLY, $this);

        if ($this->canApply) {
            if (isset($this->config['apply'])) {

                if ($this->config['apply'] === false) {
                    return;
                }

                if (is_callable($this->config['apply'])) {
                    $this->config['apply']($this);
                }
            } else {
                $this->form->applyAttributeData($this);
            }
        }
    }

    /**
     * Execute before instance saving
     */
    public function beforeSave()
    {
        if (isset($this->config['beforeSave'])) {
            $this->config['beforeSave']($this);
        }

        $this->triggerEvent(self::EVENT_BEFORE_SET, $this);
    }

    /**
     * Execute before instance saving
     */
    public function afterSave()
    {
        if (isset($this->config['afterSave'])) {
            $this->config['afterSave']($this);
        }

        $this->triggerEvent(static::EVENT_AFTER_SAVE, $this);
    }

    /**
     * Get data array from the form
     */
    public function toArray()
    {
        return [$this->attribute => $this->getValue()];
    }

    public $inputAttributes = ['class' => 'form-control'];

    public function getInputAttributes()
    {
        $out = array_merge($this->inputAttributes, [
            'name' => $this->name,
            'id' => $this->id,
        ]);

        // merge attribute with callable function from confi
        if (isset($this->config['getInputAttributes'])) {
            $ia = $this->config['getInputAttributes'];
            if (is_array($ia)) {
                $out = array_merge($out, $ia);
            } else if ($ia instanceof \Closure) {
                $out = array_merge($out, $ia($this));
            }
        }

        return $out;
    }

    public function buildContext()
    {
        return [
            'field' => $this,
        ];
    }

    public function getFileByKey($files, $key, $default = null)
    {
        $file = DataHelper::get($files, $key, $default);
        return empty($file) ? $default : $file;
    }


    // ==================== render methods ====================

    public function renderInput()
    {
        $attributes = array_merge($this->getInputAttributes(), [
            'value' => $this->getValue(),
            'type' => $this->type,
        ]);

        return Html::tag('input', '', $attributes);
    }

    public function renderLabel()
    {
        return "<label for='{$this->id}'>{$this->label}</label>";
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

    public function renderTip()
    {
        if (!empty($this->tip)) {
            return Html::tag('div', $this->tip, [
                'class' => 'form-tip'
            ]);
        }
    }

    public function render()
    {
        $out = $this->renderLabel();
        $out .= $this->renderInput();
        $out .= $this->renderTip();
        $out .= $this->renderErrors();


        return $out;
    }
}
