<?php

namespace App\Site;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{

    public string $site_name;

    public string $site_description;

    public ?string $site_image;

    public ?string $instagram_handler;

    public ?string $linkedin_handler;

    public ?string $twitter_handler;

    public ?string $github_handler;

    public static function group(): string
    {
        return 'site';
    }
}
