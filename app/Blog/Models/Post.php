<?php

declare(strict_types=1);

namespace App\Blog\Models;

use App\Blog\Enums\Status;
use App\Blog\HtmlContent;
use App\Site\Models\Concerns\ModelStatus;
use App\Site\Settings\SiteSettings;
use Database\Factories\PostFactory;
use DOMDocument;
use DOMElement;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

    public function getContent(bool $withTorchlight = true): HtmlContent
    {
        return new HtmlContent($this->renderRichContent('content'), $withTorchlight);
    }

    public function getExcerpt(string $end = '...'): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding((string) $this->getContent(false), 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $text = collect($dom->getElementsByTagName('p'))
            ->reduce(fn (string $text, DOMElement $p) => $text.' '.trim(strip_tags($p->textContent)), '');

        return (string) Str::of($text)->trim()->limit(255, $end);
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

    public function getTableOfContent(bool $unordered = true): ?HtmlString
    {
        preg_match_all('/^(?<size>#{2,6})\h+(?<title>.+)$/m', $this->content, $matches);

        if (empty($matches['size']) || empty($matches['title'])) {
            return null;
        }

        $counters = [];

        $toc = collect($matches['size'])
            ->zip($matches['title'])
            ->map(function (Collection $heading) use (&$counters, $unordered) {
                [$size, $title] = $heading;

                $level = strlen($size);

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

                return sprintf(
                    '%s%s [%s](#%s)',
                    $indent,
                    $marker,
                    $title,
                    str($title)->slug(),
                );
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
}
