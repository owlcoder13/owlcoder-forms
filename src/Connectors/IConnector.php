<?php

namespace Owl\OwlForms\Connectors;

interface IConnector
{
    public function fieldGetAttribute($form, $field, $instance, $attribute);

    public function fieldSetAttribute($form, $field, $instance, $attribute, $value);

    public function fieldSave($form, $obj);

    public function beforeSave($form, $field, $instance, $attribute)
    {

    }

    public function afterSave($form, $field, $instance, $attribute)
    {

    }
}
