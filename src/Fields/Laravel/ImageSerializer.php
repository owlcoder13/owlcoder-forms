<?php

namespace Owlcoder\Forms\Fields\Laravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Owlcoder\Cms\Models\Image;
use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Forms\Fields\Field;

class ImageSerializer extends Field
{
    public $multiple = false;
    public $foreignClass;
    public $foreignField;

    public $oldData = [];

    public function apply()
    {
//        $this->oldData = $this->value;

        if ( ! $this->multiple) {
            //            DataHelper::set($this->instance, $this->attribute, $this->value['id']);
            parent::apply();
        }
    }

    public function fetchData()
    {
        $imageIds = [];
        $this->oldData = DataHelper::get($this->instance, $this->attribute);

        foreach ($this->oldData as $one) {
            $imageIds[] = $one->image_id;
        }

        $this->value = Image::whereIn('id', $imageIds)->get()->toArray();
    }

    public function afterSave()
    {
        if ($this->multiple) {

            $fkField = $this->foreignField;
            $className = $this->foreignClass;
            $saved = [];

//            /** @var Collection $oldRecords */
//            $ids = Arr::pluck($this->oldData, 'id');

            foreach ($this->value as $one) {
                $imageId = $one['id'];

                $found = $this->oldData->search(function ($item) use ($fkField, $imageId) {
                    return $item->$fkField == $imageId;
                });

                if ($found === false) {
                    $model = new $className;
                    $model->image_id = $imageId;
                    $model->{$this->foreignField} = $this->instance->id;
                    $model->save();

                    $imageId = $model->id;
                } else {
                    $model = $this->oldData[$found];

                }

                $saved[] = $model['id'];
            }

            foreach ($this->oldData as $one) {
                if ( ! in_array($one['id'], $saved)) {
                    $one->delete();
                }
            }
        }
    }
}
