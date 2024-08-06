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
            Please contact me if you have any questions or if you'd like to work
            together. If your inquiry is about a project you're considering, please
            include as much detail as you can.
        </p>
    </div>

    <div>
        <livewire:contact-form/>
    </div>
</x-site::layout>
