<?php

namespace Owlcoder\Forms\Fields;

class ColorInputField extends Field
{
    public function js()
    {
        return "
            $(function() {
                if (typeof $.spectrum === 'undefined') {
                    console.error('There is no spectrum colorpicker on a page');
                    return;
                }

                $('#$this->id')
                    .css({
                        cursor: 'pointer',
                        width: 'fit-content'
                    })
                    .spectrum({
                        type: 'component',
                        showInput: true,
                        showInitial: true,
                        palette: [ ],
                    });
            });
        ";
    }
}
