@props(['src', 'srcset' => null, 'width' => null, 'height' => null])

@php
    if (file_exists(public_path($src))) {
        if (! $height || ! $width) {
            [$width, $height] = getimagesize($src);
        }

        $src = asset($src);
    }
@endphp

<img
    src="{{ $src }}"
    @if($srcset) srcset="{{ $srcset }}" @endif
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    {{ $attributes->class(['w-full h-auto']) }}
/>
