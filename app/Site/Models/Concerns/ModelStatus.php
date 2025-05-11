<?php

namespace App\Site\Models\Concerns;

use App\Blog\Enums\Status;
use App\Site\Models\Scopes\ModelStatusScope;
use Illuminate\Support\Carbon;

trait ModelStatus
{
    public static function bootModelStatus(): void
    {
        static::restoring(function ($model) {
            $model->setStatus(Status::DRAFT);
        });

        static::deleted(function ($model) {
            $model->changeStatus(Status::ARCHIVED);
        });

        static::addGlobalScope(ModelStatusScope::class);
    }

    public function initializeModelStatus(): void
    {
        if (! isset($this->casts[$this->getStatusColumn()])) {
            $this->casts = array_merge($this->casts, [
                $this->getStatusColumn() => Status::class,
                $this->getPublishedAtColumn() => 'datetime',
            ]);
        }
    }

    public function draft(): bool
    {
        return $this
            ->setPublishedAt(null)
            ->changeStatus(Status::DRAFT);
    }

    public function archive(): bool
    {
        return $this->delete();
    }

    public function publish(): bool
    {
        return $this
            ->setPublishedAt($this->freshTimestamp())
            ->changeStatus(Status::PUBLISHED);
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    protected function changeStatus(Status $status): bool
    {
        return $this->setStatus($status)->update();
    }

    public function isDraft(): bool
    {
        return $this->hasStatus(Status::DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->hasStatus(Status::PUBLISHED);
    }

    public function isArchived(): bool
    {
        return $this->hasStatus(Status::ARCHIVED);
    }

    public function hasStatus(Status $status): bool
    {
        return $this->status === $status;
    }

    public function getStatusColumn(): string
    {
        return defined(static::class.'::STATUS_COLUMN') ? static::STATUS_COLUMN : 'status';
    }

    public function setPublishedAt(?Carbon $value): static
    {
        $this->{$this->getPublishedAtColumn()} = $value;

        return $this;
    }

    public function getPublishedAtColumn(): string
    {
        return defined(static::class.'::PUBLISHED_AT_COLUMN') ? static::PUBLISHED_AT_COLUMN : 'published_at';
    }

    public function getQualifiedStatusColumn(): string
    {
        return $this->qualifyColumn($this->getStatusColumn());
    }

    public function getQualifiedPublishedAtColumn(): string
    {
        return $this->qualifyColumn($this->getPublishedAtColumn());
    }
}
