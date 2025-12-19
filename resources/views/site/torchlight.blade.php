@props(['content', 'language'])

@php
    $content = html_entity_decode($content)
@endphp

<div
    x-data="{
        code: @js($content),
        show: false,
        copy: async function () {
            const blob = new Blob([this.code], { type: 'text/plain' });
            const data = [new ClipboardItem({ 'text/plain': blob })];

            await navigator.clipboard.write(data);

            this.show = true;

            setTimeout(() => this.show = false, 2000);
        }
    }"
    class="relative pb-px bg-slate-800/80">
    <div class="absolute top-0 right-0 m-2">
        <span
            x-cloak
            x-show="show"
            x-transition:enter.duration.200ms="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave.duration.400ms="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"

            class="inline-block text-xs text-slate-50">Copied!</span>

        <button aria-label="Copy button" x-on:click="copy" class="bg-harlequin-700 hover:bg-harlequin-600 transition text-xs font-semibold px-2 py-1">
            Copy <x-icon name="heroicon-s-square-2-stack" class="inline w-4 h-4"/>
        </button>
    </div>

    <pre class="block p-0 whitespace-nowrap overflow-hidden rounded-none selection:bg-white/20 bg-[#292D3E]">
        <div class="torchlight-wrapper w-full max-h-120 p-4 overflow-y-scroll overflow-x-scroll">
            <x-torchlight-code :language="$language" class="whitespace-pre" :contents="$content"/>
        </div>
    </pre>

    <p class="absolute -bottom-[10px] left-2 flex text-xs text-white">
        <span>Powered by <a href="https://torchlight.dev" target="_blank" class="font-semibold text-white hover:text-harlequin-400 visited:text-harlequin-600">Torchlight</a></span>
        <img src="{{ asset('img/torchlight-logo.svg') }}" class="w-4 h-4 m-0!" alt="Torchlight logo"/>
    </p>
</div>
