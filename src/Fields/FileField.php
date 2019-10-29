<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class FileField extends Field
{
    /** @var  UploadedFile */
    public $file;

    /** @var string */
    public $uri;

    public function renderInput()
    {
        $attributes = $this->buildInputAttributes([
            'value' => $this->escapeAttrValue($this->getValue()),
        ]);

        return "<input {$attributes} type='file' value='{$this->value}'/>";
    }

    /** @var string */
    public $directory;

    public function __construct(array $config, $instance, Form $form)
    {
        parent::__construct($config, $instance, $form);

        $this->uri = data_get($this->config, 'directory', '/uploads/');

        if (substr($this->uri, -1) != '/') {
            $this->uri .= '/';
        }

        $this->directory = base_path('public' . data_get($this->config, 'directory', $this->uri));
    }

    /**
     * @param $data
     * @param FileBag $files
     */
    public function load($data, $files)
    {
        $this->file = $this->getFileByKey($files, $this->attribute, null);
        if ($this->file) {
            data_set($this->instance, $this->attribute, $this->uri . $this->file->getClientOriginalName());
        }
    }

    public function afterSave()
    {
        // save files
        parent::afterSave();

        if ($this->file) {
            $fileName = $this->file->getClientOriginalName();
            $this->file->move($this->directory, $this->file->getClientOriginalName());
        }
    }
}
