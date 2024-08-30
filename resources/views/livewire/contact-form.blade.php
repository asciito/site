<?php

use Livewire\Volt\Component;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

new #[\Livewire\Attributes\Layout('site::pages.Layout.site')] class extends Component {
    use WithRateLimiting;

    public \App\Livewire\Forms\ContactForm $form;

    public bool $messageSend = false;

    public function submit(): void
    {
        try {
            $this->rateLimit(5, component: 'contact-form');
        } catch (TooManyRequestsException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => "Slow down! Please wait another {$e->secondsUntilAvailable} seconds to contact me again",
            ]);
        }

        $this->form->contact();

        $this->messageSend = true;
    }

    public function getSeoData(): \RalphJSmit\Laravel\SEO\Support\SEOData
    {
        return new \RalphJSmit\Laravel\SEO\Support\SEOData(
            title: 'Contact',
            description: 'The web change fast, and no matter your background, ' .
                         'and if you want to learn about web technologies your\'re ' .
                         'in the right place',
        );
    }
}; ?>

<form
    wire:submit.prevent="submit"
    @class(["relative grid md:grid-cols-2 space-y-4"])
>
    @if ($errors->isNotEmpty())
        <div class="col-span-full bg-dark-blue-200 p-4">
            <div class="flex justify-center">
                <p class="uppercase font-bold font-mono text-dark-blue-200 text-xl mb-4 px-4 py-0 bg-gray-300">Form Error</p>
            </div>

            @error('form.*')
                <ul class="space-y-1">
                    @foreach($errors->get('form.*') as $_ => $error)
                        <li class="text-white uppercase text-xs">* {!! \Illuminate\Support\Str::replaceMatches('/`(.*)`/', '<strong>\0</strong>', $error[0]) !!}</li>
                    @endforeach
                </ul>
            @enderror
        </div>
    @endif

    @if ($messageSend)
        <div
            class="col-span-full bg-dark-blue-200 p-4"
            x-data="{ open: true }"
            x-show="open"
            x-init="setTimeout(() => open = false, 5000)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
        >
            <div class="flex justify-center">
                <p class="uppercase font-bold font-mono text-dark-blue-200 text-2xl px-4 py-0 bg-gray-300">MESSAGE SEND</p>
            </div>
        </div>
    @endif

    <div class="col-span-full grid grid-cols-subgrid space-y-4 md:space-y-0 md:space-x-4">
        <x-site::input name="name" type="text" class="col-span-1" wire:model="form.name"/>

        <x-site::input name="lastName" type="text" class="col-span-1" wire:model="form.lastName"/>
    </div>

    <div class="col-span-full space-y-4">
        <x-site::input name="email" type="email" placeholder="your@email.com" wire:model="form.email"/>

        <x-site::textarea name="message" placeholder="your message" rows="5" wire:model="form.message"/>
    </div>

    <div class="col-span-full">
        <x-site::button
            type="submit"
            class="col-span-full"
            position="end"
        >Submit</x-site::button>
    </div>
</form>
