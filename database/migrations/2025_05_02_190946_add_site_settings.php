<?php

use App\Settings\Database\Schema\Blueprint;
use App\Settings\Database\Schema\Settings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Settings::in('site', function (Blueprint $settings) {
            $settings->add('name', config('app.name'));
            $settings->add('description');
            $settings->add('logo');
            $settings->add('favicon');

            $settings->add('instagram_handler');
            $settings->add('twitter_handler');
            $settings->add('facebook_handler');
            $settings->add('linkedin_handler');
            $settings->add('github_handler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Settings::dropSettingsIn('site');
    }
};
