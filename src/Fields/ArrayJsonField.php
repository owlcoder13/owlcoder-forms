<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\Helpers\DataHelper;
use Owlcoder\Forms\Form;
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
        DataHelper::set(
            $this->instance,
            $this->attribute,
            json_encode($this->getValue())
        );
    }
}
