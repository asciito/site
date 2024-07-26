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
    <div class="w-full h-32 grayscale mb-5">
        <div class="w-full h-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
        </div>
    </div>

    <h2 class="text-4xl mb-8">Recent Posts</h2>

    <livewire:posts />
</x-site::layout>
