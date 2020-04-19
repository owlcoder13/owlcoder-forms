<?php

class PrefixArrayTestForm extends \Owlcoder\Forms\Form
{
    public $namePrefix = 'form_1';

    public function getFields()
    {
        return [
            [
                'attribute' => 'items',
                'nestedConfig' => [
                    'fields' => [
                        'id'
                    ],
                ],
            ]
        ];
    }
}
