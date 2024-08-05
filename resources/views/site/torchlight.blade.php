@props(['content', 'language'])

<div class="relative pb-[1px] bg-slate-800/80">
    <pre class="block whitespace-nowrap rounded-none bg-[#292D3E]">
        <div class="max-h-[30rem]">
            <x-torchlight-code :language="$language" class="whitespace-pre" :contents="htmlspecialchars_decode($content)"/>
        </div>
    </pre>

    <p class="absolute -bottom-[10px] left-2 flex text-xs text-white">
        <span>Powered by <a href="https://torchlight.dev" target="_blank" class="font-semibold text-white hover:text-harlequin-400 visited:text-harlequin-600">Torchlight</a></span>
        <img src="{{ asset('img/torchlight-logo.svg') }}" class="w-4 h-4 !m-0" alt="Torchlight logo"/>
    </p>
</div>
