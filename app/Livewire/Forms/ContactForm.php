<?php

namespace App\Livewire\Forms;

use App\Events\Contacted;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContactForm extends Form
{
    #[Validate('required|min:2', as: '`NAME`')]
    public string $name = '';

    #[Validate('required|min:2', as: '`LAST NAME`')]
    public string $lastName = '';

    #[Validate('required|email', as: '`EMAIL`')]
    public string $email = '';

    #[Validate('required|min:6|max:1024', as: '`MESSAGE`')]
    public string $message = '';

    public function contact(): void
    {
        $this->ensureIsNotRateLimited();

        RateLimiter::hit($this->throttleKey());

        $this->sendMessage();

        $this->reset();
    }

    public function sendMessage(): void
    {
        Contacted::dispatch(
            $this->resolveUser(),
            $this->message,
        );
    }

    public function resolveUser(): \App\Models\User
    {
        return \App\Models\User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => "{$this->name} {$this->lastName}",
                'password' => bcrypt(Str::random(32)),
            ]
        );
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.name' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->name).'|'.request()->ip());
    }
}
