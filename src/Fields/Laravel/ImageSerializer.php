<?php

namespace Owlcoder\Forms\Fields\Laravel;

use Owlcoder\Cms\Models\Image;
use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Forms\Fields\Field;

class ImageSerializer extends Field
{
    public $multiple = false;
    public $foreignClass;
    public $foreignField;

    public function apply()
    {
        if ( ! $this->multiple) {
            DataHelper::set($this->instance, $this->attribute, $this->value['id']);
        }
    }

    public function fetchData()
    {
        $imageIds = [];

        foreach (DataHelper::get($this->instance, $this->attribute) as $one) {
            $imageIds[] = $one->{$this->foreignField};
        }

        $this->value = Image::whereIn('id', $imageIds)->get()->toArray();
    }

    public function afterSave()
    {
        if ($this->multiple) {
            foreach ($this->value as $one) {
            }
        }
    }
}
