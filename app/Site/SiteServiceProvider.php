<?php

namespace App\Site;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->loadViewsFrom(resource_path('views/site'), 'site');

        Blade::componentNamespace('App\\View\\Site\\Components', 'site');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
