<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;

class EditorField extends TextAreaField
{
    public $template = 'forms/editor-field';

    public function js()
    {
        return Form::removeScriptTag(view('forms.editor-field-js'));
    }
}
