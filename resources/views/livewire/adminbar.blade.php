<?php

use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component {
    public \App\Models\User $user;

    public function mount(): void
    {
        $this->user = \Illuminate\Support\Facades\Auth::user();
    }
}; ?>

<div class="absolute top-0 left-0 right-0 z-[9999] h-16 bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 h-full">
        <nav class="h-full flex items-center justify-between text-dark-blue-600 text-sm">
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

            <div
                x-data="{
                open: false,
                toggle() {
                    if (this.open) {
                        return this.close()
                    }

                    this.$refs.button.focus()

                    this.open = true
                },
                close(focusAfter) {
                    if (! this.open) return

                    this.open = false

                    focusAfter && focusAfter.focus()
                }
            }"
                x-on:keydown.escape.prevent.stop="close($refs.button)"
                x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                x-id="['dropdown-button']"
                class="relative py-1"
            >
                <!-- Button -->
                <button
                    x-ref="button"
                    x-on:click="toggle()"
                    :aria-expanded="open"
                    :aria-controls="$id('dropdown-button')"
                    type="button"
                    class="flex items-center space-x-2"
                >
                    <img src="{{ filament()->getUserAvatarUrl($user) }}" class="object-cover object-center rounded-full w-8 h-8"/>
                </button>

                <!-- Panel -->
                <div
                    x-ref="panel"
                    x-show="open"
                    x-transition.origin.top.right
                    x-on:click.outside="close($refs.button)"
                    :id="$id('dropdown-button')"
                    style="display: none;"
                    class="absolute right-0 mt-4 w-40 bg-white text-slate-800 shadow-lg"
                >
                    <form
                        action="{{ filament()->getLogoutUrl() }}" method="POST"
                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-dark-blue hover:text-slate-50 disabled:text-gray-500"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="w-full flex space-x-1"
                        >
                            <x-icon name="heroicon-s-arrow-left-end-on-rectangle" class="w-5"/>
                            <span>{{ $logoutItem?->getLabel() ?? __('filament-panels::layout.actions.logout.label') }}</span>
                        </button>
                    </form>
                </div>
            </div
        </nav>
    </div>
</div>
