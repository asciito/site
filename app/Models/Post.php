<?php

namespace App\Models;

use App\Models\Concerns\ModelStatus;
use App\Site\HtmlContent;
use App\Site\SiteSettings;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Post extends Model implements HasMedia, Sitemapable
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

    public function getContent(bool $withTorchlight = true): Htmlable
    {
        return new HtmlContent($this->getRawContent(), $withTorchlight);
    }

    public function getRawContent(): string
    {
        if (app()->isProduction()) {
            $key = $this->calculateCacheKey('content', $this->updated_at->timestamp);

            return cache()->rememberForever($key, function () {
                return Markdown::convert($this->content)->getContent();
            });
        }

        return Markdown::convert($this->content)->getContent();
    }

    public function getExcerpt(string $end = '...'): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($this->getContent(false), 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $text = collect($dom->getElementsByTagName('p'))
            ->reduce(fn (string $text, \DOMElement $p) => $text.' '.trim(strip_tags($p->textContent)), '');

        return Str::of($text)->trim()->limit(255, $end);
    }

    public function getDate(bool $asHtml = true): Htmlable|Carbon
    {
        if (! $this->isPublished()) {
            if ($this->created_at->equalTo($this->updated_at)) {
                $date = $this->created_at;

                $message = $date->isToday() ? 'Created Today' : 'Created on '.$date->format('F d, Y');
            } else {
                $date = $this->updated_at;

                $message = $date->isToday() ? 'Updated Today' : 'Updated on '.$date->format('F d, Y');
            }
        } else {
            if ($this->updated_at->equalTo($this->published_at)) {
                $date = $this->published_at;

                $message = $date->isToday() ? 'Published Today' : 'Published on '.$date->format('F d, Y');
            } else {
                $date = $this->updated_at;

                $message = $date->isToday() ? 'Updated Today' : 'Updated on '.$date->format('F d, Y');
            }
        }

        return $asHtml ? new HtmlString(<<<HTML
        <time datetime="{$date->format('Y-m-d')}">
            $message
        </time>
        HTML) : $date;
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title.' | '.(app(SiteSettings::class)->name ?? config('app.name')),
            description: $this->getExcerpt(),
            image: $this->getFirstMedia()?->getUrl(),
            url: route('post', $this),
            enableTitleSuffix: false,
            robots: ! $this->isPublished() ? 'noindex, nofollow' : config('seo.robots.default')
        );
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $dimensions = filled($media) ? \App\Helpers\getMediaImageDimensions($media) : null;

        $thumb = $this
            ->addMediaConversion('thumb')
            ->width(1280)
            ->height(720);

        $feature = $this
            ->addMediaConversion('feature-image')
            ->width(1920)
            ->height(1080)
            ->withResponsiveImages();

        if (filled($dimensions) && ($dimensions[0] > 1920 || $dimensions[1] > 1080)) {
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

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(route('post', $this))
            ->setLastModificationDate($this->updated_at);
    }

    public static function calculateCacheKeyUsing(?callable $callback = null): ?callable
    {
        return $callback;
    }

    public function calculateCacheKey(string ...$key): string
    {
        $calculateCacheKeyUsing = static::calculateCacheKeyUsing();

        return (
            $calculateCacheKeyUsing
                ? $calculateCacheKeyUsing()
                : function (self $post, ...$key): string {
                    return class_basename($post).'::'.$post->id.'.'.Arr::join($key, '.');
                }
        )($this, ...$key);
    }

    protected static function booted(): void
    {
        if (app()->isProduction()) {
            static::updating(function (Post $post) {
                $oldKey = $post->calculateCacheKey('content', $post->getOriginal('updated_at')->timestamp);

                cache()->forget($oldKey);
            });

            static::deleted(function (Post $post) {
                $key = $post->calculateCacheKey('content', $post->updated_at->timestamp);

                cache()->forget($key);
            });

            static::restored(function (Post $post) {
                $key = $post->calculateCacheKey('content', $post->updated_at->timestamp);

                cache()->forget($key);
            });
        }
    }
}
