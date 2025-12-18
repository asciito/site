<?php

namespace App\Site;

use Coyotito\LaravelSettings\Facades\SettingsManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Override;

class SiteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->loadViewsFrom(resource_path('views/site'), 'site');

        Blade::componentNamespace('App\\Site\\View\\Components', 'site');

        SettingsManager::addNamespace('App\\Site\\Settings');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
