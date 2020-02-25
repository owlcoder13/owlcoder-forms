<?php

namespace Owlcoder\Forms;

use Owlcoder\Common\EventTrait;
use Owlcoder\Common\Helpers\StringHelper;
use Owlcoder\Common\Helpers\ViewHelper;
use Owlcoder\Forms\Connectors\ArrayConnector;
use Owlcoder\Forms\Events\FormSetFieldValueEvent;
use Owlcoder\Forms\Events\FormSetInstanceValueEvent;
use Owlcoder\Forms\Fields\Field;
use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Forms\Traits\FormErrorsTrait;

class Form implements IFormEvent
{
    use EventTrait;
    use FormErrorsTrait;

    public $id;

    /** @var Field[] */
    public $fields = [];

    public $instance = null;
    public $namePrefix = '';
    public $parentForm = null;

    public $data;
    public $files;
    public $config;

    public static $defaultConnector = ArrayConnector::class;

    /**
     * @var array
     */
    public $rules = [];

    public static $formCounter = 0;

    public function __construct($config = [], &$instance = null, &$parentForm = null)
    {
        $this->id = $config['id'] ?? 'form_' . self::$formCounter++;

        $this->instance = &$instance ?? [];
        $this->namePrefix = $config['namePrefix'] ?? '';
        $this->parentForm = $parentForm;
        $this->config = $config;
        $this->rules = $config['rules'] ?? [];

        $this->registerEventsFromConfig();

        foreach ($config as $key => $handler) {
            if (StringHelper::startsWith($key, 'on')) {
                $eventName = substr($key, 2);
                $this->on($eventName, $handler);
            }
        }

        if (isset($config['beforeSave']) && is_callable($config['beforeSave'])) {
            $this->on('beforeSave', $config['beforeSave']);
        }

        // link to instance
        $i = &$this->instance;

        // create form fields
        $config['fields'] = $config['fields'] ?? [];
        foreach (array_merge($this->getFields(), $config['fields']) as $key => $value) {

            $fieldConf = Field::normalizeFormConfig($key, $value);
            $fieldClass = $fieldConf['class'];

            $namePrefix = $this->namePrefix ? $this->namePrefix : '';
            $fieldConf['namePrefix'] = $namePrefix;
            $fieldConf['idPrefix'] = $config['idPrefix'] ?? '';

            $field = new $fieldClass($fieldConf, $i, $this);
            $field->init();
            $this->fields[$field->attribute] = $field;
        }
    }

    public function render()
    {

        $out = [];
        $out[] = "<div id='{$this->id}'>";

        foreach ($this->fields as $field) {
            $out[] = "<div class='form-group'>" . $field->render() . "</div>";
        }

        $out[] = "</div>";

        return join("\n", $out);
    }

    public function registerEventsFromConfig()
    {
        if (isset($this->config['events'])) {
            foreach ($this->config['events'] as $key => $eventFunc) {
                if (is_array($eventFunc)) {
                    foreach ($eventFunc as $oneEventFunc) {
                        $this->addEventListener($key, $oneEventFunc);
                    }
                }
                $this->addEventListener($key, $eventFunc);
            }
        }
    }

    public function on($eventName, $callable)
    {
        $this->addEventListener($eventName, $callable);
    }

    public function load($data = [], $files = [])
    {
        if ( ! (is_array($data) && count($data) != 0) && ! (is_array($files) && count($files) != 0)) {
            return false;
        }

        $this->data = $data;
        $this->files = $files;

        foreach ($this->fields as $one) {
            $one->load($this->data, $this->files);
        }

        return true;
    }

    public function validate()
    {
        $this->triggerEvent(self::BEFORE_VALIDATE, $this);

        $valid = true;

        foreach ($this->fields as $field) {
            $valid = (boolean) $field->validate() & $valid;
            if ( ! $valid) {
                $this->addError($field->attribute, $field->errors);
            }
        }

        $this->triggerEvent(Form::AFTER_VALIDATE, $this);

        return $valid;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        if ( ! $this->validate()) {
            return false;
        }

        /**
         * Apply change in form to instance
         */
        foreach ($this->fields as $field) {
            $field->apply();
        }

        /**
         * Trigger before save method on each field
         */
        foreach ($this->fields as $field) {
            $field->beforeSave();
        }

        $this->triggerEvent(Form::BEFORE_SAVE, $this);

        $this->saveInstance();

        foreach ($this->fields as $field) {
            $field->afterSave();
        }

        $this->triggerEvent(Form::AFTER_SAVE, $this);

        return true;
    }

    /**
     * @throws \Exception
     */
    public function saveInstance()
    {
        if (is_object($this->instance) &&
            method_exists($this->instance, 'save')) {

            return $this->instance->save();

        }

        return true;
    }

    public static function removeScriptTag($content)
    {
        $content = (string) $content;

        if (StringHelper::startsWith($content, '<script')) {

            $end = mb_strpos($content, '>');
            $lastTagPos = mb_strrpos($content, '</script>');

            if ($end !== false && $lastTagPos !== false) {
                $content = mb_substr($content, $end + 1, $lastTagPos - strlen('</script>'));
            }

        }

        return $content;
    }

    public function js()
    {
        $render = ViewHelper::Render(__DIR__ . '/../resources/views/form-js.php', ['form' => $this]);
        return static::removeScriptTag($render);
    }

    public function toArray()
    {
        $out = [];

        foreach ($this->fields as $field) {
            $out = array_merge($out, $field->toArray());
        }

        return $out;
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * Default operation to get value from instance
     * Fields can get data through the form or have own method to get value from instance
     * If field have own method to get instance attribute value it is SPECIAL FIELD
     * Can be redefined in field classes.
     *
     */
    public function instanceGetValue($instance, $attribute, Field $field)
    {
        $event = new FormSetFieldValueEvent();
        $event->attribute = $attribute;
        $event->instance = $instance;

        $ref = &$event;

        $this->triggerEvent(self::FIELD_INSTANCE_GET_VALUE, $ref);

        if ( ! $event->prevented) {
            return DataHelper::get($instance, $attribute);
        }

        return $event->value;
    }

    /**
     *  Set field for instance for default fields that can not redefine load
     */
    public function instanceSetValue($data, $files, &$field)
    {
        $event = new FormSetInstanceValueEvent();
        $event->attribute = $field->attribute;
        $event->instance = &$this->instance; // instance by reference
        $event->data = $data;
        $event->files = $files;
        $event->field = $field;

        $ref = &$event;

        $this->triggerEvent(self::FIELD_INSTANCE_SET_VALUE, $ref);

        if ( ! $event->prevented) {
            $value = DataHelper::get($event->data, $event->attribute);
            DataHelper::set($field->instance, $field->attribute, $value);
        }
    }

    /**
     * Возвращает поля текущей формы
     * @return array
     */
    public function getFields()
    {
        return [];
    }

}
