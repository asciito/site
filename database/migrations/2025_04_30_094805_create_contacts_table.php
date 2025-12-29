<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignIdFor(Contact::class)->nullable();
        });

        $allowedEmails = (array) config('site.allowed_emails', []);

        $usersQuery = DB::table('users');

        if (! empty($allowedEmails)) {
            $usersQuery->whereNotIn('email', $allowedEmails);
        }

        DB::table('contacts')->insertUsing(
            ['name', 'email', 'created_at', 'updated_at'],
            $usersQuery->select([
                'name',
                'email',
                DB::raw('CURRENT_TIMESTAMP'),
                DB::raw('CURRENT_TIMESTAMP'),
            ])
        );

        DB::table('messages')
            ->whereNotNull('user_id')
            ->update([
                'contact_id' => DB::raw('(SELECT c.id FROM contacts c JOIN users u ON u.email = c.email WHERE u.id = messages.user_id LIMIT 1)'),
            ]);

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn((new User)->getForeignKey());
        });

        $deleteUsersQuery = DB::table('users');
        if (! empty($allowedEmails)) {
            $deleteUsersQuery->whereNotIn('email', $allowedEmails);
        }
        $deleteUsersQuery->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after((new Contact)->getForeignKey())->nullable();
        });

        $passwordHash = Hash::make(Str::random(32));

        DB::table('users')->insertUsing(
            ['name', 'email', 'password', 'created_at', 'updated_at'],
            DB::table('contacts')->select([
                'name',
                'email',
                DB::raw("'$passwordHash'"),
                DB::raw('CURRENT_TIMESTAMP'),
                DB::raw('CURRENT_TIMESTAMP'),
            ])
        );

        DB::table('messages')
            ->whereNotNull('contact_id')
            ->update([
                'user_id' => DB::raw('(SELECT u.id FROM users u JOIN contacts c ON u.email = c.email WHERE c.id = messages.contact_id LIMIT 1)'),
            ]);

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn((new Contact)->getForeignKey());
        });

        Schema::dropIfExists('contacts');
    }
};
