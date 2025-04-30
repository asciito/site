@props([
    'href' => null,
    'size' => 'md',
    'position' => 'left',
    'shouldExpand' => false,
    'showLoadingIndicator' => false,
])

@php
    $sizeClasses = match ($size) {
        default => ($showLoadingIndicator ? 'px-12' : 'px-6').' py-4 text-md',
        'sm' => ($showLoadingIndicator ? 'px-10' : 'px-4').' py-4 text-sm',
        'lg' => ($showLoadingIndicator ? 'px-16' : 'px-8').' py-6 text-lg',
    };

    $positionClass = match ($position) {
        default => 'justify-start',
        'center' => 'justify-center',
        'end' => 'justify-end',
    };

    $attributes = $attributes->class([
        $sizeClasses,
        'select-none',
        'text-slate-800',
        'px-6 py-2 bg-harlequin',
        'cursor-pointer',
        'w-full' => $shouldExpand,
    ]);

@endphp

<div @class(['w-full flex flex-wrap leading-none', $positionClass])>
    @isset($href)
        <a
            href="{{ $href }}"
            {{ $attributes->class([
                'enabled',
                '[&.disabled]:opacity-80 [&.disabled]:cursor-not-allowed',
                '[&.enabled]:hover:bg-harlequin-600 [&.enabled]:active:bg-harlequin-400',
            ]) }}
            wire:loading.class="disabled"
            wire:loading.class.remove="enabled"
        >
            {{ $slot }}
        </a>
    @else
        <button
            {{ $attributes->class([
                'disabled:opacity-80 disabled:cursor-not-allowed',
                'enabled:hover:bg-harlequin-600 enabled:active:bg-harlequin-400',
                'text-center',
            ]) }}
            wire:loading.attr="disabled"
        >
            <span class="relative flex items-center">
                {{ $slot }}

                @if ($showLoadingIndicator)
                    <div wire:loading.flex class="absolute -right-8">
                        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                            <rect class="fill-white stroke-white" stroke-width="4" width="40" height="40" x="25" y="85">
                                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate>
                            </rect>
                            <rect class="fill-white stroke-white" stroke-width="4" width="40" height="40" x="85" y="85">
                                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate>
                            </rect>
                            <rect class="fill-white stroke-white" stroke-width="4" width="40" height="40" x="145" y="85">
                                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate>
                            </rect>
                        </svg>
                    </div>
                @endif
            </span>
        </button>
    @endisset
</div>
