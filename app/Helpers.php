<?php

declare(strict_types=1);

namespace App\Helpers {

    use Exception;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Pluralizer;
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
     * Get the relative representation of a date range
     *
     * @throws InvalidRangeException if the `$endDate` is less than the `$startDate`
     */
    function relativeRangeDate(Carbon $startDate, Carbon $endDate): string
    {
        $start = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        if ($start->greaterThan($end)) {
            throw new InvalidRangeException;
        }

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        if ($years > 0 && $months === 0 && $days === 0) {
            $label = Pluralizer::plural('year', $years);

            return "$years $label";
        }

        if ($years === 0) {
            if ($months <= 3) {
                return 'Less than 3 months';
            }
            if ($months <= 6) {
                return 'Less than 6 months';
            }

            return 'Less than 1 year';
        }

        $yearLabel = Pluralizer::plural('year', $years);

        $monthsBucket = match (true) {
            /**
             * 0-2 months (any extra days) -> 3 months bucket
             * or
             * exactly 3 months -> 3 months, but 3 months with not days
             **/
            $months < 3, $months === 3 && $days === 0 => '3 months',

            /**
             * exactly 3 months -> 3 months, but 3 months + (1+) days -> 6 months
             * or
             * 4-5 months (any extra days) -> 6 months bucket
             * or
             * exactly 6 months -> 6 months, but 6 months + 1+ days -> next year bucket
             **/
            $months === 3 && $days > 0, $months < 6, $months === 6 && $days === 0 => '6 months',

            default => null,
        };

        if ($monthsBucket === null) {
            $nextYears = $years + 1;
            $nextLabel = Pluralizer::plural('year', $nextYears);

            return "Less than $nextYears $nextLabel";
        }

        return "Less than $years $yearLabel and $monthsBucket";
    }

    /**
     * Thrown when the `end date` is less than the `start date`
     */
    class InvalidRangeException extends Exception
    {
        public function __construct()
        {
            parent::__construct('The end date must be greater than the start date');
        }
    }
}
