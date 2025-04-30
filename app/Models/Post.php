<?php

namespace App\Models;

use App\Models\Concerns\ModelStatus;
use App\Site\HtmlContent;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        if (empty($key = $this->getContentCacheKey())) {
            return Markdown::convert($this->content)->getContent();
        }

        return cache()->rememberForever(
            $key,
            fn () => Markdown::convert($this->content)->getContent(),
        );
    }

    public function getContentCacheKey(): ?string
    {
        if (empty($key = $this->getCacheKey())) {
            return null;
        }

        $timestamp = $this->updated_at->timestamp;
        $dynamic_key = cache()->get($key);

        if ($dynamic_key &&
            ((int) \Illuminate\Support\Str::afterLast($dynamic_key, '.') !== $timestamp)
        ) {
            $dynamic_key = "{$key}.{$timestamp}";

            $this->updateContentCacheKey($key, $dynamic_key);
        } else {
            $dynamic_key = "{$key}.{$timestamp}";

            cache()->add($key, $dynamic_key);
        }

        return $dynamic_key;
    }

    public function updateContentCacheKey(string $key, string $value): void
    {
        $stored_key = cache($key);

        tap(cache())
            ->forget($key)
            ->forget($stored_key);

        cache()->add($key, $value);
    }

    public function getCacheKey(): ?string
    {
        if (empty($this->id)) {
            return null;
        }

        return "post.{$this->id}";
    }

    public function getExcerpt(string $end = '...'): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $dom = new \DOMDocument();
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

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(route('post', $this))
            ->setLastModificationDate($this->updated_at);
    }
}
