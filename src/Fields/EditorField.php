<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;

class EditorField extends TextAreaField
{
    public $template = 'editor-field';
    public $editorConfig = [];

    public function js()
    {
        return Form::removeScriptTag(view('forms::editor-field-js', ['field' => $this]));
    }
}
