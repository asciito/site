<?php

use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('site::pages.Layout.site', ['titlePage' => 'Contact'])] class extends Component {
    public \App\Livewire\Forms\ContactForm $form;

    public function submit(): void
    {
        $this->validate();

        $this->form->contact();

        $this->displaySuccessMessage();
    }

    public function displaySuccessMessage(): void
    {
        // TODO: Implement
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-4xl mb-8">Contact Me</h2>

        <p>
            Please contact me if you have any inquiry or you want to work with
            me. If is something related with a project you have in mind, please
            put as much information you need.
        </p>
    </div>

    <form
        wire:submit.prevent="submit"
        @class(["relative grid md:grid-cols-2 space-y-4", "pt-40" => $errors->isNotEmpty()])
    >
        @if ($errors->isNotEmpty())
            <div class="absolute top-0 left-0 right-0 bg-dark-blue-200 p-4">
                <div class="flex justify-center">
                    <p class="uppercase font-bold font-mono text-dark-blue-200 text-xl mb-4 px-4 py-0 bg-gray-300">Form Error</p>
                </div>

                <ul class="space-y-1">
                    @error('form.name')
                        <li class="text-white uppercase text-xs">* {!! \Illuminate\Support\Str::replaceMatches('/`(.*)`/', '<strong>\0</strong>', $message) !!}</li>
                    @enderror

                    @error('form.lastName')
                        <li class="text-white uppercase text-xs">* {!! \Illuminate\Support\Str::replaceMatches('/`(.*)`/', '<strong>\0</strong>', $message) !!}</li>
                    @enderror

                    @error('form.email')
                        <li class="text-white uppercase text-xs">* {!! \Illuminate\Support\Str::replaceMatches('/`(.*)`/', '<strong>\0</strong>', $message) !!}</li>
                    @enderror

                    @error('form.message')
                        <li class="text-white uppercase text-xs">* {!! \Illuminate\Support\Str::replaceMatches('/`(.*)`/', '<strong>\0</strong>', $message) !!}</li>
                    @enderror
                </ul>
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

        <div class="col-span-full flex justify-end">
            <button class="px-6 py-2 bg-[rgba(51,255,51,1.0)] cursor-pointer">
                SUBMIT
            </button>
        </div>
    </form>
</div>
