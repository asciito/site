@props(['src'])

@php
    $dimensions = \App\Helpers\getMediaImageDimensions($src);

    if (filled($dimensions)) {
        $width = $dimensions[0];
        $height = $dimensions[1];
    }

    $src = $src instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media
        ? $src->getUrl()
        : $src;
@endphp

<img
    src="{{ $src }}"
    @isset($width) width="{{ $width }}" @endisset
    @isset($height) height="{{ $height }}" @endisset
    {{ $attributes->class(['w-full h-auto']) }}
/>
