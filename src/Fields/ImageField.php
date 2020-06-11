<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Cms\Helpers\Html;
use Owlcoder\Common\Helpers\File;
use Owlcoder\Forms\Form;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class ImageField extends FileField
{
    /** @var  UploadedFile */
    public $file;

    /** @var string */
    public $baseUri;

    /** @var string */
    public $basePath;

    public $fileName;

    /**
     * @var bool
     */
    public $unique = true;

    /**
     * @param $data
     * @param FileBag $files
     */
    public function load($data, $files)
    {
        $this->file = $this->getFileByKey($files, $this->attribute, null);

        if ($this->file) {
            $fileName = File::uniqueFileName($this->basePath, $this->file->getClientOriginalName());
            $this->fileName = $fileName;
            $this->value = File::removeDoubleSlash($this->baseUri . '/' . $fileName);
        }

    }

    public function afterSave()
    {
        if ($this->file) {
            $this->file->move($this->basePath, $this->fileName);
        }
    }

    public function render()
    {
        $render = parent::render();

        if ($this->value) {
            $render .= \Owlcoder\Common\Helpers\Html::tag('img', '', [
                'src' => $this->value,
                'style' => 'max-width: 200px; max-height: 200px;']);
        }

        return $render;

    }
}
