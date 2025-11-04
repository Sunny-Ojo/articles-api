<?php

namespace App\Jobs\Elasticsearch;

use App\Services\Elasticsearch\ElasticsearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveArticleIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $articleId;

    public function __construct(int $articleId)
    {
        $this->articleId = $articleId;
    }

    public function handle(ElasticsearchService $elastic): void
    {
        $elastic->deleteArticle($this->articleId);
    }
}
