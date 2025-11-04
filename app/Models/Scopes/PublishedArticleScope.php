<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PublishedArticleScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder<Model> $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNotNull('published_at');
    }
}
