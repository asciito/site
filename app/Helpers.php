<?php

declare(strict_types=1);

namespace App\Helpers {
    use Illuminate\Support\Facades\Storage;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;


    /**
     * Get the dimensions of an image.
     *
     * @param Media|string $path
     * @return array{0: int, 1: int} Where index 0 is the width and 1 is the height
     */
    function getImageDimensions(Media|string $path): array
    {
        if ($path instanceof Media) {
            $path = $path->getPath();
        }

        $dimensions = getimagesize($path);

        return [
            $dimensions[0],
            $dimensions[1],
        ];
    }
}
