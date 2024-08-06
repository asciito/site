<?php

namespace App\Models\Concerns;

use App\Models\Scopes\ModelStatusScope;
use App\Site\Enums\Status;
use Illuminate\Support\Carbon;

trait ModelStatus
{
    public static function bootModelStatus(): void
    {
        static::softDeleted(function ($model) {
            $model->archive();
        });

        static::restored(function ($model) {
            $model->draft();
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
        return $this->changeStatus(Status::DRAFT);
    }

    public function archive(): bool
    {
        return $this->changeStatus(Status::ARCHIVED);
    }

    public function publish(): bool
    {
        return $this
            ->setPublishedAt($this->freshTimestamp())
            ->changeStatus(Status::PUBLISHED);
    }

    protected function changeStatus(Status $status): bool
    {
        return $this->update([
            $this->getStatusColumn() => $status,
        ]);
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

    protected function hasStatus(Status $status): bool
    {
        return $this->status === $status;
    }

    public function getStatusColumn(): string
    {
        return defined(static::class.'::STATUS_COLUMN') ? static::STATUS_COLUMN : 'status';
    }

    public function setPublishedAt(Carbon $value): static
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
