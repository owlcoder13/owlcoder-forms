<?php

class FormSetFieldTest extends \Tests\TestCase
{
    public function testData()
    {
        /**
         * set initial data for testing
         */
        $initialData = [
            'content' => '',
        ];

        /**
         * create form
         */
        $form = new \Owl\OwlForms\Form([
            'fields' => [
                [
                    'attribute' => 'content',
                    'class' => \Owl\OwlForms\Fields\FormSetField::class,
                    'nestedConfig' => [
                        'fields' => [
                            ['attribute' => 'title'],
                            ['attribute' => 'subtitle'],
                        ],
                    ],
                ]
            ]
        ], $initialData);

        /**
         * load data to form
         */
        $form->load([
            'content' => [
                2 => [
                    'title' => 'test',
                    'subtitle' => 'case',
                ],
                5 => [
                    'title' => 'test2',
                    'subtitle' => 'case2',
                ],
            ],
        ]);

        /**
         * return current form data
         */
        $result = $form->toArray();

        $this->assertTrue(count($result['content']) == 2);


        /**
         * apply changes to initial data
         */
        $form->save();

        /**
         * check initial data changed
         */
        $this->assertTrue(is_array($initialData));
        $this->assertTrue(count($initialData['content']) == 2);
    }
}
