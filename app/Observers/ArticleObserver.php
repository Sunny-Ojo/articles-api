<?php

namespace App\Observers;

use App\Jobs\Elasticsearch\IndexArticle;
use App\Jobs\Elasticsearch\RemoveArticleIndex;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    public function created(Article $article): void
    {
        IndexArticle::dispatch($article);
    }

    public function updated(Article $article): void
    {
        IndexArticle::dispatch($article);
    }

    public function deleted(Article $article): void
    {
        RemoveArticleIndex::dispatch($article->id);
    }

    public function restored(Article $article): void
    {
        IndexArticle::dispatch($article->id);
    }

    public function forceDeleted(Article $article): void
    {
        RemoveArticleIndex::dispatch($article->id);
    }
}
