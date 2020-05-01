<?php

namespace Owlcoder\Forms\Fields;

use Owlcoder\Common\EventTrait;
use Owlcoder\Forms\Validation\FieldValidation;
use Owlcoder\Forms\Form;

use Owlcoder\Common\Helpers\DataHelper;
use stringEncode\Exception;

class DateField extends Field
{
    public $format = "yyyy-mm-dd";

    public function js()
    {
        return "$(el).datepicker({
		'dateFormat' : '{$this->format}',
		format: 'yyyy-mm-dd',
		beforeShow: function() {
        setTimeout(function(){
                $('.ui-datepicker').css('z-index', 101);
            }, 0);
        }
	});";
    }
}
