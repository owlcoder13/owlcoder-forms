<?php

namespace Owlcoder\Forms\Fields;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Owlcoder\Common\Helpers\ArrayHelper;
use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Common\Helpers\ViewHelper;

/**
 * Attribute for this model must be middle model
 * Class ManyToManyCheckboxListField
 * @package Owlcoder\Forms\Fields
 */
class ManyToManyCheckboxListField extends Field
{
    public $options;
    public $oldValue;

    public $remoteModel;
    public $middleModel;
    public $toLocalKey;
    public $toRemoteKey;

    public $middleAttribute;

    public $remoteLabel = 'name';
    public $remoteId = 'id';

    public $canApply = false;

    public function __construct($config, $instance, $form = null)
    {
        parent::__construct($config, $instance, $form);

        $this->name .= '[]';

        if (isset($this->config['options'])) {
            if (is_callable($config['options'])) {
                $this->options = $config['options']($this);
            } else {
                $this->options = $config['options'];
            }
        } else {
            $remoteModelClassName = $this->remoteModel;
            $remoteModelClassName = $remoteModelClassName::all()->pluck($this->remoteLabel, $this->remoteId);
            $this->options = $remoteModelClassName->toArray();
        }

        $this->remoteModel = $config['remoteModel'] ?? null;
        $this->middleModel = $config['middleModel'] ?? null;
        $this->toLocalKey = $config['toLocalKey'] ?? null;
        $this->toRemoteKey = $config['toRemoteKey'] ?? null;

        return $this;
    }

    public function fetchData()
    {
        $models = data_get($this->instance, $this->attribute);
        $remoteKey = $this->toRemoteKey;
        $ids = $models->pluck($remoteKey)->toArray();

        $this->value = $ids;
        $this->oldValue = $this->instance->{$this->attribute};
    }

    public function renderInput()
    {
        return ViewHelper::Render(__DIR__ . '/../../resources/views/checkbox-list.php', $this->buildContext());
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
        $key = $this->getFkToLocal();

        $not_to_delete = [];
        $ids = $this->getValueFromData($this->data, $this->files);
        if (empty($ids)) {
            $ids = [];
        }
        $middleModelClass = $this->middleModel;

        foreach ($ids as $id) {

            $attributes = [
                $this->toRemoteKey => $id,
                $this->toLocalKey => $this->instance->id,
            ];

            $middleInstance = $middleModelClass::where($attributes)->first();
            if ($middleInstance == null) {
                $middleInstance = new $middleModelClass($attributes);
                $middleInstance->save();
            }

            $not_to_delete[] = $middleInstance->id;
        }

        foreach ($this->oldValue as $one) {
            if ( ! in_array($one->id, $not_to_delete)) {
                $one->delete();
            }
        }
    }
}
