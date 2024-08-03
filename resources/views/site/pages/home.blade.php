@php
use Illuminate\Support\Facades\Storage;

$settings = app(\App\Site\SiteSettings::class);

$seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
    title: $settings->site_name,
    description: $settings->site_description,
    image: $settings->site_image ? Storage::disk('public')->url($settings->site_image) : null,
);
@endphp

<x-site::layout :page="$seoData">
    <header class="block text-center space-y-4 mb-8">
        <h1 class="text-2xl md:text-4xl lg:text-5xl !leading-snug"><span>Hi, I'm Ayax Córdova</span> <span class="block">(A.K.A <strong>@asciito</strong>)<span></h1>
        <p>As a Software Engineer, I share insights on PHP (<strong>Laravel</strong>), JavaScript, Python, and web development. Explore tutorials, coding tips, and programming guides to boost your skills. Feel free to reach out for tech advice!</p>
    </header>

    <div class="w-full h-32 grayscale mb-5">
        <div class="w-full h-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
        </div>
    </div>

    <h2 class="text-4xl mb-8">Recent Posts</h2>

    <livewire:posts />
</x-site::layout>
