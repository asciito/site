<?php

namespace App\Models;

use App\Models\Concerns\ModelStatus;
use App\Site\HtmlContent;
use Illuminate\Contracts\Support\Htmlable;
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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;
    use ModelStatus;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'content',
        'excerpt',
        'published_at',
    ];

    public function getContent(): Htmlable
    {
        return new HtmlContent($this->content);
    }

    public function getExcerpt(string $end = '...'): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $excerpt = Str::of($this->content)->matchAll('/<p>(.*?)<\/p>/');
        $excerpt = trim($excerpt->join(' '));

        return Str::limit(strip_tags($excerpt), 255, $end);
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->getExcerpt(),
            image: $this->getFirstMedia()?->getUrl(),
            url: route('post', $this),
            enableTitleSuffix: false,
            robots: ! $this->isPublished() ? 'noindex, nofollow' : config('seo.robots.default')
        );
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        [$width, $height] = getimagesize($media->getPath());

        $thumb = $this
            ->addMediaConversion('thumb')
            ->width(1280)
            ->height(720);

        $feature = $this
            ->addMediaConversion('feature-image')
            ->width(1920)
            ->height(1080)
            ->withResponsiveImages();

        if ($width > 1920 || $height > 1080) {
            $thumb->focalCrop(1920, 1080);

            $feature->focalCrop(1920, 1080);
        }
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return parent::resolveRouteBindingQuery($this, $value, $field)
            ->when(Auth::check(), fn (Builder $query) => $query->withDrafts())
            ->first();
    }
}
