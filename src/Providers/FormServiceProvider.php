<?php

namespace Owl\OwlForms\Providers;

use Carbon\Laravel\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\View\Factory $view */
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'forms');

        return parent::boot();
    }
}
