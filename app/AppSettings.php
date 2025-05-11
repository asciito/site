<?php

namespace App;

<<<<<<<< HEAD:app/AppSettings.php
class AppSettings extends \App\Settings\Settings
========
class Settings extends \App\Settings\Settings
>>>>>>>> staging:app/Settings.php
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
}
