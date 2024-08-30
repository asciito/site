<?php

namespace App\Livewire\Forms;

use App\Events\Contacted;
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
        $this->validate();

        if (in_array(Str::lower($this->email), config('site.allowed_emails'))) {
            throw ValidationException::withMessages([
                "{$this->getPropertyName()}.email" => 'AHA! nice try',
            ]);
        }


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
}
