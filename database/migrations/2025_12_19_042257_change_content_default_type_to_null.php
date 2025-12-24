<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! $this->runningInSqlite()) {
                $table->text('content')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! $this->runningInSqlite()) {
                $table->text('content')->nullable(false)->change();
            }
        });
    }

    /**
     * Check if running on SQLite
     */
    protected function runningInSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
