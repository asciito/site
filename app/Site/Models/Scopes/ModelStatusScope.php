<?php

namespace App\Site\Models\Scopes;

use App\Blog\Enums\Status;
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

    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    protected function addWithDrafts(Builder $builder): void
    {
        $builder->macro('withDrafts', function (Builder $builder, bool $withDrafts = true) {
            if (! $withDrafts) {
                return $this->withoutDrafts();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    protected function addWithoutDrafts(Builder $builder): void
    {
        $builder->macro('withoutDrafts', function (Builder $builder) {
            $model = $builder->getModel();

            return $builder->withoutGlobalScope($this)->whereNot(
                $model->getQualifiedStatusColumn(),
                Status::DRAFT,
            );
        });
    }

    protected function addOnlyDrafts(Builder $builder): void
    {
        $builder->macro('onlyDrafts', function (Builder $builder) {
            return $this->onlyWithStatus($builder, Status::DRAFT);
        });
    }

    protected function addOnlyPublished(Builder $builder): void
    {
        $builder->macro('onlyPublished', function (Builder $builder) {
            return $this->onlyWithStatus($builder, Status::PUBLISHED);
        });
    }

    protected function onlyWithStatus(Builder $builder, Status $status): Builder
    {
        $model = $builder->getModel();

        return $builder->withoutGlobalScope($this)->where(
            $model->getQualifiedStatusColumn(),
            $status,
        );
    }
}
