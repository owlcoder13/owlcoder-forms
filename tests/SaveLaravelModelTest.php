<?php

class SaveLaravelModelTest extends \Tests\TestCase
{
    public function testData()
    {
        DB::transaction(function () {
            $user = new \App\User();
            $user->name = 'test';

            $model = new \Owl\OwlForms\Form([
                'fields' => [
                    ['attribute' => 'email'],
                    ['attribute' => 'password']
                ],
            ], $user);

            $model->load(['email' => 'test', 'password' => 'test']);
            $this->assertTrue($model->save());

            $this->assertTrue($model->id != null);

            DB::rollBack();
        });

    }
}
