<x-site::layout :page="$post">
    @php

    @endphp

    @if($post->isDraft())
        <div class="flex mb-2">
            <span class="text-sm px-2 py-1 bg-slate-200 text-slate-800">{{ $post->status->name }}</span>
        </div>
    @endif

    <div class="relative">
        <div class="absolute bottom-[1rem] left-[1rem] z-50 space-y-2 drop-shadow-xl">
            <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-white font-semibold">
                <span>{{ \Illuminate\Support\Str::title($post->title) }}</span>
            </h1>

            <p class="text-white font-thin text-sm mt-2">
                <span>{{ $post->created_at->equalTo($post->updated_at) ? 'Created on' : 'Last updated on' }}</span>
                <time>{{ $post->updated_at->format('F d, Y') }}</time>
            </p>
        </div>

        <div class="absolute z-40 top-0 left-0 right-0 bottom-0 bg-slate-600/20 backdrop-blur-md"></div>

        <div class="relative z-30 aspect-video">
            @if($media = $post->getFirstMedia())
                <x-site::image-media :$media />
            @else
                <div class="w-full h-full bg-slate-300"></div>
            @endif
        </div>
    </div>

    <div class="mt-4 content">
        {{ $post->getContent() }}
    </div>
</x-site::layout>
