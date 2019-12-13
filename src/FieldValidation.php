<?php

namespace Owlcoder\Forms;

use Illuminate\Validation\ValidationException;

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

    public function validate()
    {
        $value = $this->getValue();
        return $this->isValid($value);
    }

    public function isValid($value)
    {
        $this->errors = [];

        $attribute = $this->attribute;

        $validator = validator();

        if (empty($this->rules)) {
            return true;
        }

        try {
            $validatedData = $validator->validate([
                $attribute => $value
            ], [
                $attribute => $this->rules,
            ]);

            $this->setValue($validatedData[$attribute]);
        } catch (ValidationException $exception) {
            $this->errors = $exception->errors()[$attribute];
        }

        return count($this->errors) === 0;
    }
}
