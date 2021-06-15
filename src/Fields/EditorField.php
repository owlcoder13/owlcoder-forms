<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Forms\Form;
use Owlcoder\Common\Helpers\ViewHelper;

class EditorField extends TextAreaField
{
    public $template = 'editor-field';
    public $editorConfig = [];

    public function js()
    {
        $viewPath = __DIR__ . '/../../resources/views/editor-field-js.php';
        return Form::removeScriptTag(ViewHelper::Render($viewPath, ['field' => $this]));
    }
}
