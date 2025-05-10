<x-site::layout :$page :show-footer="false" :should-show-navigation="false">
    <div class="relative flex items-top justify-center min-h-[calc(100vh-13.2rem)] sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto">
            <div class="flex flex-col gap-8 items-center pt-8 sm:justify-start sm:pt-0 text-center">
                <h1 class="text-4xl sm:text-6xl md:text-8xl">
                    Error <span @class(["font-bold", "text-harlequin-600" => (int) $code < 500, "text-red-600" => (int) $code >= 500 ])>{{ $code }}</span>
                </h1>

                <div {{ $message->attributes->class(["flex flex-col gap-1 md:text-2xl"]) }}>
                    {{ $message }}
                </div>

                @if ($code < 500)
                    <div>
                        @php
                            $onSite = \Illuminate\Support\Str::contains(url()->previous(), request()->getHost());

                            $safeBackUrl = $onSite ? url()->previous() : route('home');
                        @endphp

                        <x-site::button class="flex" :href="$safeBackUrl">
                            <div class="flex gap-2 items-center">
                                <x-heroicon-c-arrow-left class="w-5"/>

                                Go Back
                            </div>
                        </x-site::button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-site::layout>
