<?php

namespace Owlcoder\Forms\Fields\Yii;

use Owlcoder\Common\Helpers\File;
use Owlcoder\Forms\Fields\Field;
use Owlcoder\Common\Helpers\Html;
use yii\db\Exception;

class ImageField extends Field
{
    public $file;
    public $fileName;
    public $basePath;
    public $baseUri = '/uploads';

    public function fetchData()
    {
        parent::fetchData();
    }

    public function render()
    {
        $render = parent::render();

        if ($this->value) {
            $render .= Html::tag('img', '', ['src' => $this->value]);
        }

        return $render;
    }

    public function apply()
    {
        if ($this->file) {
            parent::apply();
        }
    }

    public function load($data, $files)
    {
        $file = $this->getFileByKey($files, $this->attribute, null);

        if (empty($file['error'])) {
            $this->file = $file;
        }

        if ($this->file) {

            $fileName = File::uniqueFileName($this->basePath, $this->file['name']);
            $this->fileName = $fileName;
            $this->value = File::removeDoubleSlash($this->baseUri . '/' . $fileName);
        }
    }

    public function renderInput()
    {
        $attributes = array_merge($this->getInputAttributes(), [
            'value' => $this->getValue(),
            'type' => 'file',
        ]);

        return Html::tag('input', '', $attributes);
    }

    public function afterSave()
    {
        if ($this->file) {
            if ( ! file_exists($this->basePath)) {
                mkdir($this->basePath, 0777, true);
            }

            $savePath = $this->basePath . DIRECTORY_SEPARATOR . $this->fileName;

            if (false === move_uploaded_file($this->file['tmp_name'], $savePath)) {
                throw new Exception('Can not save file to ' . $savePath);
            }
        }
    }
}