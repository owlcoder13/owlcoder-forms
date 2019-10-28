<?php

namespace Owl\OwlForms\Fields;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class ManyToManyCheckboxListField extends Field
{
    public $options;
    public $relatedModelField;

    public $template = 'forms.checkbox-list';

    public function __construct($config, $instance, $form = null)
    {
        parent::__construct($config, $instance, $form);

        $this->name .= '[]';
        $this->value = $this->getInitialValue();
        $this->options = $config['options']($instance);
        $this->relatedModelField = $config['relatedModelField'];

        return $this;
    }

    public function getInitialValue()
    {
        $models = data_get($this->instance, $this->attribute);
        $fkToLocal = $this->relatedModelField;

        if ($fkToLocal == null) {
            throw new \Exception('Can not get fk for ' . $this->attribute);
        }

        return iterator_to_array($models->pluck($fkToLocal));
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

    public function getFkToLocal()
    {
        return $this->getRelation()->getForeignKeyName();
    }

    public function save()
    {

    }

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'options' => $this->options,
        ]);
    }

    public function load($data, $files)
    {
        $this->data = $data;
        $this->files = $files;
    }

    public function afterSave()
    {
        $models = data_get($this->instance, $this->attribute);

        $key = $this->getFkToLocal();

        $not_to_delete = [];
        $relatedField = $this->relatedModelField;
        $data = $this->getValueFromData($this->data, $this->files);

        foreach ($data as $id) {
            $tmp = Arr::first($models, function ($item) use ($id, $key) {
                return $item->$key == $id;
            });

            if ($tmp == null) {
                $relatedClass = $this->getRelatedClass();
                $className = new $relatedClass;

                $tmp = new $className;
                $tmp->$key = $this->instance->id;
                $tmp->$relatedField = $id;

                $tmp->save();
            }

            $not_to_delete[] = $tmp->id;
        }

        foreach ($models as $one) {
            if ( ! in_array($one->id, $not_to_delete)) {
                $one->delete();
            }
        }
    }
}
