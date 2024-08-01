@props(['title', 'code'])

@php
    $page = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        robots: 'noindex, nofollow',
    );
@endphp

<x-site::layout :$page :show-footer="false">
    <div class="relative flex items-top justify-center min-h-[calc(100vh-13.2rem)] sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto">
            <div class="flex flex-col gap-8 items-center pt-8 sm:justify-start sm:pt-0 text-center">
                <h1 class="text-4xl sm:text-6xl md:text-8xl">
                    Error <span @class(["font-bold", "text-harlequin-600" => (int) $code < 500, "text-red-600" => (int) $code >= 500 ])>{{ $code }}</span>
                </h1>

                <div {{ $message->attributes->class(["flex flex-col gap-1 md:text-2xl"]) }}>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
</x-site::layout>
