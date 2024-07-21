<x-site::layout :page="$post">
    <div class="mb-8 space-y-2">
        <h1 class="text-4xl text-slate-800">{{ \Illuminate\Support\Str::title($post->title) }}</h1>

        <p class="text-blue-600 text-sm">
            <span>{{ $post->created_at->equalTo($post->updated_at) ? 'Created on' : 'Last updated on' }}</span>
            <time>{{ $post->updated_at->format('F d, Y') }}</time>
        </p>
    </div>

    @php
        $media = $post->getFirstMedia();
    @endphp

    {{ $post->getFirstMedia()?->img()->attributes(['class' => 'w-full']) }}

    <div class="mt-4 prose prose-xl prose-figcaption:mt-0 prose-img:has-[figcaption]:mb-2">
        {!! $post->content !!}
    </div>
</x-site::layout>
