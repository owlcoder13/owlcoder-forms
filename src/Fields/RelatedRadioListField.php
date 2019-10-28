<?php

namespace Owl\OwlForms\Fields;

class RelatedRadioListField extends Field
{
    public $template = 'forms.related-radio-list';

    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'options' => $this->config['options'](),
        ]);
    }
}
