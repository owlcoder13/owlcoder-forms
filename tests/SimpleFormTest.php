<?php

class SimpleFormTest extends \Tests\TestCase
{
    public function testData()
    {
        $initialData = [
            'title' => 'title <script>alert();</script>',
            'subtitle' => 'subtitle',
            'content' => 'some text ',
        ];

        $form = new \Owlcoder\Forms\Form([
            'fields' => [
                [
                    'attribute' => 'title',
                    'rules' => [
                        'strip-tags'
                    ],
                ],
                [
                    'attribute' => 'subtitle'
                ],
                [
                    'attribute' => 'content',
                    'class' => \Owlcoder\Forms\Fields\TextAreaField::class
                ],
            ],
        ], $initialData);

        $this->assertTrue(! empty($form->render()));
        $this->assertTrue(! empty($form->js()));

        $formData = $form->toArray();

        $this->assertEquals($formData['title'], $initialData['title']);
        $this->assertEquals($formData['subtitle'], $initialData['subtitle']);
        $this->assertEquals($formData['content'], $initialData['content']);

        $form->validate();
        $formData = $form->toArray();

        $this->assertEquals($formData['title'], 'title alert();');
        $this->assertEquals($formData['subtitle'], $initialData['subtitle']);
        $this->assertEquals($formData['content'], $initialData['content']);
    }
}
