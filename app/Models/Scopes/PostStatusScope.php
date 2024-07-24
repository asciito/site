<?php

namespace App\Models\Scopes;

use App\Site\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PostStatusScope implements Scope
{
    protected array $extensions = ['WithDrafts'];

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereStatus(PostStatus::PUBLISHED);
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(fn (Builder $builder) =>
            $builder->update([
                'status' => PostStatus::DRAFT,
            ])
        );
    }

    protected function addWithDrafts(Builder $builder): void
    {
        $builder->macro('withDrafts', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
