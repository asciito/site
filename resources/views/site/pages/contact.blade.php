@php
    $seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: 'Contact',
        description: 'You have a project in mind? talk with me',
    );
@endphp

<x-site::layout :page="$seoData">
    <div class="text-center mb-10">
        <h1 class="text-4xl lg:text-5xl !leading-snug mb-8">Contact Me</h1>

        <p>
            Please contact me if you have any inquiry or you want to work with
            me. If is something related with a project you have in mind, please
            put as much information you need.
        </p>
    </div>

    <div>
        <livewire:contact-form/>
    </div>
</x-site::layout>
