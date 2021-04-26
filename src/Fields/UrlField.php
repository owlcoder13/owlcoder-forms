<?php

namespace Owlcoder\Forms\Fields;

class UrlField extends Field
{
    public function validate()
    {
        $value = $this->getValue();

        if ( ! empty($value) && $value[0] != '/') {
            $this->addError("Url must starts with '/'");
        }

        return count($this->errors) === 0;
    }
}
