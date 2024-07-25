@php
$seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
    title: config('app.name'),
    description: 'The web change fast, and no matter your background, ' .
                 'and if you want to learn about web technologies your\'re ' .
                 'in the right place',
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
