<?php

namespace App\Pipelines\Article;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class SearchFilter
{
    /**
     * @param Builder<Article> $query
     * @param Closure $next
     * @return Builder<Article>
     */
    public function handle(Builder $query, Closure $next): Builder
    {
        $searchTerm = request('search');

        if (filled($searchTerm)) {
            $articleIds = Article::search($searchTerm)->keys();
            if ($articleIds->isNotEmpty()) {
                $query->whereIn('id', $articleIds);
            } else {
                // empty result
                $query->whereRaw('1 = 0');
            }
        }

        return $next($query);
    }
}
