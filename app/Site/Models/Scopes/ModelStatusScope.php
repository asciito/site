<?php

namespace App\Site\Models\Scopes;

use App\Blog\Enums\Status;
use App\Site\Models\Concerns\ModelStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ModelStatusScope implements Scope
{
    protected array $extensions = ['WithDrafts', 'WithoutDrafts', 'OnlyDrafts', 'OnlyPublished'];

    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('status', Status::PUBLISHED);
    }

    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    protected function addWithDrafts(Builder $builder): void
    {
        $builder->macro('withDrafts', function (Builder $builder, bool $withDrafts = true): Builder {
            if (! $withDrafts) {
                return $this->withoutDrafts();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    protected function addWithoutDrafts(Builder $builder): void
    {
        $builder->macro('withoutDrafts', function ($builder): Builder {
            $model = $builder->getModel();

            return $builder->withoutGlobalScope($this)->whereNot(
                $model->getQualifiedStatusColumn(),
                Status::DRAFT,
            );
        });
    }

    protected function addOnlyDrafts(Builder $builder): void
    {
        $builder->macro('onlyDrafts', function (Builder $builder): Builder {
            return $this->onlyWithStatus($builder, Status::DRAFT);
        });
    }

    protected function addOnlyPublished(Builder $builder): void
    {
        $builder->macro('onlyPublished', function (Builder $builder): Builder {
            return $this->onlyWithStatus($builder, Status::PUBLISHED);
        });
    }

    protected function onlyWithStatus(Builder $builder, Status $status): Builder
    {
        /** @var Model&ModelStatus $model */
        $model = $builder->getModel();

        return $builder->withoutGlobalScope($this)->where(
            $model->getQualifiedStatusColumn(),
            $status,
        );
    }
}
