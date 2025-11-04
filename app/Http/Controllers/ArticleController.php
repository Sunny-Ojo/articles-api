<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $service
    ) {}

    public function index()
    {
        $data = ArticleResource::collection($this->service->list());
        return ApiResponse::success($data);
    }

    public function show(string $slug)
    {
        $article = $this->service->show($slug);

        if (! $article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        return ApiResponse::success(new ArticleResource($article));
    }

    public function store(StoreArticleRequest $request)
    {
        $article = $this->service->store($request->validated());
        return ApiResponse::success(new ArticleResource($article));
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $updated = $this->service->update($article, $request->validated());
        return  ApiResponse::success(new ArticleResource($updated));
    }

    public function destroy(Article $article)
    {
        $this->service->delete($article);
        return ApiResponse::success(null, 'Article deleted successfully');
    }
}
