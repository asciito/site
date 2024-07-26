<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('site', function (SettingsBlueprint $blueprint) {
            $blueprint->add('site_name', 'ASCIITO');
            $blueprint->add('site_description', 'The asciito site description');
            $blueprint->add('site_image');
        });
    }
};
