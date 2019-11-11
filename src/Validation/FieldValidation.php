<?php

namespace Owl\OwlForms\Validation;

use mysql_xdevapi\Exception;
use Owl\OwlForms\Form;

/**
 * Trait FieldValidation
 * @package Owl\OwlForms
 *
 * @property Form $form
 * @property $instance
 * @property $attribute
 * @property array|string $rules
 * @method getValue
 */
trait FieldValidation
{
    public $errors = [];
    public $rules = [];

    public $predefinedValidators = '';

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function addError($errorMessage)
    {
        $this->errors[] = $errorMessage;
    }

    public function validate()
    {
        return $this->isValid();
    }

    public function isValid()
    {
        $this->errors = [];

        $rules = $this->rules ?? [];

        foreach ($rules as $key => $one) {
            if (is_string($key) || is_int($key) && is_string($one)) {

                if (is_int($key) && is_string($one)) {
                    $key = $one;
                    $one = [];
                }

                $validator = $this->getPredefinedValidator($key, $this, $one);
                $validator->validate();
            } else if ($one instanceof \Closure) {
                $one($this);
            } else if (is_array($one)) {
                $validatorClass = array_shift($one['class']);

                /** @var Validator $validator */
                $validator = new $validatorClass($this, $one);
                $validator->validate();
            }
        }

        return count($this->errors) === 0;
    }

    /**
     * @param $validator
     * @return Validator
     */
    public function getPredefinedValidator($validator, $field, $options)
    {
        switch ($validator) {
            case 'max':
                return new MaxLengthValidator($field, ['value' => $options]);
            case 'min':
                return new MinLengthValidator($field, ['value' => $options]);
            case 'reg':
                return new RegexpValidator($field, ['pattern' => $options]);
            case 'required':
                return new RequiredValidator($field, []);
            case 'strip-tags':
                return new StripTagsValidator($field, []);
        }

        throw new \Exception('Validator not found');
    }
}
