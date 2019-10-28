<?php

namespace Owl\OwlForms\Connectors;

class ArrayConnector implements IConnector
{
    public function setAttribute($form, $field, $instance, $attribute, $value)
    {
        $instance[$attribute] = $attribute;
    }

    public function getAttribute($form, $field, $instance, $attribute)
    {
        return $instance[$attribute];
    }

    public function save($form, $obj)
    {

    }
}
