<?php

namespace App\Site;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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
        Blade::anonymousComponentPath(resource_path('views/site/errors'), 'site-errors');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->afterLoadingEnvironment(function () {
            View::share('settings', config('site.settings'));
        });
    }
}
