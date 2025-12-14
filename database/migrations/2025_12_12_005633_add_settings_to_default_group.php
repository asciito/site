<?php

declare(strict_types=1);

use Coyotito\LaravelSettings\Database\Schema\Blueprint;
use Coyotito\LaravelSettings\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::default(function (Blueprint $group) {
            // Basic Site Information
            $group->add('name');
            $group->add('description');
            $group->add('favicon');
            $group->add('image');

            // Social Media Handlers
            $group->add('twitter_handler');
            $group->add('facebook_handler');
            $group->add('instagram_handler');
            $group->add('linkedin_handler');
            $group->add('github_handler');
        });
    }

    public function down(): void
    {
        // Remove your settings here
    }
};
