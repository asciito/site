@props(['media', 'conversion' => ''])

@php
    /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */

    $alt = \Illuminate\Support\Str::of($media->name)->slug()->replace('-', ' ')->title();
    [$width, $height] = getimagesize($media->getPath());
    $src = $media->getUrl($conversion);
    $srcset = $media->getSrcset($conversion);
@endphp

<x-image :$src :$srcset :$width :$height :$alt/>
