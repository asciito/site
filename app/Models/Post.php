<?php

namespace App\Models;

use App\Models\Concerns\ModelStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;
    use ModelStatus;
    use HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'content',
        'excerpt',
    ];

    public function getExcerpt(): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $excerpt = Str::of($this->content)->matchAll('/<p>(.*?)<\/p>/');
        $excerpt = trim($excerpt->join(' '));

        return Str::limit(strip_tags($excerpt), 255, '');
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->getExcerpt(),
            image: $this->getFirstMedia()?->getUrl(),
            url: route('post', $this),
            enableTitleSuffix: false,
        );
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return parent::resolveRouteBindingQuery($this, $value, $field)
            ->when(Auth::check(), fn (Builder $query) => $query->withDrafts())
            ->first();
    }
}
