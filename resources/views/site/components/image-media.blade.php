@props(['media', 'conversion' => ''])

@php
    /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */

    $src = $media->getPath($conversion);
    $srcset = $media->getSrcset($conversion);
@endphp

<x-image :$src :$srcset {{ $attributes }}/>
