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
        'text-slate-800',
        'flex justify-between items-center',
        'px-6 py-2 bg-harlequin enabled:hover:bg-harlequin-600 enabled:active:bg-harlequin-400 cursor-pointer',
        'w-full' => $shouldExpand,
    ]);
@endphp

<div @class(['w-full flex flex-wrap', $positionClass])>
    @empty($href)
        <button {{ $attributes }}>
            {{ $slot }}
        </button>
    @else
        <a href="{{ $href }}" {{ $attributes }}>
            {{ $slot }}
        </a>
    @endisset
</div>
