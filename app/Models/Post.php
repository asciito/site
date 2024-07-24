<?php

namespace App\Models;

use App\Models\Scopes\PostStatusScope;
use App\Site\Enums\PostStatus;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
    use HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'content',
        'excerpt',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
        ];
    }

    public function isDraft(): bool
    {
        return $this->hasStatus(PostStatus::DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->hasStatus(PostStatus::PUBLISHED);
    }

    public function isArchived(): bool
    {
        return $this->hasStatus(PostStatus::ARCHIVED);
    }

    public function hasStatus(PostStatus $status): bool
    {
        return $this->status === $status;
    }

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

    protected static function booted(): void
    {
        if (! Auth::id()) {
            static::addGlobalScope(PostStatusScope::class);
        }
    }

}
