@props([
    'size' => 'md',
    'position' => 'left',
    'shouldExpand' => false,
    'showLoadingIndicator' => false,
])

@php
    $type = $attributes->has('href') ? 'a' : 'button';

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
        'text-slate-800 bg-harlequin cursor-pointer',
        'disabled:opacity-80 disabled:cursor-not-allowed hover:bg-harlequin-600 active:bg-harlequin-400',
        'flex justify-center leading-none',
        'w-full' => $shouldExpand,
    ]);

@endphp

<div @class(['w-full flex flex-wrap', $positionClass])>
    <{{ $type }} {{ $attributes }}>
        <span class="flex items-center relative"])>
            <span>{{ $slot }}</span>

            @if ($showLoadingIndicator)
                <div wire:loading.flex class="absolute -right-8">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                        <rect class="fill-slate-800 stroke-slate-800" stroke-width="4" width="40" height="40" x="25" y="85">
                            <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate>
                        </rect>
                        <rect class="fill-slate-800 stroke-slate-800" stroke-width="4" width="40" height="40" x="85" y="85">
                            <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate>
                        </rect>
                        <rect class="fill-slate-800 stroke-slate-800" stroke-width="4" width="40" height="40" x="145" y="85">
                            <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate>
                        </rect>
                    </svg>
                </div>
            @endif
        </span>
    </{{ $type }}>
</div>
