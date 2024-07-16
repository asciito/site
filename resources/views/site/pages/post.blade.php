<x-site::layout>
    <h1 class="text-4xl mb-8 text-slate-800">{{ \Illuminate\Support\Str::title($post->title) }}</h1>

    <img src="" alt="">

    {!! $post->content !!}
</x-site::layout>
