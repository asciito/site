<?php

namespace App\Models;

use App\Concerns\HasCategories;
use Database\Factories\JobExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Override;

/**
 * @property string $title The job title (position)
 * @property ?string $description The thing you do in that job
 * @property ?int $order The order in which should be display
 * @property ?array $meta Embedded information
 * @property bool $working_here If still working in this position
 * @property ?Carbon $start_date The start date
 * @property ?Carbon $end_date The end date
 * @property array $technologies The technologies you use (or used) in this position
 */
class JobExperience extends Model
{
    /** @use HasFactory<JobExperienceFactory> */
    use HasCategories, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'working_here',
        'start_date',
        'end_date',
        'date_range_as_relative',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Override]
    protected static function boot(): void
    {
        parent::boot();

        $currentJobHandling = static function (self $recent): void {
            if (! $recent->working_here || ($recent->exists && ! $recent->isDirty('working_here'))) {
                return;
            }

            DB::transaction(function () use ($recent): void {
                $query = static::query()->whereWorkingHere(true);

                if ($recent->getKey()) {
                    $query->whereKeyNot($recent->getKey());
                }

                $old = $query->first();

                if (! $old) {
                    return;
                }

                $old->updateQuietly(['working_here' => false]);
            });
        };

        static::creating($currentJobHandling);
        static::updating($currentJobHandling);
    }
}
