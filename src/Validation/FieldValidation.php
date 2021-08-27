<?php

namespace Owlcoder\Forms\Validation;

use Egulias\EmailValidator\EmailValidator;
use Owlcoder\Forms\Form;

/**
 * Trait FieldValidation
 * @package Owlcoder\Forms
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

                if ($validator == null) {
                    $validator = $this->getValidatorByClassName($key, $this, $one);
                }

                if ($validator == null) {
                    throw new \Exception(sprintf('Validator %s not found', $key));
                }

                $validator->makeValidation();
            } else if ($one instanceof \Closure) {
                $one($this);
            } else if (is_callable($one)) {
                call_user_func($one, $this, $this->value);
            } else if (is_array($one)) {
                $validatorClass = array_shift($one['class']);

                /** @var Validator $validator */
                $validator = new $validatorClass($this, $one);
                $validator->makeValidation();
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
                return new MaxLengthValidator($field, $options);
            case 'min':
                return new MinLengthValidator($field, $options);
            case 'reg':
                return new RegexpValidator($field, $options);
            case 'required':
                return new RequiredValidator($field, $options);
            case 'strip-tags':
                return new StripTagsValidator($field, $options);
            case 'number':
                return new NumberValidator($field, $options);
            case 'email':
                return new EmailValidator($field, $options);
            case 'filter':
                return new FilterValidator($field, $options);
        }

        return null;
    }

    /**
     * Get validator by class Name
     *
     * @param $className
     * @param $field
     * @param $options
     * @return mixed|null
     */
    public function getValidatorByClassName($className, $field, $options)
    {
        if (class_exists($className)) {
            return new $className($field, $options);
        }

        return null;
    }
}
