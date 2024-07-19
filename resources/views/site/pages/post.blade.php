<x-site::layout>
    <div class="mb-8 space-y-2">
        <h1 class="text-4xl text-slate-800">{{ \Illuminate\Support\Str::title($post->title) }}</h1>

        <p class="text-blue-600 text-sm">
            <span>{{ $post->created_at->equalTo($post->updated_at) ? 'Created on' : 'Last updated on' }}</span>
            <time>{{ $post->updated_at->format('F d, Y') }}</time>
        </p>
    </div>


    <img src="" alt="">

    {!! $post->content !!}
</x-site::layout>
