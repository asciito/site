<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
use App\Models\Concerns\ModelStatus;
use App\Settings\SiteSettings;
use Closure;
use Database\Factories\PostFactory;
use DOMDocument;
use DOMElement;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Override;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

use function App\Helpers\getMediaImageDimensions;

/**
 * @property string $title
 * @property string $slug
 * @property Status $status
 * @property string $content
 * @property string $excerpt
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ?Carbon $published_at
 */
class Post extends Model implements HasMedia, HasRichContent, Sitemapable
{
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;
    use InteractsWithRichContent;
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

    public function getContent(bool $withTorchlight = true): HtmlString
    {
        $rawKey = $this->generateKey('content-raw');

        $raw = $this->rememberPostCache($rawKey, fn () => $this->getRawContent());

        return new HtmlString($raw && $withTorchlight ? $this->applyTorchlight($raw) : $raw);
    }

    protected function applyTorchlight(string $content): string
    {
        return Str::replaceMatches(
            '~<pre>\s*<code(?:\s+class="language-([^"]+)")?>(.*?)</code>\s*</pre>~s',
            fn (array $matches) => view('site::torchlight', [
                'content' => trim((string) $matches[2]),
                'language' => $matches[1] ?? 'text',
            ]),
            $content
        );
    }

    public function getRawContent(): string
    {
        return $this->renderRichContent('content');
    }

    public function getExcerpt(string $end = '...'): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $cacheKey = $this->generateKey('excerpt-'.md5($end));

        if (! config('site.cache.post')) {
            return $this->extractExcerpt($this->getContent(withTorchlight: false)->toHtml(), $end);
        }

        return Cache::rememberForever($cacheKey, fn () => $this->extractExcerpt($this->getContent(withTorchlight: false)->toHtml(), $end));
    }

    protected function extractExcerpt(string $content, string $end = '...'): string
    {
        if (blank($content)) {
            return '';
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $paragraphs = $dom->getElementsByTagName('p');
        $textContent = collect($paragraphs)->reduce(fn (string $text, DOMElement $p) => $text.' '.trim(strip_tags($p->textContent)), '');

        return (string) Str::of($textContent)->trim()->limit(255, $end);
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
        } elseif ($this->updated_at->equalTo($this->published_at)) {
            /** @var Carbon $date */
            $date = $this->published_at;
            $message = $date->isToday() ? 'Published Today' : 'Published on '.$date->format('F d, Y');
        } else {
            /** @var Carbon $date */
            $date = $this->updated_at;

            $message = $date->isToday() ? 'Updated Today' : 'Updated on '.$date->format('F d, Y');
        }

        return $asHtml ? new HtmlString(<<<HTML
        <time datetime="{$date->format('Y-m-d H:i:s')}">
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
            robots: $this->isPublished() ? config('seo.robots.default') : 'noindex, nofollow'
        );
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $dimensions = filled($media) ? getMediaImageDimensions($media) : null;

        $thumb = $this
            ->addMediaConversion('thumb')
            ->width(1280)
            ->height(720);

        $feature = tap(
            $this->addMediaConversion('feature-image'),
            function (Conversion $conversion) {
                $conversion->width(1920)->height(1080);

                $conversion->withResponsiveImages();
            });

        if (filled($dimensions) && ($dimensions[0] > 1920 || $dimensions[1] > 1080)) {
            $thumb->focalCrop(1920, 1080);

            $feature->focalCrop(1920, 1080);
        }
    }

    #[Override]
    public function resolveRouteBinding($value, $field = null): ?self
    {
        return parent::resolveRouteBindingQuery($this, $value, $field)
            /** @phpstan-ignore-next-line */
            ->when(Auth::check(), fn (Builder $query) => $query->withDrafts())
            ->first();
    }

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(route('post', $this))
            ->setLastModificationDate($this->updated_at);
    }

    public function getTableOfContent(bool $withLinks = true, bool $unordered = true): ?HtmlString
    {
        preg_match_all('~<(?<tag>h(?<size>[2-6]))[^>]*>(?<title>(?!\s*</\k<tag>>)[\s\S]*?)</\k<tag>>~', $this->content, $matches);

        if (empty($matches['size']) || empty($matches['title'])) {
            return null;
        }

        $counters = [];
        $baseTemplate = '%s%s '.($withLinks ? '<a href="#%s" class="group"><span class="item-marker">→</span><span>%s</span></a>' : '**%s**');

        $toc = collect($matches['size'])
            ->zip($matches['title'])
            ->map(function (Collection $heading) use ($baseTemplate, &$counters, $unordered) {
                [$size, $title] = $heading;

                $level = (int) $size;

                // Indent by 4 spaces per nesting level (Markdown convention)
                $indent = str_repeat(' ', ($level - 2) * 4);

                if ($unordered) {
                    $marker = '-';
                } else {
                    // Reset deeper levels when we come back up
                    for ($l = $level + 1; $l <= 6; $l++) {
                        unset($counters[$l]);
                    }

                    $counters[$level] = ($counters[$level] ?? 0) + 1;
                    $marker = $counters[$level].'.';
                }

                return sprintf($baseTemplate, $indent, $marker, str(html_entity_decode($title))->stripTags(), $title);
            })->join("\n");

        return $toc ? str($toc)->markdown()->toHtmlString() : null;
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make());
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    /**
     * Generate a unique key for caching rich content attributes.
     *
     * @internal
     */
    protected function rememberPostCache(string $key, Closure $callback): string
    {
        if (! config('site.cache.should_cache')) {
            return (string) $callback();
        }

        $ttl = config('site.cache.ttl');

        if (filled($ttl)) {
            return (string) Cache::remember($key, $ttl, fn () => (string) $callback());
        }

        return (string) Cache::rememberForever($key, fn () => (string) $callback());
    }

    protected function generateKey(string $attribute): string
    {
        $id = $this->getKey() ?? 'new';

        return 'post-'.$id.'-'.$attribute.'-v'.$this->updated_at->timestamp;
    }

    #[Override]
    protected static function boot(): void
    {
        parent::boot();

        // Only warm caches when caching is enabled. Warming is safe in any environment.
        if (! config('site.cache.should_cache')) {
            return;
        }

        static::saved(function (Post $post) {
            $post->getContent(withTorchlight: false);

            $post->getExcerpt();
        });
    }
}
