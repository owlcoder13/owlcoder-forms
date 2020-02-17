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

    public function createEmptyInstance()
    {
        return new $this->modelClassName;
    }

    public function fetchData()
    {
        $attr = $this->attribute;
        $value = $this->instance->$attr;
        $this->value = iterator_to_array($value);
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
        $oldData = new Collection($this->value);
        $fkField = $this->fkField;
        $notDeleteIds = [];

        foreach ($this->forms as $form) {
            $form->instance[$fkField] = $this->instance->id;
            $form->save();
            $notDeleteIds[] = DataHelper::get($form->instance, 'id');
        }

        foreach ($oldData as $one) {
            if ( ! in_array($one->id, $notDeleteIds)) {
                $one->delete();
            }
        }

        parent::afterSave();
    }

    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;

        $localData = DataHelper::get($data, $this->attribute);
        $localFiles = DataHelper::get($files, $this->attribute);

        if (is_array($localData)) {
            foreach ($localData as $k => $formData) {

                $form = null;

                if ( ! empty($formData['id'])) {
                    $form = $this->searchForm($formData['id']);
                }

                if ($form == null) {
                    $form = $this->createForm($this->createEmptyInstance(), $k);
                }

                $form->load($formData, $localFiles[$k] ?? null);
                $this->forms[] = $form;
            }
            return true;
        }

        return false;
    }

    public function searchForm($id)
    {
        foreach ($this->forms as $form) {
            if ($form->instance['id'] == $id) {
                return $form;
            }
        }
        return null;
    }
}
