@php
    use RalphJSmit\Laravel\SEO\Support\SEOData;
    use function Coyotito\LaravelSettings\Helpers\settings;

    $seoData = new SEOData(
        title: 'Experience | '.(settings('name') ?? config('app.name')),
        description: 'Explore all my past experience and contact me at '.('ayax.cordova@aydev.mx'),
    );
@endphp

<x-site::layout :page="$seoData">
    <div class="text-center mb-10">
        <h1 class="text-4xl lg:text-5xl leading-snug! mb-8">Yes, you found me!</h1>

        <p>
            Are you HR (úchale de aquí 😅), or just someone curious about my professional journey?
            Take a look around, download my résumé, and <x-filament::link :href="route('contact')" class="text-blue-700"><strong>Contact Me</strong></x-filament::link> — I’d be happy to chat.
        </p>
    </div>

    <div>
        <livewire:experience />
    </div>
</x-site::layout>
