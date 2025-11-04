<?php

namespace App\Repositories;

use App\Models\Article;
use App\Pipelines\Article\SearchFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ArticleRepository
{
    public function all(): LengthAwarePaginator
    {
        return app(Pipeline::class)
            ->send(Article::query()->published())
            ->through([
                SearchFilter::class,
            ])
            ->thenReturn()
            ->latest()
            ->paginate();
    }

    public function findBySlug(string $slug): ?Article
    {
        return Article::where('slug', $slug)->first();
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function update(Article $article, array $data): Article
    {
        $article->update($data);
        return $article;
    }

    public function delete(Article $article): bool
    {
        return $article->delete();
    }
}
