<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Override;
use RalphJSmit\Laravel\SEO\Facades\SEOManager;
use RalphJSmit\Laravel\SEO\Support\SEOData;

use function Coyotito\LaravelSettings\Helpers\settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->loadViewsFrom(resource_path('views/blog'), 'blog');
        $this->loadViewsFrom(resource_path('views/site'), 'site');

        Blade::componentNamespace('App\\View\\Components', 'site');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SEOManager::SEODataTransformer(function (SEOData $seo): SEOData {
            $image = filled($image = settings('image')) ? Storage::url($image) : null;
            $favicon = filled($favicon = settings('favicon')) ? Storage::url($favicon) : null;

            $seo->image ??= $image;
            $seo->favicon ??= $favicon;

            return $seo;
        });
    }
}
