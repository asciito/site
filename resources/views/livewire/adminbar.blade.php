<?php

use Filament\Facades\Filament;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component {
    protected ?array $userMenuItems = null;

    public function getUserMenuItems(): array
    {
        $this->userMenuItems = Filament::getUserMenuItems();

        foreach ($this->userMenuItems as $action) {
            $action->defaultView($action::GROUPED_VIEW);
        }

        if (blank($this->userMenuItems)) {
            $this->userMenuItems = null;
        }

        return $this->userMenuItems ?? [];
    }
}; ?>

<div class="relative h-16 bg-white shadow-sm z-9999">
    <div class="mx-auto h-full">
        <nav class="h-full flex items-center justify-between text-dark-blue text-sm px-4">
            <ul class="flex items-center justify-start gap-6">
                <li>
                    <x-filament::link
                        :href="route('filament.webtools.pages.dashboard')"
                        :icon-size="IconSize::Small"
                        icon="heroicon-s-wrench-screwdriver"
                        class="cursor-pointer text-blue-700"
                    >
                        <strong>Webtools</strong>
                    </x-filament::link>
                </li>

                @if (\Illuminate\Support\Facades\Route::is('post'))
                    <li>
                        <a
                            href="{{ route('filament.webtools.resources.posts.edit', \Illuminate\Support\Facades\Route::current()->parameter('post')) }}"
                            class="flex items-center gap-x-2 leading-2 hover:underline hover:underline-1"
                        >
                            <x-icon name="heroicon-s-pencil-square" class="w-4"/>

                            <span>Edit Post</span>
                        </a>
                    </li>
                @endif
            </ul>

            <x-filament-panels::user-menu/>
        </nav>
    </div>
</div>
