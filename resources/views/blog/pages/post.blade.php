<x-site::layout :page="$post">
    @if($post->isDraft())
        <div class="flex mb-2">
            <span class="text-sm px-2 py-1 bg-slate-200 text-slate-800">{{ $post->status->name }}</span>
        </div>
    @endif

    <div class="relative">
        <div class="absolute bottom-4 left-4 z-50 space-y-2 drop-shadow-xl">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-white font-semibold">
                <span>{{ \Illuminate\Support\Str::title($post->title) }}</span>
            </h1>

            <p class="text-white font-thin text-sm mt-2">
                {{ $post->getDate() }}
            </p>
        </div>

        <div class="absolute z-40 top-0 left-0 right-0 bottom-0 bg-slate-600/20 backdrop-blur-md"></div>

        <div class="relative z-30 aspect-video">
            @if($media = $post->getFirstMedia())
                <x-image :src="$media" :srcset="$media->getSrcset()"/>
            @else
                <div class="w-full h-full bg-slate-300"></div>
            @endif
        </div>
    </div>

    <div id="content" class="mt-4 content">
        @if ($tableOfContent = $post->getTableOfContent())
            <div id="table-of-content">
                <h2 class="text-2xl mb-4">Table of Content</h2>

                {{ $tableOfContent }}
            </div>

            <hr>
        @endif

        {{ $post->getContent() }}
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toc = document.getElementById('table-of-content');

                if (! toc) return;

                const headings = document.querySelectorAll('#content h2, #content h3, #content h4, #content h5, #content h6');

                const slugify = text => text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

                toc.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', event => {
                        event.preventDefault();

                        const targetSlug = link.getAttribute('href').slice(1);

                        const targetHeading = Array.from(headings).find(h => slugify(h.innerText) === targetSlug);

                        if (targetHeading) {
                            window.scrollTo({ top: targetHeading.offsetTop, behavior: 'smooth' });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-site::layout>
