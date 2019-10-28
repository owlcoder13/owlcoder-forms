<?php

namespace Owl\OwlForms\Connectors;

interface IConnector
{
    public function getAttribute($form, $field, $instance, $attribute);

    public function setAttribute($form, $field, $instance, $attribute, $value);

    public function save($form, $obj);
}
