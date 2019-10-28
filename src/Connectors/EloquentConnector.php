<?php

namespace Owl\OwlForms\Connectors;

class EloquentConnector implements IConnector
{
    public function setAttribute($form, $field, $instance, $attribute, $value)
    {
        $instance->$attribute = $attribute;
    }

    public function getAttribute($form, $field, $instance, $attribute)
    {
        return $instance->$attribute = $attribute;
    }

    public function save($form, $obj)
    {

    }
}
