<?php

namespace Owl\OwlForms\Providers;

use Carbon\Laravel\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\View\Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__ . '/../resources/views');

        return parent::boot();
    }
}
