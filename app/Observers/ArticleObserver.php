<?php

namespace App\Observers;

use App\Jobs\Elasticsearch\IndexArticle;
use App\Jobs\Elasticsearch\RemoveArticleIndex;
use App\Models\Article;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        IndexArticle::dispatch($article);
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        IndexArticle::dispatch($article);
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        RemoveArticleIndex::dispatch($article);
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        IndexArticle::dispatch($article);
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        RemoveArticleIndex::dispatch($article);
    }
}
