<?php

namespace App\Services;

use App\Contracts\SearchEngineInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;

class ElasticsearchService implements SearchEngineInterface
{
    public function index(Model $model): bool
    {
        if (!method_exists($model, 'searchable')) {
            return false;
        }

        try {
            $model->searchable();
            Log::info('Model indexed successfully', [
                'model' => get_class($model),
                'id' => $model->getKey()
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to index model', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function remove(Model $model): bool
    {
        if (!method_exists($model, 'unsearchable')) {
            return false;
        }

        try {
            $model->unsearchable();
            Log::info('Model removed from index successfully', [
                'model' => get_class($model),
                'id' => $model->getKey()
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove model from index', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * @param array<string, mixed> $query
     * @return Collection<int, Model>
     */
    public function search(string $index, array $query): Collection
    {
        try {
            if ($index === 'products') {
                $searchQuery = $query['query'] ?? '';
                if (is_string($searchQuery) && !empty($searchQuery)) {
                    /** @var Collection<int, Model> $results */
                    $results = Product::search($searchQuery)->get();
                    return $results;
                }
            }
            
            /** @var Collection<int, Model> $empty */
            $empty = new Collection();
            return $empty;
        } catch (\Exception $e) {
            Log::error('Search failed', [
                'index' => $index,
                'query' => $query,
                'error' => $e->getMessage()
            ]);
            /** @var Collection<int, Model> $empty */
            $empty = new Collection();
            return $empty;
        }
    }

    /**
     * @param array<string, mixed> $mapping
     */
    public function createIndex(string $index, array $mapping): bool
    {
        // For Scout, indices are managed automatically
        return true;
    }

    public function deleteIndex(string $index): bool
    {
        // For Scout, indices are managed automatically  
        return true;
    }

    public function indexExists(string $index): bool
    {
        // For Scout, indices are managed automatically
        return true;
    }
}