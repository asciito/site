@php
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    use Illuminate\Support\Facades\Storage;
    use RalphJSmit\Laravel\SEO\Support\SEOData;
    use function Coyotito\LaravelSettings\Helpers\settings;

    $seoData = new SEOData(
        title: settings('name'),
        description: settings('description'),
        image: settings('image') ? Storage::disk('public')->url(settings('image')) : null,
    );
@endphp

<x-site::layout :page="$seoData">
    <header class="block text-center space-y-4 mb-8">
        <div class="content">
            @if ($introduction = $user?->introduction)
                {{ RichContentRenderer::make($introduction) }}
            @else
                <p>There's no introduction available</p>
            @endif
        </div>
    </header>

    <div class="w-full h-32 grayscale mb-5">
        <div class="w-full h-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('img/pixel-windows-xp.jpg') }}')">
        </div>
    </div>

    <h2 class="text-4xl mb-8">Recent Posts</h2>

    <livewire:posts/>

    <section>
        <div id="about-me" class="mb-20 sm:mb-28 h-px"></div>

        <h3 class="text-3xl mb-8 text-center">{{ $user?->name ?? 'User not created' }}</h3>

        <div class="content">
            @if ($description = $user?->description)
                {{ RichContentRenderer::make($description) }}
            @else
                <p class="text-center">There's no description available</p>
            @endif
        </div>
    </section>
</x-site::layout>
