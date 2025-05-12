<?php

use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="relative h-16 bg-white shadow z-[9999]">
    <div class="mx-auto h-full">
        <nav class="h-full flex items-center justify-between text-dark-blue-600 text-sm px-4 md:px-6 lg:px-8">
            <ul class="flex items-center justify-start gap-6">
                <li>
                    <a
                        href="{{ route('filament.webtools.pages.dashboard') }}"
                        class="group/link relative inline-flex items-center justify-center outline-none gap-1.5 cursor-pointer"
                    >
                        <x-icon name="heroicon-s-wrench-screwdriver" class="w-4"/>

                        <span class="font-semibold group-hover/link:underline group-focus-visible/link:underline text-sm">Webtools</span>
                    </a>
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

            <x-filament-panels::user-menu />
        </nav>
    </div>
</div>
