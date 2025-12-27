<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait InteractsWithSeederForTesting
{
    protected function getUser(): Authenticatable&Model
    {
        if (! User::exists()) {
            $this->command->call('make:filament-user', [
                '--name' => 'Test User',
                '--email' => 'test@example.com',
                '--password' => 'password',
            ]);
        }

        return User::first();
    }

    public function run(null|(Authenticatable&Model) $user = null, int $total = 10): void
    {
        $user = $user ?? $this->getUser();

        $this->process($user, max($total, 10));
    }

    public function process(Authenticatable&Model $user, int $total): void
    {
        throw new LogicException('You must implement the process method in the seeder using the HasUser concern.');
    }

    protected function createUser(): Authenticatable&Model {}
}
