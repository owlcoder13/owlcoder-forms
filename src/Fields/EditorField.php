<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;

class EditorField extends TextAreaField
{
    public $template = 'forms/editor-field';

    public function js()
    {
        return Form::removeScriptTag(view('forms.editor-field-js'));
    }
}
