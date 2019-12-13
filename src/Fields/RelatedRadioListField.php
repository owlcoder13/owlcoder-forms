<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\ViewHelper;

class RelatedRadioListField extends Field
{
    public function buildContext()
    {
        return array_merge(parent::buildContext(), [
            'options' => $this->config['options'](),
        ]);
    }

    public function renderInput()
    {
        return ViewHelper::Render(__DIR__ . '/../../resources/views/related-radio-list.php', $this->buildContext());
    }
}
