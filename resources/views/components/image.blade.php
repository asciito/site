@props(['src', 'srcset' => null])

@php
    if (! \Str::isUrl($src)) {
        [$width, $height] = \App\Helpers\getImageDimensions($src);
    }
@endphp

<img
    src="{{ $src }}"
    @if($srcset) srcset="{{ $srcset }}" @endif
    @isset($width) width="{{ $width }}" @endisset
    @isset($height) height="{{ $height }}" @endisset
    {{ $attributes->class(['w-full h-auto']) }}
/>
