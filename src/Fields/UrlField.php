<?php

namespace Owlcoder\Forms\Fields;

class UrlField extends Field
{
    public function validate()
    {
        $value = $this->getValue();

        if (!empty($value) && $value[0] != '/') {
            throw new \Owlcoder\Forms\ValidationException("Url must starts with '/'");
        }
    }
}
