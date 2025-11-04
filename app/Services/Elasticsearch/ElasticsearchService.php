<?php

namespace App\Services\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Article;

class ElasticSearchService
{
    public function __construct(protected Client $client) {}

    /**
     * Index or update a model in Elasticsearch.
     */
    public function indexArticle(Article $article): void
    {
        try {
            $data = $this->transformArticle($article);

            $this->client->index([
                'index' => 'articles',
                'id'    => $article->id,
                'body'  => $data,
            ]);

            Log::info("Indexed article [{$article->id}] successfully.");
        } catch (\Throwable $e) {
            Log::error("Failed to index article [{$article->id}]: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Remove a model from Elasticsearch.
     */
    public function deleteArticle(int $id): void
    {
        try {
            if (! $this->exists('articles', $id)) {
                Log::warning("Article {$id} not found in Elasticsearch; skipping delete.");
                return;
            }

            $this->client->delete([
                'index' => 'articles',
                'id'    => $id,
            ]);

            Log::info("Removed article [{$id}] from Elasticsearch.");
        } catch (\Throwable $e) {
            Log::error("Failed to delete article [{$id}]: {$e->getMessage()}");
        }
    }

    /**
     * Check if a document exists in the index.
     */
    public function exists(string $index, int $id): bool
    {
        try {
            return $this->client->exists([
                'index' => $index,
                'id'    => $id,
            ])->asBool();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Transform an article into an Elasticsearch-acceptable array.
     */
    protected function transformArticle(Article $article): array
    {
        $data = $article->toArray();

        $data['published_at'] = $this->formatDate($article->published_at);
        $data['created_at']   = $this->formatDate($article->created_at);
        return $data;
    }

    /**
     * Convert Carbon or date string to ISO8601 for Elasticsearch.
     */
    protected function formatDate($value): ?string
    {
        if (empty($value)) return null;

        return Carbon::parse($value)->toIso8601String();
    }
}
