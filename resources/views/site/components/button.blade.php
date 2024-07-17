@props(['href' => null, 'size' => 'md', 'position' => 'left', 'shouldExpand' => false])

@php
    $sizeClasses = match ($size) {
        default => 'px-6 py-2 text-md',
        'sm' => 'px-4 py-2 text-sm',
        'lg' => 'px-8 py-3 text-lg',
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
        'flex justify-between items-center',
        'px-6 py-2 bg-harlequin',
        'cursor-pointer',
        'w-full' => $shouldExpand,
    ]);

@endphp

<div @class(['w-full flex flex-wrap', $positionClass])>
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
            ]) }}
            wire:loading.attr="disabled"
        >
            {{ $slot }}
        </button>
    @endisset
</div>
