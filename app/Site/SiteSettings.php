<?php

namespace App\Site;

use App\Settings\Settings;

class SiteSettings extends Settings
{
    public ?string $name = null;

    public ?string $description = null;

    public ?string $favicon = null;

    public ?string $image = null;

    public ?string $twitter_handler = null;

    public ?string $facebook_handler = null;

    public ?string $instagram_handler = null;

    public ?string $linkedin_handler = null;

    public ?string $github_handler = null;

    public static function group(): string
    {
        return 'site';
    }
}
