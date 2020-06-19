<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\ViewHelper;
use Owlcoder\Forms\Form;
use Illuminate\Support\Arr;
use Owlcoder\Common\Helpers\DataHelper;

class ArrayField extends Field
{

    public $model;
    public $createInstance;
    public $nestedConfig;
    public $sortField = null;

//    /**
//     * Field can not apply itself. It calls child forms to apply data
//     * @var bool
//     */
//    public $canApply = false;

    public $onBeforeEachSave;

    /** @var Form[] */
    public $forms = [];

    public $template = 'array-field';

    public $hiddenForm = null;

    /**
     * enable Sortable plugin
     * @var bool
     */
    public $sort = false;

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

        if ( ! isset($config['nestedConfig'])) {
            throw new \Exception('Array field must have nestedConfig attribute');
        }

        $this->hiddenForm = $this->createHiddenForm();
    }

    public function fetchData()
    {
        $instances = DataHelper::get($this->instance, $this->attribute);

        if (empty($instances) || ! is_array($instances)) {
            $instances = [];
        }

        foreach ($instances as $key => $instance) {
            $form = $this->createForm($instance, $key);
            $this->forms[$key] = $form;
        }
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
        return ViewHelper::Render(__DIR__ . '/../../resources/views/array-field.php', [
            'field' => $this,
        ]);
    }

    /**
     * Create new instance from fields with null values
     * @return array
     */
    public function createEmptyInstance()
    {
        $out = [];

        foreach ($this->config['nestedConfig']['fields'] as $key => $field) {
            $attribute = $field['attribute'] ?? $key;
            $out[$attribute] = null;
        }

        return $out;
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

    public function apply()
    {
        return DataHelper::set($this->instance, $this->attribute, $this->toArray()[$this->attribute]);
    }

    /**
     * Reset our forms and create new ones
     *
     * @param $data
     * @param $files
     */
    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;
        $this->value = $data;

        $oldForms = $this->forms;
        $this->forms = [];

        $localData = DataHelper::get($data, $this->attribute);
        $localFiles = DataHelper::get($files, $this->attribute);

//        $handledKeys = [];

        if (is_array($localData)) {
            foreach ($localData as $k => $formData) {

                /**
                 * We don't want to save hidden form. Ignore it
                 */
                if ($k === '__index__') {
                    continue;
                }

                $newModel = $this->createEmptyInstance();

                if (isset($oldForms[$k])) {
                    $form = $oldForms[$k];
                    $form->load($formData, $localFiles[$k] ?? null);
                } else {
                    $form = $this->createForm($newModel, $k);
                    $form->load($formData, $localFiles[$k] ?? null);
                }

                $this->forms[] = $form;
            }
        }

        return false;
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
        return Form::removeScriptTag(
            ViewHelper::Render(__DIR__ . '/../../resources/views/array-field-js.php',
                ['field' => $this])
        );
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

    public function beforeSave()
    {
        foreach ($this->forms as $form) {
            $form->beforeSave();
        }
    }

    public function afterSave()
    {
        foreach ($this->forms as $form) {
            $form->afterSave();
        }
    }
}
