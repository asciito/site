<x-site::layout :page="$post">
    <div class="mb-8 space-y-2">
        <h1 class="text-4xl text-slate-800">
            @if($post->isDraft())
                <div class="flex mb-2">
                        <span class="text-sm px-2 py-1 bg-slate-200 text-slate-800">{{ $post->status->name }}</span>
                </div>
            @endif

            <span>{{ \Illuminate\Support\Str::title($post->title) }}</span>
        </h1>

        <p class="text-blue-600 text-sm">
            <span>{{ $post->created_at->equalTo($post->updated_at) ? 'Created on' : 'Last updated on' }}</span>
            <time>{{ $post->updated_at->format('F d, Y') }}</time>
        </p>
    </div>

    @php
        $media = $post->getFirstMedia();
    @endphp

    {{ $post->getFirstMedia()?->img()->attributes(['class' => 'w-full']) }}

    <div class="mt-4 prose max-w-full prose-figcaption:mt-0 prose-img:has-[figcaption]:mb-2">
        @php
        $content = $post->content;

        $content = \Illuminate\Support\Str::of($content)->replaceMatches('/<pre>(.*?)<\/pre>/s', function ($matches) {
            // Capture the content between <pre> and </pre>
            $content = $matches[1];

            return \Illuminate\Support\Facades\Blade::render(
                <<<'TEMPLATE'
                <pre class="block whitespace-nowrap rounded-none bg-[#292D3E]">
                    <div class="max-h-[30rem]">
                        <x-torchlight-code language="php" class="whitespace-pre" :contents="$content"/>
                    </div>
                </pre>
                TEMPLATE,
                ["content" => htmlspecialchars_decode($content)]
            );
        });
        @endphp

        {!! $content !!}
    </div>
</x-site::layout>
