<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('site', function (SettingsBlueprint $blueprint) {
            $blueprint->add('instagram_handler');
            $blueprint->add('linkedin_handler');
            $blueprint->add('twitter_handler');
            $blueprint->add('github_handler');
        });
    }
};
