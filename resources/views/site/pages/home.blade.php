@php
    use Illuminate\Support\Facades\Storage;
    use function Coyotito\LaravelSettings\Helpers\settings;

    $seoData = new \RalphJSmit\Laravel\SEO\Support\SEOData(
        title: settings('name'),
        description: settings('description'),
        image: settings('image') ? Storage::disk('public')->url(settings('image')) : null,
    );
@endphp

<x-site::layout :page="$seoData">
    <header class="block text-center space-y-4 mb-8">
        @if ($introduction = $user->introduction)
            {!! str($introduction)->markdown()->toHtmlString() !!}
        @else
            <p>There's no introduction available</p>
        @endif
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

        @empty($user)
            <p class="text-center">User not yet created</p>
        @else
            <div class="prose prose-pre:ring-1 prose-pre:ring-black/5 prose-pre:shadow-sm max-w-none mt-8">
                @if ($description = $user->description)
                    {!! str($description)->markdown()->sanitizeHtml() !!}
                @else
                    <p class="text-center">There's no description available</p>
                @endif
            </div>
        @endempty
    </section>
</x-site::layout>
