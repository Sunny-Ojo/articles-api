<?php

namespace App\Jobs\Elasticsearch;

use App\Models\Article;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateArticleIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Article $article) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->article->unsearchable();
            Log::info('Article removed from index successfully', [
                'article_id' => $this->article->id,
                'article_name' => $this->article->title
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to remove article from index', [
                'article_id' => $this->article->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
