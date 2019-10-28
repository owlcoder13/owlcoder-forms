<?php

namespace Owl\OwlForms\Fields;

class UrlField extends Field
{
    public function validate()
    {
        $value = $this->getValue();
        if ($value[0] != '/') {
            throw new \Owl\OwlForms\ValidationException("Url must starts with '/'");
        }
    }
}
