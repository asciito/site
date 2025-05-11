
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Site\Models\Contact::class)->after((new \App\Models\User)->getForeignKey())->nullable();
        });

        $users = \App\Models\User::with('messages')
            ->whereNotIn('email', config('site.allowed_emails'))
            ->get();

        foreach ($users as $user) {
            $contact = \App\Site\Models\Contact::create([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            foreach ($user->messages as $message) {
                $message->update(['contact_id' => $contact->id]);
            }
        }

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn((new \App\Models\User)->getForeignKey());
        });

        $users->each->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\User::class)->after((new \App\Site\Models\Contact)->getForeignKey())->nullable();
        });

        $contacts = \App\Site\Models\Contact::with('messages')->get();

        foreach ($contacts as $contact) {
            $user = \App\Models\User::create([
                'name' => $contact->name,
                'email' => $contact->email,
                'password' => bcrypt(\Illuminate\Support\Str::random(32)),
            ]);

            foreach ($contact->messages as $message) {
                $message->update(['user_id' => $user->id]);
            }
        }

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn((new \App\Site\Models\Contact)->getForeignKey());
        });

        Schema::dropIfExists('contacts');
    }
};
