<?php

namespace Owlcoder\Forms\Fields;

use Illuminate\Support\Collection;
use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Forms\Form;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class ManyToOneModelsField extends ArrayField
{

    public $modelClassName;
    public $fkField;
    public $formsToDelete = [];

    public function createEmptyInstance()
    {
        return new $this->modelClassName;
    }

    public function fetchData()
    {
        $attr = $this->attribute;
        $value = $this->instance->$attr;

        if (is_iterable($value)) {
            $value = iterator_to_array($value);
        } else {
            $value = [];
        }

        $this->value = $value;
    }

    public function toArray()
    {
        return [$this->attribute => $this->value];
    }

    public function apply()
    {
        //        parent::apply();
    }

    public function afterSave()
    {
        // todo: delete old records
        $oldData = new Collection($this->value);
        $fkField = $this->fkField;

        foreach ($this->forms as $form) {
            $form->instance[$fkField] = $this->instance->id;
            $form->save();
        }

        foreach ($this->formsToDelete as $one) {
            $one->instance->delete();
        }

        parent::afterSave();
    }

    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;

        $localData = DataHelper::get($data, $this->attribute);
        $localFiles = DataHelper::get($files, $this->attribute);

        $forms = $this->forms;
        $newForms = [];

        $this->value = $localData;

        if (is_array($localData)) {

            foreach ($localData as $k => $formData) {

                if (strpos($k, '__index__') !== false) {
                    continue;
                }

                $form = null;

                if ( ! empty($formData['id'])) {
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
