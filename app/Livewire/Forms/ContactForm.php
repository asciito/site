<?php

namespace App\Livewire\Forms;

use App\Events\Contacted;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class ContactForm extends Form
{
    public string $name = '';

    public string $lastName = '';

    public string $email = '';

    public string $message = '';

    public function rules(): array
    {
        return [
            'name' => 'required|min:2',
            'lastName' => 'required|min:2',
            'email' => 'required|email',
            'message' => 'required|min:32|max:512',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'name' => '`NAME`',
            'lastName' => '`LAST NAME`',
            'email' => '`EMAIL`',
            'message' => '`MESSAGE`',
        ];
    }

    public function contact(): void
    {
        $this->validate();

        $this->sendMessage();

        $this->reset();
    }

    public function sendMessage(): void
    {
        $this->sanitizeMessage();

        Contacted::dispatch(
            $this->resolveContact(),
            $this->message,
        );
    }

    public function sanitizeMessage(): void
    {
        $this->message = strip_tags($this->message);
    }

    public function resolveContact(): \App\Site\Models\Contact
    {
        return \App\Site\Models\Contact::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => "{$this->name} {$this->lastName}",
            ]
        );
    }

    public function boot(): void
    {
        $this->withValidator(function ($validator) {
            $validator->after(function () {
                if (in_array(Str::lower($this->email), config('site.allowed_emails'))) {
                    throw ValidationException::withMessages([
                        "{$this->getPropertyName()}.email" => 'AHA! nice try',
                    ]);
                }
            });
        });
    }
}
