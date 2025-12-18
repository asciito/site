<?php

use App\Blog\BlogServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\Filament\WebtoolsPanelProvider;
use App\Providers\VoltServiceProvider;
use App\Settings\SettingsProvider;
use App\Site\SiteServiceProvider;

return [
    AppServiceProvider::class,
    WebtoolsPanelProvider::class,
    SettingsProvider::class,
    SiteServiceProvider::class,
    VoltServiceProvider::class,
    BlogServiceProvider::class,
];
