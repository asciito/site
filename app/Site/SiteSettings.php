<?php

namespace App\Site;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{

    public string $site_name;

    public string $site_description;

    public ?string $site_image;

    public static function group(): string
    {
        return 'site';
    }
}
