<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed emails to access the panel
    |--------------------------------------------------------------------------
    |
    | This option contains the emails allowed to access the panel
    |
    */

    'allowed_emails' => explode(',', (string) env('SITE_ALLOWED_EMAILS', '')) ?? [],

    /*
    |--------------------------------------------------------------------------
    | Webtools Panel path
    |--------------------------------------------------------------------------
    |
    | This option contains the path to  the webtools panel
    |
    */

    'webtools_path' => env('WEBTOOLS_PATH', 'webtools'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This option contains the cache settings used throughout the site
    |
    */

    'cache' => [
        'should_cache' => (bool) env('SITE_SHOULD_CACHE', env('APP_ENV', 'production') === 'production'),

        'ttl' => env('CACHE_TTL', 3600), // in minutes
    ],
];
