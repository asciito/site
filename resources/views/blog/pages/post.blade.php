<x-site::layout :page="$post">
    <article>
        @if($post->isDraft())
            <div class="flex mb-2">
                <span class="text-sm px-2 py-1 bg-slate-200 text-slate-800">{{ $post->status->name }}</span>
            </div>
        @endif

        <div class="relative">
            <header class="absolute bottom-4 left-4 right-4 z-50 space-y-2 drop-shadow-xl">
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-white font-semibold">
                    {{ \Illuminate\Support\Str::title($post->title) }}
                </h1>

                <p class="text-white font-thin text-sm mt-2">
                    {{ $post->getDate() }}
                </p>
            </header>

            <div class="absolute z-40 top-0 left-0 right-0 bottom-0 bg-slate-600/20 backdrop-blur-md"></div>

            @if($media = $post->getFirstMedia())
                <x-image :src="$media" :srcset="$media->getSrcset()" class="aspect-video"/>
            @else
                <div class="aspect-video bg-slate-300"></div>
            @endif
        </div>

        <div id="content" class="mt-8 content">
            @if ($tableOfContent = $post->getTableOfContent())
                <aside id="table-of-content">
                    <nav aria-labelledby="table-of-content-title">
                        <h2 id="table-of-content-title" class="text-center text-2xl mb-4 mt-0">Table of Content</h2>

                        {{ $tableOfContent }}
                    </nav>
                </aside>

            @endif

            <hr>

            {{ $post->getContent() }}
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const toc = document.getElementById('table-of-content');

                    if (! toc) return;

                    const headings = document.querySelectorAll('#content h2, #content h3, #content h4, #content h5, #content h6');

                    const slugify = text => {
                        return text
                            .trim()
                            .replace(/\s+/g, ' ')
                            .toLowerCase()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .replace(/['’]/g, '')
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                    };

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
    </article>
</x-site::layout>
