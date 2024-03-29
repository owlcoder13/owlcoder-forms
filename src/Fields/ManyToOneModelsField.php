<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\DataHelper;

/**
 * Required config params:
 *  fkField => field for setting relation key
 *  modelClassName => create new instance of related model
 * Class ManyToOneModelsField
 * @package Owlcoder\Forms\Fields
 */
class ManyToOneModelsField extends ArrayField
{
    /**
     * @var string this fields adds items of such modelClassName objects
     */
    public $modelClassName;
    public $fkField = null;
    public $callbackBeforeNewModelsSaving;

    public $formsToDelete = [];

    public function createEmptyInstance()
    {
        return new $this->modelClassName;
    }

    /**
     * Translate value from instance to array
     * @param $value
     * @return array
     */
    protected function fetchedToArray($value)
    {
        if (is_array($value)) {
            return $value;
        } else if (is_object($value) && is_iterable($value)) {
            $value = iterator_to_array($value);
        } else {
            $value = [];
        }

        return $value;
    }

    public function fetchData()
    {
        if (isset($this->config['fetchData']) && is_callable($this->config['fetchData'])) {
            return $this->config['fetchData']($this);
        }

        $attr = $this->attribute;
        $value = $this->instance->$attr;


        $this->value = $this->fetchedToArray($value);
    }

    public function toArray()
    {
        return [$this->attribute => $this->value];
    }

    public function apply()
    {
        // nothing here
    }

    public function afterSave()
    {
        $fkField = $this->fkField;

        foreach ($this->forms as $key => $form) {

            // set fk fields to related model if specified
            if ($fkField) {
                $form->instance[$fkField] = $this->instance->id;
            }

            // run callback if specified
            if ($this->callbackBeforeNewModelsSaving) {
                $callback = $this->callbackBeforeNewModelsSaving;
                $callback($form);
            }

            if ($this->sortField) {
                $form->instance[$this->sortField] = $key;
            }

            $form->save();
        }

        foreach ($this->formsToDelete as $one) {
            $one->instance->delete();
        }
    }

    public function getDataAndFiles($data, $files)
    {
        $localData = DataHelper::get($data, $this->attribute);
        $localFiles = DataHelper::get($files, $this->attribute);

        // fill data if only files exist
        if ($localData == null && $localFiles != null) {
            $localData = [];
            foreach ($localFiles as $key => $value) {
                $localData[$key] = [];
            }
        }

        return [$localData, $localFiles];
    }

    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;

        list($localData, $localFiles) = $this->getDataAndFiles($data, $files);

        $forms = $this->forms;
        $newForms = [];

        $this->value = $localData;

        if (is_array($localData)) {

            foreach ($localData as $k => $formData) {

                if (strpos($k, '__index__') !== false) {
                    continue;
                }

                $form = null;

                if (!empty($formData['id'])) {
                    $form = $this->searchForm($formData['id'], $forms);
                }

                if ($form == null) {
                    $form = $this->createForm($this->createEmptyInstance(), $k);
                }

                $newForms[] = $form;

                $form->load($formData, $localFiles[$k] ?? null);
            }

            $this->forms = $newForms;
            $this->formsToDelete = $forms;

            return true;
        }

        return false;
    }

    public function searchForm($id, &$forms)
    {
        foreach ($forms as $key => $form) {
            if ($form->instance['id'] == $id) {
                unset($forms[$key]);
                return $form;
            }
        }
        return null;
    }
}
