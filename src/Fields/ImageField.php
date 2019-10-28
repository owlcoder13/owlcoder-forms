<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class ImageField extends Field
{
    public $template = 'forms.image-field';

    /** @var  UploadedFile */
    public $file;

    /** @var string */
    public $uri;

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

//            if ( ! is_dir($this->directory)) {
//                mkdir($this->directory, 777, true);
//            }

            $this->file->move($this->directory, $this->file->getClientOriginalName());
        }
    }
}
