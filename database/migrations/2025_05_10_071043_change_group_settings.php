<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected const string DEFAULT_GROUP = \App\Settings\Database\Schema\Builder::DEFAULT_GROUP;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Settings\Database\Schema\Settings::renameGroup('site', self::DEFAULT_GROUP);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Settings\Database\Schema\Settings::renameGroup(self::DEFAULT_GROUP, 'site');
    }
};
