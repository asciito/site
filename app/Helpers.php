<?php

declare(strict_types=1);

namespace App\Helpers {
    use Illuminate\Support\Facades\Storage;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;


    /**
     * Get the dimensions of an image.
     *
     * @param Media|string $path
     * @return ?array{0: int, 1: int} Where index 0 is the width and 1 is the height, or null if the image cannot be found.
     */
    function getMediaImageDimensions(Media|string $path): ?array
    {
        if ($path instanceof Media) {
            $content = stream_get_contents($path->stream()) ?: null;
        } else {
            $content = Storage::get($path);
        }

        if ($content === null) {
            return null;
        }

        $dimensions = getimagesizefromstring($content);

        return [
            $dimensions[0],
            $dimensions[1],
        ];
    }
}
