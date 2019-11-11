<?php

namespace Owl\OwlForms\Fields;

use Owl\OwlForms\Form;
use Illuminate\Support\Arr;

class ArrayJsonField extends ArrayField
{
    public function createInitialForms()
    {
        $this->value = json_decode($this->value, true);

        if (is_array($this->value)) {
            foreach ($this->value as $key => $row) {
                $this->forms[] = $this->createForm($row, $key);
            }
        }
    }

    public function apply()
    {
        parent::apply(json_encode($this->getValue()));
    }
}
