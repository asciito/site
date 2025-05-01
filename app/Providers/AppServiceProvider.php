<?php

namespace App\Providers;

use App\Site;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setDefaultSiteImage(app(Site\SiteSettings::class)->site_image);
    }

    protected function setDefaultSiteImage(?string $url): void
    {
        app('config')->set(['seo.image.fallback' => $url]);
    }
}
