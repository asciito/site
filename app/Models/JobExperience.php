<?php

namespace App\Models;

use Database\Factories\JobExperienceFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $title
 * @property string $description
 * @property int $order
 * @property array $meta
 * @property bool $working_here
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class JobExperience extends Model
{
    /** @use HasFactory<JobExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function technologies(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meta['technologies'] ?? [],
            set: function (array $technologies): void {
                if (blank($technologies)) {
                    return;
                }

                $this->meta['technologies'] = $technologies;
            }
        );
    }

    public function categories(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meta['categories'] ?? [],
            set: function (array $categories): void {
                if (blank($categories)) {
                    return;
                }

                $this->meta['categories'] = $categories;
            }
        );
    }
}
