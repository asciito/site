@php
    use RalphJSmit\Laravel\SEO\Support\SEOData;
    use function Coyotito\LaravelSettings\Helpers\settings;

    $seoData = new SEOData(
        title: 'My work experience | '.(settings('name') ?? config('app.name')),
        description: 'Explore my professional experience and skills. Download my résumé and contact me to discuss open roles, projects, or collaborations'
    );
@endphp

<x-site::layout :page="$seoData">
    <div class="text-center mb-10">
        <h1 class="text-4xl lg:text-5xl leading-snug! mb-8">Yes, you found me!</h1>

        <p>
            Are you HR, or just someone curious about my professional journey?
            Take a look around, download my résumé, and <x-filament::link :href="route('contact')" class="text-blue-700"><strong>Contact Me</strong></x-filament::link>
            — I’d be happy to chat to collaborate and discuss your next project 😉
        </p>
    </div>

    <hr class="my-12">

    <div>
        <livewire:experience />
    </div>
</x-site::layout>
