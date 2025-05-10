<?php

declare(strict_types=1);

namespace App\Helpers {

    use App\Settings\Settings;
    use Illuminate\Support\Facades\Storage;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;

    /**
     * Get the dimensions of an image.
     *
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

    /**
     * Get the application setting.
     *
     * This function is a wrapper around the AppSettings class to provide a convenient way to access application settings.
     */
    function app_settings(?string $key = null, $default = null): mixed
    {
        $settings = app()->make(\App\AppSettings::class);

        if (func_num_args() === 0) {
            return $settings;
        }

        return $settings->get($key, $default);
    }
}
