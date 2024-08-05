@props(['content', 'language'])

<div class="relative">
    <pre class="block whitespace-nowrap rounded-none bg-[#292D3E]">
        <div class="max-h-[30rem]">
            <x-torchlight-code :language="$language" class="whitespace-pre" :contents="htmlspecialchars_decode($content)"/>
        </div>
    </pre>

    <p class="absolute -bottom-9 left-0 flex text-xs">
        <span>Powered by <a href="https://torchlight.dev" target="_blank">Torchlight</a></span>
        <img src="{{ asset('img/torchlight-logo.svg') }}" class="w-4 h-4 !m-0" alt="Torchlight logo"/>
    </p>
</div>
